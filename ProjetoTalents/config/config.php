<?php
// Arquivo: config/config.php
// TalentsHUB - Configurações do Sistema

$servidor = "localhost";
$usuario = "root";
$senha = ""; // Use uma senha forte e considere variáveis de ambiente em produção
$banco = "talentshub_db";

// As variáveis são agora um array associativo para facilitar o uso
$dbConfig = [
    'host' => $servidor,
    'user' => $usuario,
    'pass' => $senha,
    'dbname' => $banco
];

?>