<?php

define("DB_HOST", "localhost");
define("DB_NAME", "webtech_fall2024_madiba_quansah");

if ($_SERVER['SERVER_NAME'] == "localhost" || $_SERVER["SERVER_NAME"] == "127.0.0.1") {
    define("DB_USER", "madiba");
    define("DB_PASS", "madiba");
} else {
    define("DB_USER", "madiba.quansah");
    define("DB_PASS", "madiba123");
}

define("DB_FMT", "utf8mb4");
define("DB_ATTR", "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_FMT);
define("DB_OPTS", [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);


class Database
{
    private $conn = null;

    public function __construct($seed = true)
    {
        try {
            $this->conn = new PDO(DB_ATTR, DB_USER, DB_PASS, DB_OPTS);
            if ($seed) {
                $setup = file_get_contents(__DIR__."/./db.sql");
                $this->conn->exec($setup);
            }
        } catch (PDOException $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["error" => $e->getMessage()]);
            die();
        }
    }

    public function Conn()
    {
        return $this->conn;
    }
}

$db = (new Database(false))->Conn();
