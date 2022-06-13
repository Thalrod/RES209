<?php

class Event extends Model
{

    private $_id;
    private $_title;
    private $_description;
    private $_startts;
    private $_endts;
    private $_owner_id;
    private $_export;
    private $_color;
    private $_owner;



    public function __construct(array $data)
    {
        $this->hydrate($data);
    }

    public function toJson()
    {
        return json_encode([
            "id" => $this->_id,
            "title" => $this->_title,
            "description" => $this->_description,
            "startts" => $this->_startts,
            "endts" => $this->_endts,
            "owner" => $this->_owner,
            "export" => $this->_export,
            "color" => $this->_color
        ]);
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
            $this->_errors['id'] = "L'id de l'événement n'est pas valide";
        }
    }

    /**
     * Get the value of _title
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Set the value of _title
     *
     * @return  self
     */
    public function setTitle(string $_title)
    {
        if (strlen((string) $_title) < 50 && strlen((string) $_title) > 0) {
            $this->_title = $_title;
        } else {
            $this->_errors['title'] = "Le titre de l'événement n'est pas valide";
        }
    }

    /**
     * Get the value of _description
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Set the value of _description
     *
     * @return  self
     */
    public function setDescription(string $_description)
    {
        if (strlen((string) $_description) < 255) {
            $this->_description = $_description;
        } else {
            $this->_errors['description'] = "La description de l'événement n'est pas valide";
        }

        return $this;
    }

    /**
     * Get the value of _start
     */
    public function getStartts()
    {
        return $this->_startts;
    }

    /**
     * Set the value of _start
     *
     * @return  self
     */
    public function setStartts(string $_startts)
    {
        if (preg_match('/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01]) ([0-1]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $_startts)) {
            $this->_startts = $_startts;
        } else {
            $this->_errors['startts'] = "L'heure de début est invalide !";
        }
        $this->_startts = $_startts;

        return $this;
    }

    /**
     * Get the value of _end
     */
    public function getEndts()
    {
        return $this->_endts;
    }

    /**
     * Set the value of _end
     *
     * @return  self
     */
    public function setEndts(string $_endts)
    {

        if (preg_match('/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01]) ([0-1]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $_endts) && $_endts > $this->_startts) {
            $this->_endts = $_endts;
        } else {
            $this->_errors['endts'] = "L'heure de fin est invalide !";
        }

        return $this;
    }

    /**
     * Get the value of _owner
     */
    public function getOwner_id()
    {
        return $this->_owner_id;
    }

    /**
     * Set the value of _owner
     *
     * @return  self
     */
    public function setOwner_id(int $_owner_id)
    {


        if (is_int($_owner_id)) {

            $this->_owner_id = $_owner_id;
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
     * Get the value of _export
     */
    public function getExport()
    {
        return $this->_export;
    }

    /**
     * Set the value of _export
     *
     * @return  self
     */
    public function setExport(string $_export)
    {
        if (preg_match('/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01]) ([0-1]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $_export)) {
            $this->_export = $_export;
        } else {
            $this->_errors['export'] = "La date de création est invalide !";
        }
        $this->_export = $_export;

        return $this;
    }

    /**
     * Get the value of _color
     */
    public function getColor()
    {
        return $this->_color;
    }

    /**
     * Set the value of _color
     *
     * @return  self
     */
    public function setColor(string $color)
    {

        if (strpos($color, "#") === false) {
            $color = "#" . $color;
        }

        if (preg_match('/^#[a-fA-F0-9]{6}$/', $color)) {
            $this->_color = $color;
        } else {
            $this->_errors['color'] = "La couleur n'est pas valide";
        }

        return $this;
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
        return $this;
    }

}
