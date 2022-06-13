<?php

class ControllerAuth extends Controller
{

    public function __construct($url)
    {

        if (isset($url[1]) && !empty($url[1])) {

            switch ($url[1]) {
                case 'signup':
                    if ($this->isLogged()) {
                        $this->goto('home');
                    }
                    $this->signup();
                    break;
                case 'login':

                    if ($this->isLogged()) {
                        
                        $this->goto('home');
                    } else {
                        $this->login();
                    }
                    break;
                case 'logout':
                    $this->logout();

                    break;
                case 'confirm':

                    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : false;
                
                    if ($this->isLogged() && $referer !=  "/auth/login" || $referer !=  "/auth/signup" && $referer == false) {

                        $this->goto('home');
                    } else {
                        $this->confirm();
                    }
                    
                    break;
                default:
                    $this->goto('home');
                    break;
            }
        } else {
            $this->goto('home');
        }
    }

    private function signup()
    {

        $this->_view = new View('Signup');
        $this->_view->render(array(
            'template' => 'Auth',
            'js' => array(
                '../js/signup.js'
            )
        ));
    }

    private function login()
    {
        $this->_view = new View('Login');
        $this->_view->render(array(
            'template' => 'Auth',
            'js' => ['../js/login.js']


        ));
    }

    private function logout()
    {

        $auth = new Authentication();
        if ($this->isLogged()) {
            //print_r(["logout","<br>"]);
            $auth->logout();
            $this->goto('home');
        }
        $this->goto('home');
    }

    private function confirm()
    {

        $error = array();

        switch (count($_POST)) {
            case 2:
                
                if (!($_POST['username'] && $_POST['password'])) {
                    $error['form'] = "Veuillez remplir tous les champs";
                }

                //check if username is already exist
                $auth = new Authentication();
                $error = array_merge($error, $auth->checkLogin($_POST));

                if (count($error) == 0) {
                    try {
                        
                        $auth->loginAccount($_POST);
                        $this->sendJsonResponse(200, ["redirect" => BASE_URL . "home"]);
                    } catch (Exception $e) {
                        $msg = explode("&&", $e->getMessage());
                        $error = array_merge($error, [$msg[0] => $msg[1]]);
                    }
                }
                break;
            case 6:
                if (!isset($_POST['last_name'], $_POST['first_name'], $_POST['email'], $_POST['username'], $_POST['password'], $_POST['confirm'])) {
                    $error['form'] = "Veuillez remplir tous les champs";
                }

                $auth = new Authentication();
                $error = array_merge($error, $auth->checkSignup($_POST));

                if (count($error) == 0) {
                    try {
                        $auth->registerAccount($_POST);
                        $this->sendJsonResponse(200, ["redirect" => BASE_URL . "home"]);
                    } catch (Exception $e) {
                        $msg = explode("&&", $e->getMessage());
                        $error = array_merge($error, [$msg[0] => $msg[1]]);
                    }
                }
                break;
            default:
                $this->goto('home');
                break;
        }
        if (count($error) > 0) {
            $this->sendJsonResponse(400, $error);
        }
    }
}
