<?php
// Arquivo: app/controllers/PerfilCandidatoController.php

require_once __DIR__ . '/../utils/init.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'candidato') {
    header("Location: " . BASE_DIR . "/app/views/auth.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $candidato_id = $_SESSION['usuario_id'];
    $titulo_perfil = $_POST['titulo_perfil'] ?? '';
    $resumo = $_POST['resumo'] ?? '';
    $experiencia = $_POST['experiencia'] ?? '';
    $educacao = $_POST['educacao'] ?? '';

    $perfilModel = new PerfilCandidato();

    if ($perfilModel->save($candidato_id, $titulo_perfil, $resumo, $experiencia, $educacao)) {
        $_SESSION['perfil_sucesso'] = "Perfil atualizado com sucesso!";
    } else {
        $_SESSION['perfil_erro'] = "Erro ao salvar o perfil. Tente novamente.";
    }

    header("Location: " . BASE_DIR . "/app/views/completar_perfil.php");
    exit();

} else {
    header("Location: " . BASE_DIR . "/app/views/painel_candidato.php");
    exit();
}
?>