<?php
// API para sugestões de localização
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../app/utils/init.php';

$termo = $_GET['termo'] ?? '';

if (empty($termo) || strlen($termo) < 2) {
    echo json_encode([]);
    exit();
}

try {
    $vagaModel = new Vaga();
    $sugestoes = $vagaModel->getLocalizacoesDisponiveis($termo);
    
    if ($sugestoes === false) {
        $sugestoes = [];
    }
    
    echo json_encode($sugestoes);
} catch (Exception $e) {
    error_log("Erro ao buscar sugestões de localização: " . $e->getMessage());
    echo json_encode([]);
}
?>
