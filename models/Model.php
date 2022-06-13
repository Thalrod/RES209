<?php


abstract class Model
{

    private static $_bdd;
    protected $_errors = [];
    protected $table;


    // connection to the DataBase
    private static function setBdd()
    {
        try {
            $user = "";
            $pass = "";
            $dbname = "";
            $host = "";
            $port = "5432";

            $dsn = 'pgsql:host=' . $host . ';dbname=' . $dbname . ';port=' . $port;
            self::$_bdd =  new PDO($dsn, $user, $pass);
            self::$_bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        } catch (Exception $e) {
            print_r("Impossible de se connecter à la base de données");
        }
    }





    protected function hydrate(array $data)
    {
        foreach ($data as $k => $v) {
            $method = 'set' . ucfirst($k);
            if (method_exists($this, $method)) {
                $this->$method($v);
            }
        }
    }

    protected function getBdd()
    {
        if (self::$_bdd == null) {
            self::setBdd();
        }
        return self::$_bdd;
    }



    protected function disconnect()
    {
        self::$_bdd = null;
    }

    private function sendRequest($query, $args)
    {

        /*echo $query . "<br> values : ";
            print_r($args);
            echo "<br>"; */

        try {

            $req = self::getBdd()->prepare($query);

            $i = 1;
            foreach ($args as $k => $v) {

                $req->bindValue($i,  $v);
                $i++;
            }

            $req->execute();

            return $req->fetchAll(PDO::FETCH_ASSOC);
            $req->closeCursor(); //Closes the cursor, disabling the statement to be executed again





        } catch (PDOException $e) {
            die("PDOException <br/> => " . $e->getTraceAsString() . "<br/> =>" . $e->errorInfo[2]);
        }
    }


    protected function save($data)
    {

        if (isset($data["id"]) && !empty($data["id"])) {

            $sql = "UPDATE " . $this->table . " SET ";
            $args = [];
            foreach ($data["selectors"] as $k => $v) {
                if ($k != "id") {
                    $sql .= $k . " = ?, ";
                    $args[] = $v;
                }
            }
            $sql = substr($sql, 0, -2);
            $sql .= " WHERE id = ?";
            $args[] = $data["id"];
            //Exemple : UPDATE ACCOUNTS SET name = 'John', email = '' WHERE id = 1 with args = array("id" => 1, "name" => "John", "email" => ""):

        } else {
            $sql = "INSERT INTO " . $this->table . " (";
            $values = ") VALUES (";
            $args = [];

            foreach ($data["selectors"] as $k => $v) {
                $sql .= $k . ", ";
                $values .= "?, ";
                $args[] = $v;
            }
            $sql = substr($sql, 0, -2);
            $values = substr($values, 0, -2);
            $sql .= $values . ")";
            if (isset($data["RETURN"])) {
                $sql .= " RETURNING ";
                foreach ($data["RETURN"] as $k => $v) {
                    $sql .= $v . ", ";
                }
                $sql = substr($sql, 0, -2);
            }
            //Exemple : INSERT INTO ACCOUNTS (name, email) VALUES ('John', '') RETURNING id; with args = array("name" => "John", "email" => "")
        }

        $res = $this->sendRequest($sql, $args)[0];
        /*
        if (isset($data["id"]) && !empty($data["id"])) {
            $this->id = $data["id"];
        } else {
            $this->id = $res[0]["id"];
        }*/
        return $res;
    }

    protected function find($data =  [])
    {
        $sql = "SELECT ";

        if (isset($data["selectors"])) {


            foreach ($data["selectors"] as $k => $v) {
                $sql .= $v . ", ";
            }

            $sql = substr($sql, 0, -2);
        } else {
            $sql .= "* ";
        }

        $sql .= " FROM " . $this->table;

        $args = [];
        if (count($data) > 0) {
            if (isset($data["innerJoin"])) {
                foreach ($data["innerJoin"] as $k) {
                    $sql .= " INNER JOIN " . $k['table'] . " ON " . $k['compared'] . $k['operator'] . $k['referring'];
                    //because PDO can't put column name in parameter so we must to put "manually" :/
                }
            }
            if (isset($data["conditions"])) {
                $sql .= " WHERE ";
                /*echo("<pre>");
                print_r($data);
                echo("</pre>");*/
                foreach ($data["conditions"] as $k => $v) {

                    $sql .= $k . " ? AND ";
                    $args[] = $v;
                }
                $sql = substr($sql, 0, -5);
            }
            if (isset($data["order"])) {
                $sql .= " ORDER BY ? ";
                $args[] = $data["order"];
            }
            if (isset($data["limit"])) {
                $sql .= " LIMIT ?";
                $args[] = $data["limit"];
            }
            //Exemple : SELECT * FROM USERS WHERE username = 'John' AND password_hash = 'dzq' ORDER BY id DESC LIMIT 1;
            //with args = array("conditions" => array("username" => "John", "password_hash" => "dzq"), "order" => "id DESC", "limit" => 15)
        }
        return $this->sendRequest($sql, $args);

        //Exemple: SELECT * FROM USERS with args = array()

    }

    protected function delete($data)
    {

        $args = [];
        $sql = "DELETE FROM " . $this->table;
        if (isset($data["conditions"])) {
            $sql .= " WHERE ";
            $args = [];
            foreach ($data["conditions"] as $k => $v) {
                $sql .= $k . "? AND ";
                $args[] = $v;
            }
            $sql = substr($sql, 0, -5);
        }
        return $this->sendRequest($sql, $args);
        //Exemple : DELETE FROM USERS WHERE id = 1 with args = array(1)
    }

    public function getErrors()
    {
        return $this->_errors;
    }
}
