<?php
// Arquivo: app/views/vagas_detalhes.php

require_once __DIR__ . '/../utils/init.php';

if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: " . BASE_DIR . "/app/views/vagas.php");
    exit();
}

$vagaModel = new Vaga();
$vaga_id = $_GET['id'];

$vaga = $vagaModel->findById($vaga_id);

if (!$vaga) {
    echo "<h1>Vaga não encontrada.</h1>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($vaga['titulo']); ?> - Detalhes</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>

    <div class="header">
        <h1><?php echo htmlspecialchars($vaga['titulo']); ?></h1>
        <div class="auth-links">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <?php if ($_SESSION['usuario_tipo'] === 'candidato'): ?>
                    <a href="../../app/views/painel_candidato.php">Painel do Candidato</a>
                <?php elseif ($_SESSION['usuario_tipo'] === 'empresa'): ?>
                    <a href="../../app/views/painel_empresa.php">Painel da Empresa</a>
                <?php endif; ?>
                <a href="../../app/controllers/LogoutController.php">Sair</a>
            <?php else: ?>
                <a href="../../app/views/auth.php">Login / Cadastro</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="job-details-container">
        <?php
        if (isset($_SESSION['candidatura_sucesso'])) {
            echo '<p class="sucesso">' . $_SESSION['candidatura_sucesso'] . '</p>';
            unset($_SESSION['candidatura_sucesso']);
        }
        if (isset($_SESSION['candidatura_erro'])) {
            echo '<p class="erro">' . $_SESSION['candidatura_erro'] . '</p>';
            unset($_SESSION['candidatura_erro']);
        }
        ?>
        <div class="job-card">
            <h3>Detalhes da Vaga</h3>
            <p><strong>Empresa:</strong> <?php echo htmlspecialchars($vaga['nome_empresa']); ?></p>
            <p><strong>Localização:</strong> <?php echo htmlspecialchars($vaga['localizacao']); ?></p>
            <p><strong>Descrição:</strong></p>
            <p><?php echo nl2br(htmlspecialchars($vaga['descricao'])); ?></p>
            <p><strong>Requisitos:</strong></p>
            <p><?php echo nl2br(htmlspecialchars($vaga['requisitos'])); ?></p>
        </div>
        
        <?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'candidato'): ?>
            <form action="../controllers/CandidaturaController.php" method="POST">
                <input type="hidden" name="vaga_id" value="<?php echo htmlspecialchars($vaga['id']); ?>">
                <input type="submit" value="Candidatar-se a esta Vaga">
            </form>
        <?php endif; ?>
        
        <a href="vagas.php" class="back-link">Voltar para a lista de vagas</a>
    </div>

</body>
</html>