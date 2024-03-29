<?php

class Controller_auth extends Controller
{
    public function action_default()
    {
        $this->action_auth();
    }

    public function action_auth()
    {
        
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_token'])) {
            // Vérifier si les valeurs de session ne sont pas vides
            if (!empty($_SESSION['user_id']) && !empty($_SESSION['user_token'])) {
                $model = Model::getModel();
                // La session existe avec des valeurs non vides et existantes
                $data = [
                    'co' => 'connected',
                    'user' => $model->getUserInfoByToken($_SESSION['user_token']),
                ];
                $this->render('home', $data);
            } 
        }
        if (!isset($_GET['ci'])){
            $data=[
                'ci' => 'connecter'
            ];
        }else{
            $data=[
                'ci' => $_GET['ci']
            ];
        }
        $this->render("auth", $data);
    }

    public function action_connecter()
    {
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_token'])) {
            // Vérifier si les valeurs de session ne sont pas vides
            if (!empty($_SESSION['user_id']) && !empty($_SESSION['user_token'])) {
                $model = Model::getModel();
                // La session existe avec des valeurs non vides et existantes
                $data = [
                    'co' => 'connected',
                    'user' => $model->getUserInfoByToken($_SESSION['user_token']),
                ];
                $this->render('home', $data);
            } 
        }
        if (!isset($_GET['ci'])){
            $data=[
                'ci' => 'connecter'
            ];
        }else{
            $data=[
                'ci' => $_GET['ci']
            ];
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['email'], $_POST['password']) && !empty($_POST['email']) && !empty($_POST['password'])) {
                $email = e(trim($_POST['email']));
                $password = e(trim($_POST['password']));

                if (strlen($password) <= 256 && strlen($email) <= 128) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $user = Model::getModel()->getUserByCredentials($email, $password);
                        
                        if ($user) {
                            $userId = $user['id_utilisateur'];
                            if (Model::getModel()->isMailVerified($userId)){
                                echo 'verifie le mail';
                                $this->render("auth", $data);
                            }

                            // Connexion réussie, créer une session et stocker le token
                            session_start();

                            // Vous pouvez stocker d'autres informations de l'utilisateur si nécessaire
                            $_SESSION['user_id'] = $user['id_utilisateur'];
                            $_SESSION['user_token'] = $user['token'];
                            $_SESSION['expire_time'] = time() + (30 * 60); // 15 minutes d'expiration

                            // Rediriger vers le tableau de bord après la connexion réussie
                            header("Location: ?controller=home");
                            exit();
                        } else {
                            echo "no user";
                        }
                    } else {
                        echo "Format d'e-mail invalide.";
                    }
                } else {
                    echo "Les données saisies dépassent les limites autorisées.";
                }
            } else {
                echo "Veuillez remplir tous les champs requis.";
            }
        } else {
            echo "Accès non autorisé.";
        }

        $this->render("auth", $data);
    }

    public function action_inscrire() {
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_token'])) {
            // Vérifier si les valeurs de session ne sont pas vides
            if (!empty($_SESSION['user_id']) && !empty($_SESSION['user_token'])) {
                $model = Model::getModel();
                // La session existe avec des valeurs non vides et existantes
                $data = [
                    'co' => 'connected',
                    'user' => $model->getUserInfoByToken($_SESSION['user_token']),
                ];
                $this->render('home', $data);
            } 
        }
        if (!isset($_GET['ci'])){
            $data=[
                'ci' => 'inscrire'
            ];
        }else{
            $data=[
                'ci' => $_GET['ci']
            ];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                isset($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['password'], $_POST['but'])
                && !empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['but'])
            ) {
                $nom = e(trim($_POST['nom']));
                $prenom = e(trim($_POST['prenom']));
                $email = e(trim($_POST['email']));
                $password = e(trim($_POST['password']));

                if (strlen($nom) <= 64 && strlen($prenom) <= 64 && strlen($password) <= 256 && strlen($email) <= 128) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        if (preg_match('/^[a-zA-Z\-]+$/', $nom) && preg_match('/^[a-zA-Z\-]+$/', $prenom)) {

                            $but = isset($_POST['but']) ? ($_POST['but'] === '1' ? '1' : ($_POST['but'] === '2' ? '2' : ($_POST['but'] === '3' ? '3' : ''))) : '';

                            if ($but !== '') {
                                $result = Model::getModel()->registerStudent($nom, $prenom, $password, $email, $but);

                                if ($result) {
                                    echo "Inscription réussie!<br>";
                                    $verificationToken = Model::getModel()->getTokenUtilisateurByEmail($email);
                                    $verificationLink = 'http://localhost/B-I.xyz/but-info/?controller=auth&action=valide_email&token=' . urlencode($verificationToken);

                                    EmailSender::sendVerificationEmail($email, 'Vérification de l\'adresse e-mail', 'Cliquez sur le lien suivant pour vérifier votre adresse e-mail: ' . $verificationLink);
                                    
                                    echo "<br> Un e-mail de vérification a été envoyé à votre adresse. <br>";
                                    if (!isset($_GET['ci'])){
                                        $data=[
                                            'ci' => 'connecter'
                                        ];
                                    }else{
                                        $data=[
                                            'ci' => $_GET['ci']
                                        ];
                                    }
                                } else {
                                    echo "Erreur lors de l'inscription.";
                                }
                            } else {
                                echo "Rôle invalide.";
                            }
                        } else {
                            echo "Le nom et le prénom ne doivent contenir que des lettres et des tirets.";
                        }
                    } else {
                        echo "Format d'email invalide.";
                    }
                } else {
                    echo "Les données saisies dépassent les limites autorisées.";
                }
            } else {
                echo "Veuillez remplir tous les champs requis.";
            }
        } else {
            echo "Accès non autorisé.";
        }

        $this->render("auth", $data);

    }

    public function action_valide_email() {
        // Récupérer le token depuis les paramètres de l'URL
        $token = isset($_GET['token']) ? $_GET['token'] : '';
    
        // Valider le token en appelant une fonction du modèle
        $validationResult = Model::getModel()->validerTokenUtilisateur($token);
        if (!isset($_GET['ci'])){
            $data=[
                'ci' => 'connecter'
            ];
        }else{
            $data=[
                'ci' => $_GET['ci']
            ];
        }
        if ($validationResult) {
            echo "Adresse e-mail vérifiée avec succès!";
            
        } else {
            echo "Erreur lors de la vérification de l'adresse e-mail. Le lien peut avoir expiré ou être invalide.";
        }
    
        $this->render("auth", $data);
    }
    public function action_deconnexion() {
        $_SESSION = array();
        if (!isset($_GET['ci'])){
            $data=[
                'ci' => 'connecter'
            ];
        }else{
            $data=[
                'ci' => $_GET['ci']
            ];
        }
        $this->render("auth", $data);
    }
    public function action_reset() {
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_token'])) {
            // Vérifier si les valeurs de session ne sont pas vides
            if (!empty($_SESSION['user_id']) && !empty($_SESSION['user_token'])) {
                $model = Model::getModel();
                // La session existe avec des valeurs non vides et existantes
                $data = [
                    'co' => 'connected',
                    'user' => $model->getUserInfoByToken($_SESSION['user_token']),
                ];
                $this->render('home', $data);
            } 
        }
        if (!isset($_GET['ci'])){
            $data=[
                'ci' => 'connecter'
            ];
        }else{
            $data=[
                'ci' => $_GET['ci']
            ];
        }
        $this->render('auth_reset', $data);
    }

    public function action_reset_mdp(){
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_token'])) {
            // Vérifier si les valeurs de session ne sont pas vides
            if (!empty($_SESSION['user_id']) && !empty($_SESSION['user_token'])) {
                $model = Model::getModel();
                // La session existe avec des valeurs non vides et existantes
                $data = [
                    'co' => 'connected',
                    'user' => $model->getUserInfoByToken($_SESSION['user_token']),
                ];
                $this->render('home', $data);
            } 
        }
        $model = Model::getModel();
        if (isset($_POST['email'])){
            if($model->isMailExist($_POST['email'])){
                echo "Mail de reset envoyé a votre adresse email";
                $email = $_POST['email'];
                $resetMdpToken = Model::getModel()->getTokenUtilisateurByEmail($email);
                $resetMdpToken = 'http://localhost/B-I.xyz/but-info/?controller=auth&action=reset_mdp_nl&token=' . urlencode($resetMdpToken);

                 EmailSender::sendVerificationEmail($email, 'Vérification de l\'adresse e-mail', 'Cliquez sur le lien suivant pour vérifier votre adresse e-mail: ' . $resetMdpToken);
                                    
                if (!isset($_GET['ci'])){
                    $data=[
                        'ci' => 'connecter'
                    ];
                }else{
                    $data=[
                        'ci' => $_GET['ci']
                    ];
                }
                $this->render('auth', $data);
            }else{
                echo "compte existe pas";
            }

        }else{
            $data = [
                'co' => 'connected'
            ];
            $this->render('home', $data);
        }
    }
    public function action_reset_mdp_nl(){
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_token'])) {
            // Vérifier si les valeurs de session ne sont pas vides
            if (!empty($_SESSION['user_id']) && !empty($_SESSION['user_token'])) {
                $model = Model::getModel();
                // La session existe avec des valeurs non vides et existantes
                $data = [
                    'co' => 'connected',
                    'user' => $model->getUserInfoByToken($_SESSION['user_token']),
                ];
                $this->render('home', $data);
            } 
        }    
        $model = Model::getModel();
        if(isset($_GET['token'])){
            if($model->isTokenExist($_GET['token'])){
                $data = [
                    'token' =>$_GET['token']
                ];
                $this->render('auth_reset_mdp_nl', $data);
            }
        }
    }
    
    public function action_reset_mdp_nk(){
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_token'])) {
            // Vérifier si les valeurs de session ne sont pas vides
            if (!empty($_SESSION['user_id']) && !empty($_SESSION['user_token'])) {
                $model = Model::getModel();
                // La session existe avec des valeurs non vides et existantes
                $data = [
                    'co' => 'connected',
                    'user' => $model->getUserInfoByToken($_SESSION['user_token']),
                ];
                $this->render('home', $data);
            } 
        }    
        $model = Model::getModel();
        if(isset($_GET['token'])){
            if($model->isTokenExist($_GET['token'])){
                if(isset($_POST['mdp'])){
                    $newPassword = e(trim($_POST['mdp']));
                    $token = e($_GET['token']);
                    if(strlen($newPassword) <= 256){
                        $result = $model->changePasswordByToken($token, $newPassword);
                        echo "C'est ok !";
                        if (!isset($_GET['ci'])){
                            $data=[
                                'ci' => 'connecter'
                            ];
                        }else{
                            $data=[
                                'ci' => $_GET['ci']
                            ];
                        }
                        $this->render('auth', $data);
                    }else{
                        echo 'mdp trop petit ou trop grand';
                    }
                }else{
                    echo 'error 404 mdp';
                }
            }else{
                echo 'error 404 token';
            }
        }else{
            echo 'error 404 token';
        }
        if (!isset($_GET['ci'])){
            $data=[
                'ci' => 'connecter'
            ];
        }else{
            $data=[
                'ci' => $_GET['ci']
            ];
        }
        $this->render('auth', $data);
    }
}