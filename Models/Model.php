<?php

class Model
{
    /**
     * Attribut contenant l'instance PDO
     */
    private $bd;

    /**
     * Attribut statique qui contiendra l'unique instance de Model
     */
    private static $instance = null;

    /**
     * Constructeur : effectue la connexion à la base de données.
     */
    private function __construct()
    {
        include "credentials.php";//ATTENTION !! le credentials.php actuel ne fonctionne que sur windows en LOCALHOST. 
        $this->bd = new PDO($dsn, $login, $mdp);
        $this->bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->bd->query("SET nameS 'utf8'");
    }

    /**
     * Méthode permettant de récupérer un modèle car le constructeur est privé (Implémentation du Design Pattern Singleton)
     */
    public static function getModel()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * Méthode pour récupérer les informations d'un utilisateur par son ID
     *
     * @param int $userId L'ID de l'utilisateur à récupérer
     * @return array|false Un tableau contenant les informations de l'utilisateur ou false si l'utilisateur n'est pas trouvé
     */
    public function getUserById($userId)
    {
        try {
            // Utiliser une requête préparée pour éviter les injections SQL
            $query = $this->bd->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = :userId");
            $query->bindParam(':userId', $userId, PDO::PARAM_INT);
            $query->execute();

            // Récupérer les données de l'utilisateur
            $user = $query->fetch(PDO::FETCH_ASSOC);

            // Retourner les informations de l'utilisateur s'il est trouvé, sinon false
            return $user ? $user : false;
        } catch (PDOException $e) {
            // Gérer les erreurs de la base de données
            // Vous pouvez ajuster cela en fonction de vos besoins
            echo "Erreur de base de données: " . $e->getMessage();
            return false;
        }
    }
    
    /**
     * Méthode pour récupérer les sites classés par ordre alphabétique de la colonne "annee" de la table "eleve"
     *
     * @return array|false Un tableau contenant les informations des sites ou false en cas d'erreur
     */
    public function getSitesOrderedByAnnee()
    {
        try {
            // Utiliser une requête SQL pour récupérer les sites classés par ordre alphabétique de la colonne "annee"
            $query = $this->bd->query("SELECT * FROM site s INNER JOIN eleve e ON s.id_utilisateur = e.id_utilisateur ORDER BY e.annee ASC");
            $query->execute();

            // Récupérer les données des sites
            $sites = $query->fetchAll(PDO::FETCH_ASSOC);

            // Retourner les informations des sites
            return $sites;
        } catch (PDOException $e) {
            // Gérer les erreurs de la base de données
            // Vous pouvez ajuster cela en fonction de vos besoins
            echo "Erreur de base de données: " . $e->getMessage();
            return false;
        }
    }  


