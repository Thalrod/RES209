<?php


class Agenda extends Model{

    private $_id;
    private $_owner_id;
    private $_name;
    private $_owner;

    public function __construct(array $data)
    {
        $this->hydrate($data);
    }

    
    public function toJson()
    {
        return json_encode(
            [
                "id" => $this->_id,
                "owner_id" => $this->_owner_id,
                "name" => $this->_name
            ]
        );
    }

    /**
     * Get the value of _id
     */ 
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set the value of _id
     *
     * @return  self
     */ 
    public function setId(int $id)
    {   
        if ($id > 0) {
            $this->_id = $id;
        } else {
            $this->_errors['id'] = "L'id du groupe n'est pas valide";
        }
    }

    /**
     * Get the value of _owner_id
     */ 
    public function getOwner_id()
    {
        return $this->_owner_id;
    }

    /**
     * Set the value of _owner_id
     *
     * @return  self
     */ 
    public function setOwner_id(int $owner_id)
    {
        if (is_int($owner_id)) {

            $this->_owner_id = $owner_id;
            if (!$this->_owner) {
                $this->table = "USERS";
                $this->setOwner($this->find(
                    [
                        "selectors" => [
                            "USERS.username",
                        ],
                        "innerJoin" => [
                            [
                                "table" => "ACCOUNTS",
                                "compared" => "ACCOUNTS.user_id",
                                "operator" => "=",
                                "referring" => "USERS.id"
                            ]

                        ],
                        "conditions" => [
                            "ACCOUNTS.id  =" => $this->_owner_id
                        ]
                    ]
                )[0]['username']);
            }
        } else {
            $this->_errors['owner_id'] = "L'id de l'utilisateur n'est pas valide";
        }
    }

    /**
     * Get the value of _name
     */ 
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set the value of _name
     *
     * @return  self
     */ 
    public function setName(string $name)
    {
        if (strlen((string) $name) < 50 && strlen((string) $name) > 0) {
            $this->_name = $name;
        } else {
            $this->_errors['name'] = "Le nom de l'agenda n'est pas valide";
        }

    }

    /**
     * Get the value of _owner
     */ 
    public function getOwner()
    {
        return $this->_owner;
    }

    /**
     * Set the value of _owner
     *
     * @return  self
     */ 
    public function setOwner(string $owner)
    {

        if (strlen((string) $owner) < 20 && strlen((string) $owner) > 0) {
            $this->_owner = $owner;
        } else {
            $this->_errors['owner'] = "Le nom de l'utilisateur n'est pas valide";
        }
    }
}
