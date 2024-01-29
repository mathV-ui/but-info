<?php

class Controller_panelAdmin extends Controller
{
    public function action_default()
    {
        $this->action_panel_admin();
    }  

    public function action_panel_admin(){
        $model = Model::getModel();
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_token'])) {
            // Vérifier si les valeurs de session ne sont pas vides
            if (empty($_SESSION['user_id']) && empty($_SESSION['user_token'])) {
                // La session existe avec des valeurs non vides et existantes
                header("Location: ?");
                exit;
            } 
        }
        if ($model->isAdmin($_SESSION['user_token'])){
            $data = [
                'co' => 'connected',
                'user' => $model->getUserInfoByToken($_SESSION['user_token']),
            ];
            $this->render("panel", $data);
        } else {
            header("Location: 404");
            exit;
        }
    }
    public function action_panel_github(){
        $model = Model::getModel();
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_token'])) {
            // Vérifier si les valeurs de session ne sont pas vides
            if (empty($_SESSION['user_id']) && empty($_SESSION['user_token'])) {
                // La session existe avec des valeurs non vides et existantes
                header("Location: ?");
                exit;
            } 
        }
        if ($model->isAdmin($_SESSION['user_token'])){
            $data = [
                'co' => 'connected',
                'user' => $model->getUserInfoByToken($_SESSION['user_token']),
                'liens' => $model->getUncheckedGithubLinks(),
            ];
            $this->render("panel_github", $data);
        } else {
            header("Location: 404");
            exit;
        }
    }

    public function action_accepter(){
        $model = Model::getModel();
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_token'])) {
            // Vérifier si les valeurs de session ne sont pas vides
            if (empty($_SESSION['user_id']) && empty($_SESSION['user_token'])) {
                // La session existe avec des valeurs non vides et existantes
                header("Location: ?");
                exit;
            } 
        }
        if ($model->isAdmin($_SESSION['user_token'])){
            $commentaire = 'updateGithubLinkVerification($_get['. $_GET['id'] . ')';
            $log = $model->addLog($commentaire, $_SESSION['user_id']);
            $result = $model->updateGithubLinkVerification($_GET['id']);
            $data = [
                'co' => 'connected',
                'user' => $model->getUserInfoByToken($_SESSION['user_token']),
                'liens' => $model->getUncheckedGithubLinks(),
            ];
            $this->render("panel_github", $data);
        } else {
            header("Location: 404");
            exit;
        } 
    }

    public function action_refuser(){
        $model = Model::getModel();
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_token'])) {
            // Vérifier si les valeurs de session ne sont pas vides
            if (empty($_SESSION['user_id']) && empty($_SESSION['user_token'])) {
                // La session existe avec des valeurs non vides et existantes
                header("Location: ?");
                exit;
            } 
        }
        if ($model->isAdmin($_SESSION['user_token'])){
            $commentaire = 'deleteGithubLink($_get['. $_GET['id'] . ')';
            $log = $model->addLog($commentaire, $_SESSION['user_id']);
            $result = $model->deleteGithubLink($_GET['id']);
            $data = [
                'co' => 'connected',
                'user' => $model->getUserInfoByToken($_SESSION['user_token']),
                'liens' => $model->getUncheckedGithubLinks(),
            ];
            $this->render("panel_github", $data);
        } else {
            header("Location: 404");
            exit;
        } 
    }

    public function action_panel_sites(){
        $model = Model::getModel();
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_token'])) {
            // Vérifier si les valeurs de session ne sont pas vides
            if (empty($_SESSION['user_id']) && empty($_SESSION['user_token'])) {
                // La session existe avec des valeurs non vides et existantes
                header("Location: ?");
                exit;
            } 
        }
        if ($model->isAdmin($_SESSION['user_token'])){
            $data = [
                'co' => 'connected',
                'user' => $model->getUserInfoByToken($_SESSION['user_token']),
                'liens' => $model->getSitesManage(),
            ];
            $this->render("panel_sites", $data);
        } else {
            header("Location: 404");
            exit;
        } 
    }

    public function action_supprimer(){
        $model = Model::getModel();
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_token'])) {
            // Vérifier si les valeurs de session ne sont pas vides
            if (empty($_SESSION['user_id']) && empty($_SESSION['user_token'])) {
                // La session existe avec des valeurs non vides et existantes
                header("Location: ?");
                exit;
            } 
        }
        if ($model->isAdmin($_SESSION['user_token'])){
            $commentaire = 'deleteSite($_get['. $_GET['id'] . ')';
            $log = $model->addLog($commentaire, $_SESSION['user_id']);
            $result = $model->deleteSite($_GET['id']);
            $data = [
                'co' => 'connected',
                'user' => $model->getUserInfoByToken($_SESSION['user_token']),
                'liens' => $model->getSitesManage(),
            ];
            $this->render("panel_sites", $data);
        } else {
            header("Location: 404");
            exit;
        } 
    }
}