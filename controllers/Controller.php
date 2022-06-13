<?php

class Controller
{

    protected $_url;
    protected $_view;

    protected function goto($url)
    {

        header('Location: ' . WEBROOT . $url);
        die();
    }


    protected function isLogged()
    {

        if (isset($_SESSION['username'])) {
            //print_r("session username","<br>");
            return true;
        }
        return false;
    }

    protected function sendJsonResponse($code, $data = null)
    {
        http_response_code($code);
        
        if ($data) {
            header('Content-Type: application/json');
            echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES permet de ne pas échapper les caractères spéciaux par exemple ne pas avoir \u00e9 au lieu de é
    }
}
