<?php

class Controller_about extends Controller
{
    public function action_default()
    {
        $this->action_about();
    }

    public function action_about(){
        $model = Model::getModel();

        if (isset($_SESSION['user_id']) && isset($_SESSION['user_token'])) {
            // Vérifier si les valeurs de session ne sont pas vides
            if (!empty($_SESSION['user_id']) && !empty($_SESSION['user_token'])) {
                // La session existe avec des valeurs non vides et existantes
                $data = [
                    'co' => 'connected',
                    'user' => $model->getUserInfoByToken($_SESSION['user_token']),
                ];
            } else {
                // Les valeurs de session sont vides
                $data = [
                    'co' => 'invite',
                ];
            }
        } else {
            $data = [
                'co' => 'invite',
            ];
        }

        $data['sites'] = $model->getAllSitesWithUsers();

        $this->render('about', $data);

    }
}