<?php
// Arquivo: app/controllers/VagaController.php
// TalentsHUB - Controller de Vagas

require_once __DIR__ . '/../utils/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            createVaga();
            break;
        case 'update':
            updateVaga();
            break;
        case 'delete':
            deleteVaga();
            break;
        case 'toggle_status':
            toggleStatusVaga();
            break;
        default:
            header('Location: ' . BASE_DIR . '/app/views/gerenciar_vagas.php');
            exit();
    }
} else {
    // GET requests
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'view':
            viewVaga();
            break;
        case 'search':
            searchVagas();
            break;
        default:
            header('Location: ' . BASE_DIR . '/app/views/vagas.php');
            exit();
    }
}

function createVaga() {
    // Verificar se é empresa
    if ($_SESSION['usuario_tipo'] !== 'empresa') {
        $_SESSION['vaga_erro'] = "Apenas empresas podem criar vagas.";
        header('Location: ' . BASE_DIR . '/app/views/criar_vaga.php');
        exit();
    }
    
    $vagaModel = new Vaga();
    $empresaModel = new Empresa();
    
    // Buscar ID da empresa
    $empresa = $empresaModel->findByUsuarioId($_SESSION['usuario_id']);
    if (!$empresa) {
        $_SESSION['vaga_erro'] = "Empresa não encontrada.";
        header('Location: ' . BASE_DIR . '/app/views/criar_vaga.php');
        exit();
    }
    
    error_log("Debug - Usuario ID: " . $_SESSION['usuario_id'] . ", Empresa ID: " . $empresa['id']);
    
    // Validar dados
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $requisitos = trim($_POST['requisitos'] ?? '');
    $beneficios = trim($_POST['beneficios'] ?? '');
    $salario_min = !empty($_POST['salario_min']) ? floatval($_POST['salario_min']) : null;
    $salario_max = !empty($_POST['salario_max']) ? floatval($_POST['salario_max']) : null;
    $tipo_contrato = $_POST['tipo_contrato'] ?? '';
    $modalidade = $_POST['modalidade'] ?? '';
    $nivel_experiencia = $_POST['nivel_experiencia'] ?? '';
    $area = trim($_POST['area'] ?? '');
    $localizacao = trim($_POST['localizacao'] ?? '');
    $data_limite = $_POST['data_limite'] ?? null;
    
    // Validações
    if (empty($titulo) || empty($descricao) || empty($requisitos) || empty($localizacao)) {
        $_SESSION['vaga_erro'] = "Por favor, preencha todos os campos obrigatórios.";
        header('Location: ' . BASE_DIR . '/app/views/criar_vaga.php');
        exit();
    }
    
    if (!in_array($tipo_contrato, ['clt', 'pj', 'estagio', 'trainee', 'freelancer'])) {
        $_SESSION['vaga_erro'] = "Tipo de contrato inválido.";
        header('Location: ' . BASE_DIR . '/app/views/criar_vaga.php');
        exit();
    }
    
    if (!in_array($modalidade, ['presencial', 'remoto', 'hibrido'])) {
        $_SESSION['vaga_erro'] = "Modalidade inválida.";
        header('Location: ' . BASE_DIR . '/app/views/criar_vaga.php');
        exit();
    }
    
    if (!in_array($nivel_experiencia, ['estagiario', 'junior', 'pleno', 'senior', 'especialista'])) {
        $_SESSION['vaga_erro'] = "Nível de experiência inválido.";
        header('Location: ' . BASE_DIR . '/app/views/criar_vaga.php');
        exit();
    }
    
    // Criar vaga
    $result = $vagaModel->create($empresa['id'], $titulo, $descricao, $requisitos, $beneficios, $salario_min, $salario_max, $tipo_contrato, $modalidade, $nivel_experiencia, $area, $localizacao, $data_limite);
    error_log("Debug - Resultado da criação da vaga: " . ($result ? 'SUCESSO' : 'FALHA'));
    
    if ($result) {
        $_SESSION['vaga_sucesso'] = "Vaga publicada com sucesso!";
        header('Location: ' . BASE_DIR . '/app/views/gerenciar_vagas.php');
        exit();
    } else {
        $_SESSION['vaga_erro'] = "Erro ao publicar vaga. Tente novamente.";
        header('Location: ' . BASE_DIR . '/app/views/criar_vaga.php');
        exit();
    }
}