    /**
     * Méthode pour s'inscrire en tant qu'élève
     *
     * @param string $nom Nom de l'utilisateur
     * @param string $prenom Prénom de l'utilisateur
     * @param string $password Mot de passe de l'utilisateur
     * @param string $mail Adresse email de l'utilisateur
     * @param string $token Token de l'utilisateur
     * @param string $photo_de_profil Nom du fichier photo de profil
     * @param int $annee Année d'étude de l'élève
     * @return bool Retourne true en cas de succès, false en cas d'erreur
     */
    public function registerStudent($nom, $prenom, $password, $mail, $token, $photo_de_profil, $annee)
    {
        try {

            $token = bin2hex(random_bytes(128)); // 256 characters
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $photo_de_profil='default.png';
            
            // Commencer une transaction
            $this->bd->beginTransaction();

            // Insérer l'utilisateur dans la table utilisateur
            $queryUser = $this->bd->prepare("INSERT INTO utilisateur (nom, prenom, password, mail, token, photo_de_profil, date_de_creation, mail_verifier)
                                            VALUES (:nom, :prenom, :password, :mail, :token, :photo_de_profil, NOW(), 0)");
            $queryUser->bindParam(':nom', $nom, PDO::PARAM_STR);
            $queryUser->bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $queryUser->bindParam(':password', $password, PDO::PARAM_STR);
            $queryUser->bindParam(':mail', $mail, PDO::PARAM_STR);
            $queryUser->bindParam(':token', $token, PDO::PARAM_STR);
            $queryUser->bindParam(':photo_de_profil', $photo_de_profil, PDO::PARAM_STR);
            $queryUser->execute();

            // Récupérer l'ID de l'utilisateur nouvellement inscrit
            $userId = $this->bd->lastInsertId();

            // Insérer l'élève dans la table eleve
            $queryEleve = $this->bd->prepare("INSERT INTO eleve (id_utilisateur, annee) VALUES (:id_utilisateur, :annee)");
            $queryEleve->bindParam(':id_utilisateur', $userId, PDO::PARAM_INT);
            $queryEleve->bindParam(':annee', $annee, PDO::PARAM_INT);
            $queryEleve->execute();

            // Valider la transaction
            $this->bd->commit();

            return true; // L'inscription a réussi
        } catch (PDOException $e) {
            // Annuler la transaction en cas d'erreur
            $this->bd->rollBack();

            // Gérer les erreurs de la base de données
            // Vous pouvez ajuster cela en fonction de vos besoins
            echo "Erreur de base de données: " . $e->getMessage();
            return false; // L'inscription a échoué
        }
    }


    /**
     * Méthode pour authentifier un utilisateur par email et mot de passe
     *
     * @param string $mail Adresse email de l'utilisateur
     * @param string $password Mot de passe de l'utilisateur
     * @return array|false Un tableau contenant les informations de l'utilisateur en cas de succès, false en cas d'échec
     */
    public function authenticateUser($mail, $password)
    {
        try {
            // Requête pour récupérer les informations de l'utilisateur par email
            $query = $this->bd->prepare("SELECT * FROM utilisateur WHERE mail = :mail");
            $query->bindParam(':mail', $mail, PDO::PARAM_STR);
            $query->execute();

            // Récupérer les données de l'utilisateur
            $user = $query->fetch(PDO::FETCH_ASSOC);

            // Vérifier si l'utilisateur existe et si le mot de passe est correct
            if ($user && password_verify($password, $user['password'])) {
                // Retourner les informations de l'utilisateur s'il est authentifié
                return $user;
            } else {
                // L'authentification a échoué
                return false;
            }
        } catch (PDOException $e) {
            // Gérer les erreurs de la base de données
            // Vous pouvez ajuster cela en fonction de vos besoins
            echo "Erreur de base de données: " . $e->getMessage();
            return false;
        }
    }

    
    /**
     * Méthode pour récupérer les informations d'un utilisateur par son token
     *
     * @param string $token Token de l'utilisateur
     * @return array|false Un tableau contenant les informations de l'utilisateur en cas de succès, false en cas d'échec
     */
    public function getUserByToken($token)
    {
        try {
            // Requête pour récupérer les informations de l'utilisateur par token
            $query = $this->bd->prepare("SELECT * FROM utilisateur WHERE token = :token");
            $query->bindParam(':token', $token, PDO::PARAM_STR);
            $query->execute();

            // Récupérer les données de l'utilisateur
            $user = $query->fetch(PDO::FETCH_ASSOC);

            // Retourner les informations de l'utilisateur s'il est trouvé, sinon false
            return $user ? $user : false;
        } catch (PDOException $e) {
            // Gérer les erreurs de la base de données
            // Vous pouvez ajuster cela en fonction de vos besoins
            echo "Erreur de base de données: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Méthode pour ajouter une ligne dans la table github
     *
     * @param string $lien Lien GitHub à ajouter
     * @param bool $verifier Statut de vérification (true si vérifié, false sinon)
     * @return bool Retourne true en cas de succès, false en cas d'échec
     */
    public function addGithubLink($lien, $verifier = false)
    {
        try {
            // Requête pour insérer une ligne dans la table github
            $query = $this->bd->prepare("INSERT INTO github (lien, verifier, date_du_github, id_utilisateur)
                                         VALUES (:lien, :verifier, NOW(), :id_utilisateur)");

            // Vous devez définir id_utilisateur en fonction de votre logique d'application
            $id_utilisateur = 1; // Remplacez par la logique appropriée pour obtenir l'ID de l'utilisateur

            $query->bindParam(':lien', $lien, PDO::PARAM_STR);
            $query->bindParam(':verifier', $verifier, PDO::PARAM_BOOL);
            $query->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);

            $query->execute();

            return true; // L'ajout a réussi
        } catch (PDOException $e) {
            // Gérer les erreurs de la base de données
            // Vous pouvez ajuster cela en fonction de vos besoins
            echo "Erreur de base de données: " . $e->getMessage();
            return false; // L'ajout a échoué
        }
    }

    /**
     * Méthode pour fusionner les informations des tables github et utilisateur pour tous les utilisateurs, ordonnés par date la plus récente
     *
     * @return array|false Un tableau contenant les informations fusionnées pour tous les utilisateurs en cas de succès, false en cas d'échec
     */
    public function getAllGitHubUserInfoOrderByDate()
    {
        try {
            // Requête pour fusionner les informations des tables github et utilisateur pour tous les utilisateurs, ordonnés par date la plus récente
            $query = $this->bd->query("SELECT u.*, g.lien AS github_lien, g.verifier AS github_verifier, g.date_du_github
                                       FROM utilisateur u
                                       LEFT JOIN github g ON u.id_utilisateur = g.id_utilisateur
                                       ORDER BY g.date_du_github DESC");

            // Récupérer toutes les données fusionnées
            $mergedData = $query->fetchAll(PDO::FETCH_ASSOC);

            // Retourner les données fusionnées s'il y a des résultats, sinon false
            return $mergedData ? $mergedData : false;
        } catch (PDOException $e) {
            // Gérer les erreurs de la base de données
            // Vous pouvez ajuster cela en fonction de vos besoins
            echo "Erreur de base de données: " . $e->getMessage();
            return false;
        }
    }
}