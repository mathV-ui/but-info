<?php

class Controller_home extends Controller
{
    public function action_default()
    {
        $this->action_home();
    }

    public function action_home(){

        $model = Model::getModel();

        $data = [
            //'' => $model->getActivitiesList()
        ];

        $this->render('home', $data);

    }
}