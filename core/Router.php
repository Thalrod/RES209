<?php

class Router
{

    private $_ctrlr; //controller
    private $_view; //view

    public function routeReq()
    {
        try {


            $url = array();
            if (isset($_GET['url'])) {
                $url = explode('/', filter_var($_GET['url'], FILTER_SANITIZE_URL));

                $controller = ucfirst(strtolower($url[0]));
                $controllerClass = "Controller" . $controller;
                $controllerFile = "controllers/" . $controllerClass . ".php";

                if (file_exists($controllerFile)) {
                    require_once($controllerFile);
                    $this->_ctrlr = new $controllerClass($url);
                } else {
                    throw new Exception('Page not found');
                }
            } else {

                header('Location: ' . WEBROOT . 'home');
            }
        } catch (Exception $e) {

            $errorMsg = $e->getMessage();
            $this->_view = new View('Error');
            $this->_view->render(['errorMsg' => $errorMsg, "js" => []]);
        }
    }
}
