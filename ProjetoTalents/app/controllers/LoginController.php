<?php
// Arquivo: app/controllers/LoginController.php

// Inclui o arquivo de inicialização para garantir que a sessão e as classes estejam disponíveis.
require_once __DIR__ . '/../utils/init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // DEBUG: Mostra o e-mail e a senha que o formulário está enviando
    // print_r("Email enviado: " . $email . "<br>");
    // print_r("Senha enviada: " . $senha . "<br>");

    if (empty($email) || empty($senha)) {
        $_SESSION['login_erro'] = "Preencha todos os campos.";
        header("Location: " . BASE_DIR . "/app/views/auth.php");
        exit();
    }

    $userModel = new User();
    $user = $userModel->findByEmail($email);
    
    // DEBUG: Mostra os dados do usuário encontrados no banco de dados
    // print_r("Dados do usuário do banco: ");
    // print_r($user);
    // echo "<br>";
    
    // DEBUG: Mostra se a senha corresponde
    // print_r("Verificação de senha: " . (password_verify($senha, $user['senha']) ? "Sucesso" : "Falha") . "<br>");

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];
        $_SESSION['usuario_tipo'] = $user['tipo_usuario'];
        $_SESSION['login_sucesso'] = "Login realizado com sucesso!";

        switch ($user['tipo_usuario']) {
            case 'candidato':
                header("Location: " . BASE_DIR . "/app/views/painel_candidato.php");
                break;
            case 'empresa':
                header("Location: " . BASE_DIR . "/app/views/painel_empresa.php");
                break;
            case 'admin':
                header("Location: " . BASE_DIR . "/app/views/painel_admin.php");
                break;
            default:
                header("Location: " . BASE_DIR . "/app/views/auth.php");
                break;
        }
        exit();
    } else {
        $_SESSION['login_erro'] = "E-mail ou senha inválidos.";
        header("Location: " . BASE_DIR . "/app/views/auth.php");
        exit();
    }
} else {
    header("Location: " . BASE_DIR . "/app/views/auth.php");
    exit();
}
?>
