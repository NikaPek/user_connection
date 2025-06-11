<?php

namespace App\Services;

class DatabaseService
{
    function Connect()
    {
        $host = 'localhost'; // Your PostgreSQL host (e.g., 'localhost', '127.0.0.1', or a remote IP)
        $port = '5432';      // Default PostgreSQL port
        $dbname = 'php_projektas'; // The name of your database
        $user = 'postgres';    // Your PostgreSQL username
        $password = 'root'; // Your PostgreSQL password

        // Create a connection string
        $connectionString = "host=$host port=$port dbname=$dbname user=$user password=$password";

        // Attempt to connect to the PostgreSQL database
        $dbConnection = pg_connect($connectionString);

        if (!$dbConnection) {
            die("Error in connection: " . pg_last_error());
        }

        return $dbConnection;
    }

    function ExecuteQuery($query)
    {
        $dbConnection = $this->Connect();
        $result = pg_query($dbConnection, $query);

        if (!$result) {
            die("Error in SQL query: " . pg_last_error());
        }

        return pg_fetch_all($result);
    }



    function ExecuteDebugQuery($query)
    {
        $result = $this->ExecuteQuery($query);
        echo "result count: " . count($result) . "<br>";
        echo "<p>Query: " . htmlspecialchars($query) . "</p>";
        echo "<p>Result: " . json_encode($result) . "</p>";
    }

    function CreateUser($name, $password, $key)
    {
        $connection = $this->Connect();
        $query = "INSERT INTO users (name, password, key) VALUES ('$name', '$password', '$key')";
        $result = pg_exec($connection, utf8_encode($query ));

        if (!$result) {
            die("Error in SQL query: " . pg_last_error());
        }

        return true;
    }

    function UserExists($name)
    {
        $query = "SELECT * FROM users WHERE name = '$name'";
        $result = $this->ExecuteQuery($query);

//        if (!$result) {
//            die("Error in SQL query: " . pg_last_error());
//        }

        return count($result) > 0;
    }

    function LoginUser($name, $password)
    {
        $query = "SELECT * FROM users WHERE name = '$name'";
        $result = $this->ExecuteQuery($query);




        if (empty($result)) {
            return false; // User not found
        } else {
            $user = $result[0];
            $userPassword = $user['password'];
            if (password_verify($password, $userPassword)) {
                return true; // Password matches
            } else {
                return false; // Password does not match
            }
        }
    }

    function GetUser($userName)
    {
        $query = "SELECT * FROM users WHERE name = '$userName'";
        $result = $this->ExecuteQuery($query);

        if (!$result) {
            die("Error in SQL query: " . pg_last_error());
        }

        if (empty($result)) {
            return null; // User not found
        } else {
            return $result[0]; // Return the first user found
        }
    }

    function CreateUserPassword($userName, $url, $password)
    {
        $PasswordService = new PasswordService();
        $connection = $this->Connect();
        $user = $this->GetUser($userName);
        $createdOn = date('Y-m-d H:i:s');
        $userId = $user['id'];
        $userKey = $user['key'];
        $encodedPassword = $PasswordService->EncryptAes($password, $userKey); // Encode the password for storage

        $query = "INSERT INTO userpasswords (url, createdon, password, createdbyuser) VALUES ('$url', '$createdOn', '$encodedPassword', '$userId')";
        $result = pg_exec($connection, utf8_encode($query));

        if (!$result) {
            die("Error in SQL query: " . pg_last_error());
        }

        return true;
    }

    function GetUserPasswords($userName)
    {
        $user = $this->GetUser($userName);
        if (!$user) {
            return []; // User not found
        }

        $userId = $user['id'];
        $query = "SELECT up.url, up.createdon, up.password, u.name, u.key FROM userpasswords up INNER JOIN public.users u on up.createdbyuser = u.id WHERE up.createdbyuser = '$userId'";
        return $this->ExecuteQuery($query); // Return all passwords for the user
    }
}