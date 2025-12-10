<?php

require_once __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

class Database
{
    private $db;

    public function __construct()
    {
        $dsn = "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'] . ";charset=utf8mb4";

        try {
            $this->db = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

        } catch (PDOException $e) {
            error_log(
                "Database Connection Error: "
                . $e->getMessage()
                . " - " . $e->getFile() . ":" . $e->getLine()
            );

            die("Erreur de connexion à la base de données.");
        }
    }

    public function getConnection()
    {
        return $this->db;
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
