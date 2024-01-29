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
    public function registerStudent($nom, $prenom, $password, $mail, $annee)
    {
        try {

            $token = bin2hex(random_bytes(128)); // 256 characters
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $photo_de_profil='default.png';
            
            // Commencer une transaction
            $this->bd->beginTransaction();

            // Insérer l'utilisateur dans la table utilisateur
            $queryUser = $this->bd->prepare("INSERT INTO utilisateur (nom, prenom, password, mail, token, photo_de_profil, date_de_creation, mail_verifier)
                                            VALUES (:nom, :prenom, :password, :mail, :token, :photo_de_profil, NOW(), FALSE)");
            $queryUser->bindParam(':nom', $nom, PDO::PARAM_STR);
            $queryUser->bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $queryUser->bindParam(':password', $hashedPassword);
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
     * Connection utilisateur
     * 
     * @param string $mail mail de l'utilisateur
     * @param string $password mail de l'utilisateur
     * @return Bool
     */
    public function getUserByCredentials($mail, $password)
    {
        try {
            // Prepare
            $stmt = $this->bd->prepare("SELECT * FROM utilisateur WHERE mail = :mail");

            // Bind
            $stmt->bindParam(':mail', $mail);

            // Execute
            $stmt->execute();

            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            
            $stmt->closeCursor();

            // Vérifiez le mot de passe si l'utilisateur est trouvé
            if ($user && password_verify($password, $user['password'])) {

                //unset($user['password']); // Supprimez le mot de passe haché du résultat pour des raisons de sécurité => COMMENTé POUR LE MOMMENT
                return $user;
            } else {
                // Soit l'utilisateur est introuvable, soit le mot de passe est incorrect
                return null;
            }
        } catch (PDOException $e) {
            return null;
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

    /**
     * Méthode pour récupérer le token d'un utilisateur par son email
     *
     * @param string $email L'email de l'utilisateur
     * @return string|false Le token de l'utilisateur en cas de succès, false en cas d'échec
     */
    public function getTokenUtilisateurByEmail($email)
    {
        try {
            // Requête pour récupérer le token de l'utilisateur par email
            $query = $this->bd->prepare("SELECT token FROM utilisateur WHERE mail = :email");
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();

            // Récupérer le token de l'utilisateur s'il est trouvé
            $token = $query->fetchColumn();

            // Retourner le token de l'utilisateur s'il est trouvé, sinon false
            return $token ? $token : false;
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
     * @param string $token Le token de l'utilisateur
     * @return array|false Un tableau contenant les informations de l'utilisateur en cas de succès, false en cas d'échec
     */
    public function getUserInfoByToken($token)
    {
        try {
            // Requête pour récupérer les informations de l'utilisateur par token
            $query = $this->bd->prepare("SELECT nom, prenom, mail FROM utilisateur WHERE token = :token");
            $query->bindParam(':token', $token, PDO::PARAM_STR);
            $query->execute();

            // Récupérer les données de l'utilisateur
            $userInfo = $query->fetch(PDO::FETCH_ASSOC);

            // Retourner les informations de l'utilisateur s'il est trouvé, sinon false
            return $userInfo ? $userInfo : false;
        } catch (PDOException $e) {
            // Gérer les erreurs de la base de données
            // Vous pouvez ajuster cela en fonction de vos besoins
            echo "Erreur de base de données: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Méthode pour obtenir tous les sites avec les noms et prénoms des utilisateurs associés
     *
     * @return array|false Un tableau contenant les informations des sites et des utilisateurs associés en cas de succès, false en cas d'échec
     */
    public function getAllSitesWithUsers()
    {
        try {
            // Requête pour obtenir tous les sites avec les noms et prénoms des utilisateurs associés
            $query = $this->bd->prepare("SELECT s.*, u.nom AS nom_utilisateur, u.prenom AS prenom_utilisateur FROM site s JOIN utilisateur u ON s.id_utilisateur = u.id_utilisateur");
            $query->execute();

            // Récupérer les données des sites et des utilisateurs associés
            $sites = $query->fetchAll(PDO::FETCH_ASSOC);

            // Retourner les données des sites et des utilisateurs associés
            return $sites;
        } catch (PDOException $e) {
            // Gérer les erreurs de la base de données
            // Vous pouvez ajuster cela en fonction de vos besoins
            echo "Erreur de base de données: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Méthode pour ajouter un lien GitHub avec l'ID de l'utilisateur associé
     *
     * @param int $userId L'ID de l'utilisateur associé au lien GitHub
     * @param string $lienGithub Le lien GitHub à ajouter
     * @return bool Retourne true en cas de succès, false en cas d'échec
     */
    public function addGithubLink($userId, $lienGithub)
    {
        try {
            // Requête pour ajouter un lien GitHub avec l'ID de l'utilisateur associé
            $query = $this->bd->prepare("INSERT INTO github (lien, verifier, date_du_github, id_utilisateur) VALUES (:lienGithub, FALSE, NOW(), :userId)");
            $query->bindParam(':lienGithub', $lienGithub, PDO::PARAM_STR);
            $query->bindParam(':userId', $userId, PDO::PARAM_INT);
            $query->execute();

            // Vérifier si la requête d'insertion a réussi
            if ($query->rowCount() > 0) {
                return true; // Succès : le lien GitHub a été ajouté avec succès
            } else {
                return false; // Échec : aucun enregistrement ajouté
            }
        } catch (PDOException $e) {
            // Gérer les erreurs de la base de données
            // Vous pouvez ajuster cela en fonction de vos besoins
            echo "Erreur de base de données: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Méthode pour vérifier si un utilisateur est modérateur en fonction de son token
     *
     * @param string $token Le token de l'utilisateur à vérifier
     * @return bool Retourne true si l'utilisateur est modérateur, false sinon
     */
    public function isAdmin($token)
    {
        try {
            // Requête pour vérifier si l'utilisateur est modérateur en fonction de son token
            $query = $this->bd->prepare("SELECT COUNT(*) FROM moderateur m JOIN utilisateur u ON m.id_utilisateur = u.id_utilisateur WHERE u.token = :token");
            $query->bindParam(':token', $token, PDO::PARAM_STR);
            $query->execute();

            // Récupérer le résultat de la requête
            $count = $query->fetchColumn();

            // Vérifier si l'utilisateur est modérateur
            if($count > 0){
                return true;
            } else {
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
     * Méthode pour récupérer tous les liens de la table github qui ont false en verifier
     *
     * @return array|false Un tableau contenant les liens GitHub en cas de succès, false en cas d'échec
     */
    public function getUncheckedGithubLinks()
    {
        try {
            // Requête pour récupérer tous les liens GitHub avec "false" en verifier
            $query = $this->bd->prepare("SELECT g.*, u.nom AS nom_utilisateur, u.prenom AS prenom_utilisateur FROM github g JOIN utilisateur u ON g.id_utilisateur = u.id_utilisateur WHERE g.verifier = FALSE");
            $query->execute();

            // Récupérer les données des liens GitHub
            $uncheckedLinks = $query->fetchAll(PDO::FETCH_ASSOC);

            // Retourner les données des liens GitHub
            return $uncheckedLinks;
        } catch (PDOException $e) {
            // Gérer les erreurs de la base de données
            // Vous pouvez ajuster cela en fonction de vos besoins
            echo "Erreur de base de données: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Méthode pour supprimer une ligne dans la table github en utilisant l'ID
     *
     * @param int $githubId L'ID du lien GitHub à supprimer
     * @return bool Retourne true en cas de succès, false en cas d'échec
     */
    public function deleteGithubLink($githubId)
    {
        try {
            // Requête pour supprimer le lien GitHub avec l'ID spécifié
            $query = $this->bd->prepare("DELETE FROM github WHERE id_github = :githubId");
            $query->bindParam(':githubId', $githubId, PDO::PARAM_INT);
            $query->execute();

            // Vérifier si la requête de suppression a réussi
            if ($query->rowCount() > 0) {
                return true; // Succès : le lien GitHub a été supprimé avec succès
            } else {
                return false; // Échec : aucun enregistrement supprimé
            }
        } catch (PDOException $e) {
            // Gérer les erreurs de la base de données
            // Vous pouvez ajuster cela en fonction de vos besoins
            echo "Erreur de base de données: " . $e->getMessage();
            return false;
        }
    }
    
    /**
     * Méthode pour mettre à jour la colonne "verifier" d'une ligne dans la table github en utilisant l'ID
     *
     * @param int $githubId L'ID du lien GitHub à mettre à jour
     * @return bool Retourne true en cas de succès, false en cas d'échec
     */
    public function updateGithubLinkVerification($githubId)
    {
        try {
            // Requête pour mettre à jour la colonne "verifier" à true pour le lien GitHub avec l'ID spécifié
            $query = $this->bd->prepare("UPDATE github SET verifier = TRUE WHERE id_github = :githubId");
            $query->bindParam(':githubId', $githubId, PDO::PARAM_INT);
            $query->execute();

            // Vérifier si la requête de mise à jour a réussi
            if ($query->rowCount() > 0) {
                return true; // Succès : la colonne "verifier" a été mise à jour avec succès
            } else {
                return false; // Échec : aucun enregistrement mis à jour
            }
        } catch (PDOException $e) {
            // Gérer les erreurs de la base de données
            // Vous pouvez ajuster cela en fonction de vos besoins
            echo "Erreur de base de données: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Méthode pour récupérer toutes les informations sur les sites gérés
     *
     * @return array|false Un tableau contenant toutes les informations sur les sites gérés en cas de succès, false en cas d'échec
     */
    public function getSitesManage()
    {
        try {
            // Requête pour récupérer toutes les informations sur les sites gérés
            $query = $this->bd->prepare("SELECT s.*, u.nom AS nom_utilisateur, u.prenom AS prenom_utilisateur, g.lien AS lien_github FROM site s JOIN utilisateur u ON s.id_utilisateur = u.id_utilisateur LEFT JOIN github g ON s.id_github = g.id_github");
            $query->execute();

            // Récupérer les données des sites gérés avec toutes les informations associées
            $sites = $query->fetchAll(PDO::FETCH_ASSOC);

            // Retourner les données des sites gérés
            return $sites;
        } catch (PDOException $e) {
            // Gérer les erreurs de la base de données
            // Vous pouvez ajuster cela en fonction de vos besoins
            echo "Erreur de base de données: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Méthode pour supprimer une ligne dans la table site en utilisant l'ID
     *
     * @param int $siteId L'ID du site à supprimer
     * @return bool Retourne true en cas de succès, false en cas d'échec
     */
    public function deleteSite($siteId)
    {
        try {
            // Requête pour supprimer le site avec l'ID spécifié
            $query = $this->bd->prepare("DELETE FROM site WHERE id_site = :siteId");
            $query->bindParam(':siteId', $siteId, PDO::PARAM_INT);
            $query->execute();

            // Vérifier si la requête de suppression a réussi
            if ($query->rowCount() > 0) {
                return true; // Succès : le site a été supprimé avec succès
            } else {
                return false; // Échec : aucun enregistrement supprimé
            }
        } catch (PDOException $e) {
            // Gérer les erreurs de la base de données
            // Vous pouvez ajuster cela en fonction de vos besoins
            echo "Erreur de base de données: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Méthode pour ajouter un log dans la table logs
     *
     * @param string $commentaire Le commentaire à enregistrer dans le log
     * @param int $userId L'ID de l'utilisateur associé au log
     * @return bool Retourne true en cas de succès, false en cas d'échec
     */
    public function addLog($commentaire, $userId)
    {
        try {
            // Requête pour ajouter un log dans la table logs
            $query = $this->bd->prepare("INSERT INTO logs (commentaire, date_du_log, id_utilisateur) VALUES (:commentaire, NOW(), :userId)");
            $query->bindParam(':commentaire', $commentaire, PDO::PARAM_STR);
            $query->bindParam(':userId', $userId, PDO::PARAM_INT);
            $query->execute();

            // Vérifier si la requête d'insertion a réussi
            if ($query->rowCount() > 0) {
                return true; // Succès : le log a été ajouté avec succès
            } else {
                return false; // Échec : aucun enregistrement ajouté
            }
        } catch (PDOException $e) {
            // Gérer les erreurs de la base de données
            // Vous pouvez ajuster cela en fonction de vos besoins
            echo "Erreur de base de données: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Méthode pour récupérer les logs dans l'ordre chronologique
     *
     * @return array|false Un tableau contenant les logs dans l'ordre chronologique en cas de succès, false en cas d'échec
     */
    public function getLogsChronological()
    {
        try {
            // Requête pour récupérer les logs dans l'ordre chronologique
            $query = $this->bd->prepare("SELECT * FROM logs ORDER BY date_du_log DESC");
            $query->execute();

            // Récupérer les logs dans l'ordre chronologique
            $logs = $query->fetchAll(PDO::FETCH_ASSOC);

            // Retourner les logs
            return $logs;
        } catch (PDOException $e) {
            // Gérer les erreurs de la base de données
            // Vous pouvez ajuster cela en fonction de vos besoins
            echo "Erreur de base de données: " . $e->getMessage();
            return false;
        }
    }
}