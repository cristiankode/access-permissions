<?php

namespace App\Connection;

use PDO;
use PDOException;

class Connection 
{
    private string $server = "";
    private string $userName = "";
    private string $password = "";
    private array $options = array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);
    protected PDO $conn;

    public function __construct() 
    {
        $this->server = "mysql:host=" . $_SERVER['DB_HOST'] . ";" . "dbname=" . $_SERVER['DB_NAME'];
        $this->userName = $_SERVER['DB_USERNAME'];
        $this->password = $_SERVER['DB_PASSWORD'];
    }

    public function open()
    {
        try {
            $this->conn = new PDO($this->server, $this->userName, $this->password, $this->options);
            return $this->conn;
        } catch (PDOException $e) {
            echo "Hubo un problema con la conexión: {$e->getMessage()}"; 
        }
    }

    public function close(): void
    {
        $this->conn = null;
    }
}