<?php

class Database {

    private $mysqli;

    public function __construct() {
        $cleardb_url = parse_url(getenv("CLEARDB_DATABASE_URL"));
        $cleardb_server = $cleardb_url["host"];
        $cleardb_username = $cleardb_url["user"];
        $cleardb_password = $cleardb_url["pass"];
        $cleardb_db = substr($cleardb_url["path"],1);
        echo $$cleardb_server;
        echo $cleardb_username;
        echo $cleardb_password;
        echo $cleardb_db;

        $active_group = 'default';
        $query_builder = TRUE;

        if($mysqli = mysqli_connect($cleardb_server, $cleardb_username, $cleardb_password, $cleardb_db)) {
            echo "<br>";
            echo "connection";
        } else {
            echo "<br>";
            echo "no connection";
        }

    }


    function __destruct() {
        $this->mysqli->close();
    }

    public function selectUser($username, $password, $table) {
        $id = -1;
        $sql = "SELECT * FROM $table WHERE username='$username' OR email='$username'";

        if ($result = $this->mysqli->query($sql)) {
            $ile = $result->num_rows;

            if ($ile == 1) {
                $row = $result->fetch_object();
                $hash = $row->password;
                if (password_verify($password, $hash))
                    $id = $row->user_id;
            }
        }

        return $id;
    }

    //custom function taking two arguments, first - sql query (string), second - name of record's column we want to return (string), function without second argument will return row from table
    public function select($sql, $var = false) {
        $returnValue = -1;
        
        if ($result = $this->mysqli->query($sql)) {
            $ile = $result->num_rows;
            if ($ile == 1) {
                $row = $result->fetch_object();
                if($var != false){
                    $returnValue = $row->$var;
                } else {
                    $returnValue = $row;
                }     
            }
        }

        return $returnValue;
    }

    public function selectElements($sql) {
        $dbElements = array();

        if ($result = $this->mysqli->query($sql)) {
            $ile = $result->num_rows;
            while ($row = $result->fetch_object()) {
                $dbElements[] = $row;
            }
            $result->close();
        }

        return $dbElements;
        //print_r($dbElements);
        //echo "</br>";
        //print_r($dbElements[0]->subject_id);
    }
    
    public function insert($sql) {
        $result = $this->mysqli->query($sql);
        if (!$result) {
            echo "Nie udało się dodać rekordu do bazy danych";
        }
    }

    public function delete($sql) {
        $result = $this->mysqli->query($sql);
        if (!$result) {
            echo "Nie udało się usunąć rekordu z bazy danych";
        }
    }

    public function update($sql) {
        $result = $this->mysqli->query($sql);
        if (!$result) {
            echo "Nie udało się zaktualizować rekordu z bazy danych";
        }
    }
}


?>