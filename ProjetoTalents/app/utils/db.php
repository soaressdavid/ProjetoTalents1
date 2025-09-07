<?php
// Arquivo: app/utils/db.php

require_once __DIR__ . '/../../config/config.php';

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        global $dbConfig;
        $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8";
        try {
            $this->conn = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Em produção, registre o erro em um log e mostre uma mensagem genérica
            error_log("Erro de Conexão com o Banco de Dados: " . $e->getMessage());
            die("Desculpe, ocorreu um erro na conexão com o banco de dados.");
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}

// Função para obter a conexão
function getDbConnection() {
    return Database::getInstance()->getConnection();
}
?>