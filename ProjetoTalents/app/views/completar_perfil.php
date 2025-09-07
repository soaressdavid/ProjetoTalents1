<?php
// Arquivo: app/views/completar_perfil.php

require_once __DIR__ . '/../utils/init.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'candidato') {
    header("Location: " . BASE_DIR . "/app/views/auth.php");
    exit();
}

$perfilModel = new PerfilCandidato();
$perfil = $perfilModel->findByCandidatoId($_SESSION['usuario_id']);

$titulo_perfil = $perfil['titulo_perfil'] ?? '';
$resumo = $perfil['resumo'] ?? '';
$experiencia = $perfil['experiencia'] ?? '';
$educacao = $perfil['educacao'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completar Perfil</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>

    <div class="form-container">
        <h2>Completar Perfil Profissional</h2>
        <p>Preencha os campos abaixo para completar seu perfil de candidato.</p>

        <?php
        if (isset($_SESSION['perfil_sucesso'])) {
            echo '<p class="sucesso">' . htmlspecialchars($_SESSION['perfil_sucesso']) . '</p>';
            unset($_SESSION['perfil_sucesso']);
        }
        if (isset($_SESSION['perfil_erro'])) {
            echo '<p class="erro">' . htmlspecialchars($_SESSION['perfil_erro']) . '</p>';
            unset($_SESSION['perfil_erro']);
        }
        ?>

        <form action="../controllers/PerfilCandidatoController.php" method="POST">
            <label for="titulo_perfil">Título do Perfil:</label>
            <input type="text" id="titulo_perfil" name="titulo_perfil" value="<?php echo htmlspecialchars($titulo_perfil); ?>" required>

            <label for="resumo">Resumo Profissional:</label>
            <textarea id="resumo" name="resumo" rows="5" required><?php echo htmlspecialchars($resumo); ?></textarea>

            <label for="experiencia">Experiência Profissional:</label>
            <textarea id="experiencia" name="experiencia" rows="5" required><?php echo htmlspecialchars($experiencia); ?></textarea>
            
            <label for="educacao">Educação e Formação:</label>
            <textarea id="educacao" name="educacao" rows="5" required><?php echo htmlspecialchars($educacao); ?></textarea>
            
            <input type="submit" value="Salvar Perfil">
        </form>
    </div>

</body>
</html>