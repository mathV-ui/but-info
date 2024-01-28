<?php

class Controller_github extends Controller
{
    public function action_default()
    {
        $this->action_github();
    }
    public function action_github()
    {
        $model = Model::getModel();
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_token'])) {
            // Vérifier si les valeurs de session ne sont pas vides
            if (empty($_SESSION['user_id']) && empty($_SESSION['user_token'])) {
                // La session existe avec des valeurs non vides et existantes
                header("Location: ?");
                exit;
            } 
        }
        $data = [
            'co' => 'connected',
            'user' => $model->getUserInfoByToken($_SESSION['user_token']),
        ];
        $this->render("github", $data);
    }
    public function action_ajouter()
    {
        $model = Model::getModel();
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_token'])) {
            // Vérifier si les valeurs de session ne sont pas vides
            if (empty($_SESSION['user_id']) && empty($_SESSION['user_token'])) {
                // La session existe avec des valeurs non vides et existantes
                header("Location: ?");
                exit;
            } 
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Récupérer la valeur envoyée via POST
            $lienGithub = $_POST["lien_github"];
            $userId = $_SESSION['user_id'];
            // Vérifier si le lien commence par "https://github.com/"
            if (strpos($lienGithub, "https://github.com/") === 0) {
                // Compter le nombre total de "/" dans le lien
                $nbSlash = substr_count($lienGithub, "/");
        
                // Vérifier si le nombre total de "/" est égal à 5
                if ($nbSlash === 4) {
                    $result = $model->addGithubLink($userId, $lienGithub);
                    if ($result){
                        echo "Le lien GitHub est valide. Et envoyé";
                    }
                } else {
                    echo "Le lien GitHub n'est pas valide. Assurez-vous d'inclure le nom d'utilisateur et le nom du dépôt.";
                }
            } else {
                echo "Le lien GitHub doit commencer par 'https://github.com/'.";
            }
        } else {
            echo "Seuls les envois POST sont autorisés.";
        }   
        $data = [
            'co' => 'connected',
            'user' => $model->getUserInfoByToken($_SESSION['user_token']),
        ];
        $this->render("github", $data);
    }

}