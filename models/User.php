<?php

class User extends Model{
    private $_id;
    private $_username;
    private $_passwordhash;


    public function __construct(array $data)
    {
        $this->hydrate($data);
    }
    

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $id = (int) $id;
        if ($id > 0) {
            $this->_id = $id;
        }
    }

    public function getUsername()
    {
        return $this->_username;
    }

    public function setUsername($username)
    {
        if (preg_match_all('/^[a-zA-Z0-9]{1,20}$/', $username)) {
            $this->_username = $username;
            
        }
        else 
        {
            $this->_errors['username'] = "Le nom d'utilisateur n'est pas valide";
        }
    }

    public function getPasswordhash()
    {
        return $this->_passwordhash;
    }

    public function setPasswordhash($passwordhash)
    {
        if (is_string($passwordhash) && strlen($passwordhash) > 0 && strlen($passwordhash) <= 255) {
            $this->_passwordhash = $passwordhash;
        }

    }
    

}

?>