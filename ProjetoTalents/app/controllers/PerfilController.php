<?php
// Arquivo: app/controllers/PerfilController.php

require_once __DIR__ . '/../utils/init.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: " . BASE_DIR . "/app/views/auth.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'update_perfil') {
    
    $id = $_SESSION['usuario_id'];
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? null;
    $usuario_tipo = $_SESSION['usuario_tipo'];

    $usuarioModel = new User();
    
    if ($usuarioModel->update($id, $nome, $email, $senha ? password_hash($senha, PASSWORD_DEFAULT) : null)) {
        $_SESSION['usuario_nome'] = $nome;
        $_SESSION['perfil_sucesso'] = "Perfil atualizado com sucesso!";
    } else {
        $_SESSION['perfil_erro'] = "Erro ao atualizar o perfil. Tente novamente.";
    }

    if ($usuario_tipo === 'candidato') {
        header("Location: " . BASE_DIR . "/app/views/editar_perfil_candidato.php");
    } elseif ($usuario_tipo === 'empresa') {
        header("Location: " . BASE_DIR . "/app/views/editar_perfil_empresa.php");
    } else {
        // Redirecionamento padrão caso o tipo de usuário não seja encontrado
        header("Location: " . BASE_DIR . "/app/views/index.php");
    }
    exit();

} else {
    // Redireciona para o painel apropriado se a requisição não for POST
    if ($_SESSION['usuario_tipo'] === 'candidato') {
        header("Location: " . BASE_DIR . "/app/views/editar_perfil_candidato.php");
    } elseif ($_SESSION['usuario_tipo'] === 'empresa') {
        header("Location: " . BASE_DIR . "/app/views/editar_perfil_empresa.php");
    } else {
        header("Location: " . BASE_DIR . "/app/views/index.php");
    }
    exit();
}
?>