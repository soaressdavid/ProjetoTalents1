<?php
// Arquivo: app/views/painel_admin.php

require_once __DIR__ . '/../utils/init.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo']  !== 'admin') {
    header("Location: " . BASE_DIR . "/app/views/auth.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/style.css">
    <title>Painel do Administrador</title>
</head>
<body>
    <h2>Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></h2>
    <p>Este é o seu painel de administradores. Aqui você terá controle total sobre o sistema</p>

    <a href="../../app/controllers/LogoutController.php">Sair</a>
    
</body>
</html>