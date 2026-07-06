<?php
class Conexao
{

    static $con = null;

    public static function getConnection()
    {
        $ip = "localhost";
        $port = "3306";
        $user = "root";
        $pass = "";
        $db = "banco_talentos";

        if (!self::$con) {
            self::$con = new mysqli($ip, $user, $pass, $db, $port);
            self::$con->set_charset("utf8mb4");

            if (self::$con->connect_error) {
                echo self::$con->connect_error;
                die();
            }
        }
        return self::$con;
    }
}