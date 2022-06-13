<?php


class Authentication extends Model
{
    public function registerAccount($args)
    {

        if ($this->findUserByUsername($args['username'])) {
            throw new Exception("username&&Ce nom d'utilisateur existe déjà!");
            return false;
        }

        $this->table = 'USERS';
        $userid = $this->save([
            "selectors" => [
                "username" => $args['username'],
                "password_hash" => password_hash($args['password'], PASSWORD_DEFAULT)
            ],
            "RETURN" => ["id"]

        ]);
        $this->table = 'ACCOUNTS';
        $accountid = $this->save(
            [
                "selectors" => [
                    "user_id" => $userid["id"],
                    "last_name" => $args['last_name'],
                    "first_name" => $args['first_name'],
                    "email" => $args['email'],
                    "creation_date" => date("Y-m-d H:i:s"),
                ],
                "RETURN" => ["id"]

            ]
        );
        $this->logUserIn($userid["id"]);
        $this->table = "AGENDAS";
        $agendaid = $this->save(
            [
                "selectors" => [
                    "name" => $args['last_name']." ".$args['first_name'],
                    "owner_id" => $accountid["id"],
                ],
                "RETURN" => ["id"]
            ]
        );
        $this->table = "ACCOUNT_TO_AGENDAS";
        $this->save(
            [
                "selectors" => [
                    "account_id" => $accountid["id"],
                    "agenda_id" => $agendaid["id"]
                ]

            ]
        );

        $this-> table = "ACCOUNTS";
        $this->save(
            [   "id" => $accountid["id"],
                "selectors" => [
                    "last_agenda" => $agendaid["id"]
                ]
            ]
        );
    }

    public function loginAccount($args)
    {
        $user = $this->findUserByUsername($args['username']);

        if ($user) {
            if (password_verify($args['password'], $user[0]["password_hash"])) {
                
                $this->logUserIn($user[0]["id"]);
                return true;
            }
        }
        throw new Exception("form&&Identifiants incorrects !");
        return false;
    }

    public function logUserIn($userid)
    {
        
        $_SESSION['user_id'] = $userid;
        $_SESSION['username'] = $this->findUserByID($userid)[0]['username'];
        $_SESSION['accountid'] = $this->findAccountIdByUserId($userid)[0]['id'];
        
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        session_destroy();
    }

  
    public function checkLogin($args)
    {
        $errors = [];
        $user = new User(
            [
                'username' => $args['username']
            ]
        );

        if ($user->_errors) {
            $errors = array_merge($errors, $user->_errors);
        }

        if (!isset($args['password']) || empty($args['password'])) {
            $errors['password'] = "Veuillez entrer un mot de passe";
        }
        return $errors;
    }

    public function checkSignup($args)
    {
        $errors = [];
        //check if password and confirm password are the same
        if ($args['password'] == "") {
            $errors['password'] = "Veuillez entrer un mot de passe";
        }

        if ($args['password'] != $args['confirm']) {
            $errors['confirm'] = "Les mots de passe ne correspondent pas";
        }
        //check if user params are valid
        $user = new User([
            'username' => $args['username']
        ]);

        if ($user->_errors) {
            $errors = array_merge($errors, $user->_errors);
        }

        //check if account params are valid
        $account = new Account([
            'last_name' => $args['last_name'],
            'first_name' => $args['first_name'],
            'email' => $args['email'],
        ]);

        if ($account->_errors) {
            $errors = array_merge($errors, $account->_errors);
        } else {
            $isEmailUsed = $this->isUsed("ACCOUNTS", "email", $args['email']);
            if ($isEmailUsed) {
                $errors['email'] = "Cet email est déjà utilisé";
            }
        }


        return $errors;
    }

    private function isUsed($table, $compared, $referring)
    {
        $this->table = $table;
        $isUsed = $this->find([
            'conditions' => [
                $compared . " = " => $referring
            ]

        ]);


        return $isUsed ? $isUsed : false;
    }

    

    private function findUserByID($user_id)
    {
        $this->table = "USERS";
        $res = $this->find([
            "selectors" => ["username"],
            "conditions" => ["id = " => $user_id]
        ]);

        return $res;
    }

    private function findAccountIdByUserId($user_id)
    {
        $this->table = "ACCOUNTS";
        $res = $this->find([
            "selectors" => ["id"],
            "conditions" => ["user_id = " => $user_id]
        ]);

        return $res;
    }


  
    private function findUserByUsername($username)
    {
        $this->table = "USERS";
        $res = $this->find([
            'selectors' => [
                'id',
                'password_hash'
            ],
            'conditions' => [
                'username =' => $username
            ],
        ]);
        return $res;
    }


}
