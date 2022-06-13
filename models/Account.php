<?php


class Account extends Model
{

    private $_id;
    private $_user_id;
    private $_last_name;
    private $_first_name;
    private $_email;
    private $_creation_date;
    private $_last_agenda;

    public function __construct(array $data)
    {
        $this->hydrate($data);
    }

    public function toJson()
    {
        return json_encode(
            [
                "id" => $this->_id,
                "user_id" => $this->_user_id,
                "last_name" => $this->_last_name,
                "first_name" => $this->_first_name,
                "email" => $this->_email,
                "creation_date" => $this->_creation_date,
                "last_agenda" => $this->_last_agenda
            ]
        );
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
        else {
            $this->_errors['id'] = "L'id du compte n'est pas valide";
        }
    }

    public function getUser_id()
    {
        return $this->_user_id;
    }

    public function setUser_id($user_id)
    {
        $user_id = (int) $user_id;
        if ($user_id > 0) {
            $this->_user_id = $user_id;
        }
        else {
            $this->_errors['user_id'] = "L'id de l'utilisateur n'est pas valide";
        }
    }

    public function getLast_name()
    {
        return $this->_last_name;
    }

    public function setLast_name($last_name)
    {
        if (preg_match_all("/^[a-zA-Z]{1,20}$/", $last_name)) {
            $this->_last_name = $last_name;
        }
        else {
            $this->_errors['last_name'] = "Le nom est trop long ou invalide";
        }
    }

    public function getFirst_name()
    {
        return $this->_first_name;
    }

    public function setFirst_name($first_name)
    {
        if (preg_match_all("/^[a-zA-Z]{1,20}$/", $first_name)) {
            $this->_first_name = $first_name;
        }
        else 
        {
            $this->_errors['first_name'] = "Le prénom trop long ou invalide";
        }
    }

    public function getEmail()
    {
        return $this->_email;
    }

    public function setEmail($email)
    {
        //use regex to check if email is valid

        if (preg_match_all('/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/', $email)) {
            $this->_email = $email;
        }
        else {
            $this->_errors['email'] = "L'email n'est pas valide";
        }
    }

    public function getCreation_date()
    {
        return $this->_creation_date;
    }

    public function setCreation_date($creation_date)
    {
        if (preg_match_all("/[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) (2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]/", $creation_date)) //2012-12-12 12:10:10
        {
            $this->_creation_date = $creation_date;
        }
        else {
            $this->_errors['creation_date'] = "La date n'est pas valide";
        }
    }

    /**
     * Get the value of _last_agenda
     */ 
    public function getLast_agenda()
    {
        return $this->_last_agenda;
    }

    /**
     * Set the value of _last_agenda
     *
     * @return  self
     */ 
    public function setLast_agenda($last_agenda)
    {
        $this->_last_agenda = $last_agenda;

        return $this;
    }
}
?>