function updateVaga() {
    // Verificar se é empresa
    if ($_SESSION['usuario_tipo'] !== 'empresa') {
        $_SESSION['vaga_erro'] = "Apenas empresas podem editar vagas.";
        header('Location: ' . BASE_DIR . '/app/views/gerenciar_vagas.php');
        exit();
    }
    
    $vagaModel = new Vaga();
    $empresaModel = new Empresa();
    
    $vaga_id = intval($_POST['id'] ?? 0);
    if (!$vaga_id) {
        $_SESSION['vaga_erro'] = "ID da vaga inválido.";
        header('Location: ' . BASE_DIR . '/app/views/gerenciar_vagas.php');
        exit();
    }
    
    // Verificar se a vaga pertence à empresa
    $vaga = $vagaModel->findById($vaga_id);
    if (!$vaga) {
        $_SESSION['vaga_erro'] = "Vaga não encontrada.";
        header('Location: ' . BASE_DIR . '/app/views/gerenciar_vagas.php');
        exit();
    }
    
    $empresa = $empresaModel->findByUsuarioId($_SESSION['usuario_id']);
    if (!$empresa || $vaga['empresa_id'] != $empresa['id']) {
        $_SESSION['vaga_erro'] = "Você não tem permissão para editar esta vaga.";
        header('Location: ' . BASE_DIR . '/app/views/gerenciar_vagas.php');
        exit();
    }
    
    // Validar dados (mesmo processo do create)
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $requisitos = trim($_POST['requisitos'] ?? '');
    $beneficios = trim($_POST['beneficios'] ?? '');
    $salario_min = !empty($_POST['salario_min']) ? floatval($_POST['salario_min']) : null;
    $salario_max = !empty($_POST['salario_max']) ? floatval($_POST['salario_max']) : null;
    $tipo_contrato = $_POST['tipo_contrato'] ?? '';
    $modalidade = $_POST['modalidade'] ?? '';
    $nivel_experiencia = $_POST['nivel_experiencia'] ?? '';
    $area = trim($_POST['area'] ?? '');
    $localizacao = trim($_POST['localizacao'] ?? '');
    $data_limite = $_POST['data_limite'] ?? null;
    $status = $_POST['status'] ?? 'ativa';
    
    if (empty($titulo) || empty($descricao) || empty($requisitos) || empty($localizacao)) {
        $_SESSION['vaga_erro'] = "Por favor, preencha todos os campos obrigatórios.";
        header('Location: ' . BASE_DIR . '/app/views/editar_vaga.php?id=' . $vaga_id);
        exit();
    }
    
    // Atualizar vaga
    if ($vagaModel->update($vaga_id, $empresa['id'], $titulo, $descricao, $requisitos, $beneficios, $salario_min, $salario_max, $tipo_contrato, $modalidade, $nivel_experiencia, $area, $localizacao, $data_limite, $status)) {
        $_SESSION['vaga_sucesso'] = "Vaga atualizada com sucesso!";
        header('Location: ' . BASE_DIR . '/app/views/gerenciar_vagas.php');
        exit();
    } else {
        $_SESSION['vaga_erro'] = "Erro ao atualizar vaga. Tente novamente.";
        header('Location: ' . BASE_DIR . '/app/views/editar_vaga.php?id=' . $vaga_id);
        exit();
    }
}

function deleteVaga() {
    // Verificar se é empresa
    if ($_SESSION['usuario_tipo'] !== 'empresa') {
        $_SESSION['vaga_erro'] = "Apenas empresas podem deletar vagas.";
        header('Location: ' . BASE_DIR . '/app/views/gerenciar_vagas.php');
        exit();
    }
    
    $vagaModel = new Vaga();
    $empresaModel = new Empresa();
    
    $vaga_id = intval($_POST['id'] ?? 0);
    if (!$vaga_id) {
        $_SESSION['vaga_erro'] = "ID da vaga inválido.";
        header('Location: ' . BASE_DIR . '/app/views/gerenciar_vagas.php');
        exit();
    }
    
    // Verificar se a vaga pertence à empresa
    $vaga = $vagaModel->findById($vaga_id);
    if (!$vaga) {
        $_SESSION['vaga_erro'] = "Vaga não encontrada.";
        header('Location: ' . BASE_DIR . '/app/views/gerenciar_vagas.php');
        exit();
    }
    
    $empresa = $empresaModel->findByUsuarioId($_SESSION['usuario_id']);
    if (!$empresa || $vaga['empresa_id'] != $empresa['id']) {
        $_SESSION['vaga_erro'] = "Você não tem permissão para deletar esta vaga.";
        header('Location: ' . BASE_DIR . '/app/views/gerenciar_vagas.php');
        exit();
    }
    
    // Deletar vaga
    if ($vagaModel->delete($vaga_id, $empresa['id'])) {
        $_SESSION['vaga_sucesso'] = "Vaga deletada com sucesso!";
    } else {
        $_SESSION['vaga_erro'] = "Erro ao deletar vaga. Tente novamente.";
    }
    
    header('Location: ' . BASE_DIR . '/app/views/gerenciar_vagas.php');
    exit();
}

