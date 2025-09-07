<?php
// Arquivo: app/controllers/CandidaturaController.php
// TalentsHUB - Controller de Candidaturas

require_once __DIR__ . '/../utils/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'apply':
            applyToJob();
            break;
        case 'update_status':
            updateCandidaturaStatus();
            break;
        case 'delete':
            deleteCandidatura();
            break;
        default:
            header('Location: ' . BASE_DIR . '/app/views/vagas.php');
            exit();
    }
} else {
    // GET requests
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'view':
            viewCandidatura();
            break;
        default:
            header('Location: ' . BASE_DIR . '/app/views/vagas.php');
            exit();
    }
}

function applyToJob() {
    // Verificar se é candidato
    if ($_SESSION['usuario_tipo'] !== 'candidato') {
        $_SESSION['candidatura_erro'] = "Apenas candidatos podem se candidatar a vagas.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    $vaga_id = intval($_POST['vaga_id'] ?? 0);
    $candidato_id = $_SESSION['usuario_id'] ?? null;

    if (!$vaga_id || !$candidato_id) {
        $_SESSION['candidatura_erro'] = "Dados de candidatura incompletos.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $candidaturaModel = new Candidatura();
    $candidatoModel = new Candidato();
    $vagaModel = new Vaga();

    // Buscar ID do candidato
    $candidato = $candidatoModel->findByUsuarioId($candidato_id);
    if (!$candidato) {
        $_SESSION['candidatura_erro'] = "Perfil de candidato não encontrado.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Verificar se a vaga existe e está ativa
    $vaga = $vagaModel->findById($vaga_id);
    if (!$vaga || $vaga['status'] !== 'ativa') {
        $_SESSION['candidatura_erro'] = "Vaga não encontrada ou não está mais ativa.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Verifica se o candidato já se candidatou a essa vaga
    if ($candidaturaModel->checkIfAlreadyApplied($candidato['id'], $vaga_id)) {
        $_SESSION['candidatura_erro'] = "Você já se candidatou para esta vaga.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Lida com o upload do currículo
    $curriculo_path = null;
    if (isset($_FILES['curriculo']) && $_FILES['curriculo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../public/uploads/curriculos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Validar tipo de arquivo
        $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $file_type = $_FILES['curriculo']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['candidatura_erro'] = "Por favor, envie um arquivo PDF ou DOC válido.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Validar tamanho (máximo 5MB)
        if ($_FILES['curriculo']['size'] > 5 * 1024 * 1024) {
            $_SESSION['candidatura_erro'] = "O arquivo deve ter no máximo 5MB.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $curriculo_name = uniqid('cv_') . '_' . basename($_FILES['curriculo']['name']);
        $curriculo_path = $upload_dir . $curriculo_name;
        
        if (!move_uploaded_file($_FILES['curriculo']['tmp_name'], $curriculo_path)) {
            $_SESSION['candidatura_erro'] = "Erro ao fazer upload do currículo.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
    } else {
        $_SESSION['candidatura_erro'] = "Por favor, selecione um currículo válido.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $carta_apresentacao = trim($_POST['carta_apresentacao'] ?? '');

    // Salva a candidatura no banco de dados
    if ($candidaturaModel->create($vaga_id, $candidato['id'], $curriculo_path, $carta_apresentacao)) {
        $_SESSION['candidatura_sucesso'] = "Candidatura enviada com sucesso! A empresa será notificada.";
        header('Location: ' . BASE_DIR . '/app/views/vaga_detalhes.php?id=' . $vaga_id);
        exit();
    } else {
        // Se falhou, remover arquivo enviado
        if ($curriculo_path && file_exists($curriculo_path)) {
            unlink($curriculo_path);
        }
        $_SESSION['candidatura_erro'] = "Erro ao enviar candidatura. Tente novamente.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}

function updateCandidaturaStatus() {
    // Verificar se é empresa
    if ($_SESSION['usuario_tipo'] !== 'empresa') {
        $_SESSION['candidatura_erro'] = "Apenas empresas podem alterar status de candidaturas.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    $candidatura_id = intval($_POST['candidatura_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $observacoes = trim($_POST['observacoes'] ?? '');
    
    if (!$candidatura_id || !in_array($status, ['enviada', 'visualizada', 'em_analise', 'aprovada', 'rejeitada', 'entrevista_agendada'])) {
        $_SESSION['candidatura_erro'] = "Dados inválidos.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    $candidaturaModel = new Candidatura();
    $empresaModel = new Empresa();
    
    // Verificar se a candidatura pertence à empresa
    $candidatura = $candidaturaModel->findById($candidatura_id);
    if (!$candidatura) {
        $_SESSION['candidatura_erro'] = "Candidatura não encontrada.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    $empresa = $empresaModel->findByUsuarioId($_SESSION['usuario_id']);
    if (!$empresa || $candidatura['empresa_id'] != $empresa['id']) {
        $_SESSION['candidatura_erro'] = "Você não tem permissão para alterar esta candidatura.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    // Atualizar status
    if ($candidaturaModel->updateStatus($candidatura_id, $status, $observacoes)) {
        $_SESSION['candidatura_sucesso'] = "Status da candidatura atualizado com sucesso!";
    } else {
        $_SESSION['candidatura_erro'] = "Erro ao atualizar status da candidatura.";
    }
    
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

function deleteCandidatura() {
    // Verificar se é candidato
    if ($_SESSION['usuario_tipo'] !== 'candidato') {
        $_SESSION['candidatura_erro'] = "Apenas candidatos podem cancelar candidaturas.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    $candidatura_id = intval($_POST['candidatura_id'] ?? 0);
    $candidato_id = $_SESSION['usuario_id'] ?? null;
    
    if (!$candidatura_id || !$candidato_id) {
        $_SESSION['candidatura_erro'] = "Dados inválidos.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    $candidaturaModel = new Candidatura();
    $candidatoModel = new Candidato();
    
    // Buscar ID do candidato
    $candidato = $candidatoModel->findByUsuarioId($candidato_id);
    if (!$candidato) {
        $_SESSION['candidatura_erro'] = "Perfil de candidato não encontrado.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    // Verificar se a candidatura pertence ao candidato
    $candidatura = $candidaturaModel->findById($candidatura_id);
    if (!$candidatura || $candidatura['candidato_id'] != $candidato['id']) {
        $_SESSION['candidatura_erro'] = "Candidatura não encontrada ou não pertence a você.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    // Deletar candidatura
    if ($candidaturaModel->delete($candidatura_id, $candidato['id'])) {
        // Remover arquivo de currículo se existir
        if ($candidatura['curriculo_path'] && file_exists($candidatura['curriculo_path'])) {
            unlink($candidatura['curriculo_path']);
        }
        $_SESSION['candidatura_sucesso'] = "Candidatura cancelada com sucesso!";
    } else {
        $_SESSION['candidatura_erro'] = "Erro ao cancelar candidatura.";
    }
    
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

function viewCandidatura() {
    $candidatura_id = intval($_GET['id'] ?? 0);
    if (!$candidatura_id) {
        header('Location: ' . BASE_DIR . '/app/views/vagas.php');
        exit();
    }
    
    $candidaturaModel = new Candidatura();
    $candidatura = $candidaturaModel->findById($candidatura_id);
    
    if (!$candidatura) {
        $_SESSION['candidatura_erro'] = "Candidatura não encontrada.";
        header('Location: ' . BASE_DIR . '/app/views/vagas.php');
        exit();
    }
    
    // Verificar permissões
    $empresaModel = new Empresa();
    $candidatoModel = new Candidato();
    
    $can_view = false;
    
    if ($_SESSION['usuario_tipo'] === 'empresa') {
        $empresa = $empresaModel->findByUsuarioId($_SESSION['usuario_id']);
        $can_view = $empresa && $candidatura['empresa_id'] == $empresa['id'];
    } elseif ($_SESSION['usuario_tipo'] === 'candidato') {
        $candidato = $candidatoModel->findByUsuarioId($_SESSION['usuario_id']);
        $can_view = $candidato && $candidatura['candidato_id'] == $candidato['id'];
    } elseif ($_SESSION['usuario_tipo'] === 'admin') {
        $can_view = true;
    }
    
    if (!$can_view) {
        $_SESSION['candidatura_erro'] = "Você não tem permissão para visualizar esta candidatura.";
        header('Location: ' . BASE_DIR . '/app/views/vagas.php');
        exit();
    }
    
    // Redirecionar para a página de visualização
    header('Location: ' . BASE_DIR . '/app/views/ver_candidatura.php?id=' . $candidatura_id);
    exit();
}
?>