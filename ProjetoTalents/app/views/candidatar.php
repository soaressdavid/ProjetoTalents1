<?php
require_once __DIR__ . '/../utils/init.php';

// Redireciona se o usuário não for um candidato
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'candidato') {
    header('Location: ../auth.php');
    exit();
}

// Verifica se o ID da vaga foi passado na URL
if (!isset($_GET['vaga_id']) || !is_numeric($_GET['vaga_id'])) {
    header('Location: vagas.php');
    exit();
}

$vaga_id = $_GET['vaga_id'];

// Inclui os modelos necessários
$vagaModel = new Vaga();
$userModel = new User();

// Busca os dados da vaga e do candidato logado
$vaga = $vagaModel->findById($vaga_id);
$candidato = $userModel->findById($_SESSION['usuario_id']);

if (!$vaga) {
    header('Location: vagas.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatar-se à Vaga: <?php echo htmlspecialchars($vaga['titulo']); ?></title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>

    <header class="header">
        <div class="container">
            <h1 class="logo">JobFind</h1>
            <nav class="nav">
                <a href="vagas.php">Vagas</a>
                <a href="painel_candidato.php">Painel</a>
                <a href="../auth.php?action=logout">Sair</a>
            </nav>
        </div>
    </header>

    <main class="container form-container">
        <h2>Candidatar-se a: <?php echo htmlspecialchars($vaga['titulo']); ?></h2>

        <?php if (isset($_SESSION['candidatura_erro'])): ?>
            <p class="erro"><?php echo $_SESSION['candidatura_erro']; unset($_SESSION['candidatura_erro']); ?></p>
        <?php endif; ?>

        <form action="../controllers/CandidaturaController.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="apply">
            <input type="hidden" name="vaga_id" value="<?php echo htmlspecialchars($vaga_id); ?>">

            <div class="form-group">
                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($candidato['nome']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($candidato['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="curriculo">Currículo (PDF, DOC ou DOCX)</label>
                <input type="file" id="curriculo" name="curriculo" accept=".pdf,.doc,.docx" required>
            </div>

            <div class="form-group">
                <label for="mensagem">Mensagem para o Empregador (Opcional)</label>
                <textarea id="mensagem" name="mensagem" rows="5"></textarea>
            </div>

            <button type="submit" class="button button-primary">Enviar Candidatura</button>
        </form>
    </main>

</body>
</html>