function toggleStatusVaga() {
    // Verificar se é empresa
    if ($_SESSION['usuario_tipo'] !== 'empresa') {
        $_SESSION['vaga_erro'] = "Apenas empresas podem alterar status de vagas.";
        header('Location: ' . BASE_DIR . '/app/views/gerenciar_vagas.php');
        exit();
    }
    
    $vagaModel = new Vaga();
    $empresaModel = new Empresa();
    
    $vaga_id = intval($_POST['vaga_id'] ?? $_POST['id'] ?? 0);
    $novo_status = $_POST['status'] ?? '';
    
    if (!$vaga_id || !in_array($novo_status, ['ativa', 'pausada', 'finalizada'])) {
        $_SESSION['vaga_erro'] = "Dados inválidos.";
        header('Location: ' . BASE_DIR . '/app/views/gerenciar_vagas.php');
        exit();
    }
    
    // Verificar se a vaga pertence à empresa
    $vaga = $vagaModel->findById($vaga_id);
    if (!$vaga) {
        $_SESSION['vaga_erro'] = "Vaga não encontrada.";
        header('Location: ' . BASE_DIR . '/app/views/gerenciar_vagas.php');
        exit();
    }
    
    $empresa = $empresaModel->findByUsuarioId($_SESSION['usuario_id']);
    if (!$empresa || $vaga['empresa_id'] != $empresa['id']) {
        $_SESSION['vaga_erro'] = "Você não tem permissão para alterar esta vaga.";
        header('Location: ' . BASE_DIR . '/app/views/gerenciar_vagas.php');
        exit();
    }
    
    // Atualizar status
    if ($vagaModel->update($vaga_id, $empresa['id'], $vaga['titulo'], $vaga['descricao'], $vaga['requisitos'], $vaga['beneficios'], $vaga['salario_min'], $vaga['salario_max'], $vaga['tipo_contrato'], $vaga['modalidade'], $vaga['nivel_experiencia'], $vaga['area'], $vaga['localizacao'], $vaga['data_limite'], $novo_status)) {
        $_SESSION['vaga_sucesso'] = "Status da vaga atualizado com sucesso!";
    } else {
        $_SESSION['vaga_erro'] = "Erro ao atualizar status da vaga.";
    }
    
    header('Location: ' . BASE_DIR . '/app/views/gerenciar_vagas.php');
    exit();
}

function viewVaga() {
    $vaga_id = intval($_GET['id'] ?? 0);
    if (!$vaga_id) {
        header('Location: ' . BASE_DIR . '/app/views/vagas.php');
        exit();
    }
    
    $vagaModel = new Vaga();
    $vaga = $vagaModel->findById($vaga_id);
    
    if (!$vaga) {
        $_SESSION['vaga_erro'] = "Vaga não encontrada.";
        header('Location: ' . BASE_DIR . '/app/views/vagas.php');
        exit();
    }
    
    // Incrementar visualizações
    $vagaModel->incrementViews($vaga_id);
    
    // Redirecionar para a página de detalhes
    header('Location: ' . BASE_DIR . '/app/views/vaga_detalhes.php?id=' . $vaga_id);
    exit();
}

function searchVagas() {
    $filters = [
        'search' => $_GET['search'] ?? '',
        'area' => $_GET['area'] ?? '',
        'modalidade' => $_GET['modalidade'] ?? '',
        'nivel_experiencia' => $_GET['nivel_experiencia'] ?? '',
        'tipo_contrato' => $_GET['tipo_contrato'] ?? '',
        'localizacao' => $_GET['localizacao'] ?? ''
    ];
    
    // Remover filtros vazios
    $filters = array_filter($filters, function($value) {
        return !empty($value);
    });
    
    // Redirecionar para a página de vagas com filtros
    $query_string = http_build_query($filters);
    header('Location: ' . BASE_DIR . '/app/views/vagas.php?' . $query_string);
    exit();
}
?>