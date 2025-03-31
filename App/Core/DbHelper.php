<?php

namespace App\Core;

use mysqli;
use mysqli_result;

class DbHelper
{
    public function connect(): mysqli
    {
        $servername = "localhost";
        $username = "vartotojas";
        $password = '1111';
        $dbname = "mysql_duomsaug";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } else {
            return $conn;
        }
    }

    public function runQuery($conn, $query)
    {
        return mysqli_query($conn, $query);
    }

    public function createUser($conn, $name, $email, $password)
    {
        $sql = "INSERT INTO user (name, email, password) VALUES ('$name', '$email', '$password')";

        if (!$this->userExists($conn, $email))
        {
            $this->runQuery($conn, $sql);
        }
    }

    public function userExists($conn, $email): bool
    {
        $sql = "SELECT * FROM user WHERE email='$email' LIMIT 1";

        $userFound = $this->runQuery($conn, $sql)->fetch_assoc();

        if (empty($userFound))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function getUser($conn, $email): array
    {
        $sql = "SELECT * FROM user WHERE email='$email' LIMIT 1";

        if ($this->userExists($conn, $email))
        {
            return $this->runQuery($conn, $sql)->fetch_assoc();
        }

        return [];
    }
}