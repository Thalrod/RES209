<?php

class ControllerHome extends Controller
{

    public function __construct($url)
    {
        $this->_url = $url;

        if (isset($this->_url[0]) && !empty($this->_url[0])) {
            if ($this->isLogged()) {
                //récupération de l'agenda
                $accountManager = new AccountManager();

                $account = $accountManager->getAccountByID($_SESSION['accountid']);


                $_SESSION['last_agenda'] = $account->getLast_agenda();



                $this->_view = new View('Home');
                $this->_view->render(["lastname" => $account->getLast_name(), "firstname" => $account->getFirst_name(), "js" => ["main.js"]]);
            } else {
                $this->goto('auth/login');
            }
        } else {
            throw new Exception('Page not found');
        }
    }
}
