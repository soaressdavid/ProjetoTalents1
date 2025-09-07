<?php
// Arquivo: app/views/gerenciar_vagas.php
// TalentsHUB - Gerenciar Vagas

require_once __DIR__ . '/../utils/init.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'empresa') {
    header("Location: " . BASE_DIR . "/app/views/auth.php");
    exit();
}

$vagaModel = new Vaga();
$candidaturaModel = new Candidatura();

// Buscar empresa primeiro
$empresaModel = new Empresa();
$empresa = $empresaModel->findByUsuarioId($_SESSION['usuario_id']);

error_log("Debug - Usuario ID: " . $_SESSION['usuario_id']);
error_log("Debug - Empresa encontrada: " . ($empresa ? 'SIM' : 'NÃO'));

if ($empresa) {
    error_log("Debug - Empresa ID: " . $empresa['id']);
    $vagas = $vagaModel->getByEmpresa($empresa['id']);
    error_log("Debug - Vagas retornadas: " . (is_array($vagas) ? count($vagas) : 'NÃO É ARRAY'));
} else {
    error_log("Debug - Empresa não encontrada, definindo vagas como array vazio");
    $vagas = [];
}

// Verificar se a consulta retornou dados válidos
if ($vagas === false) {
    error_log("Debug - Vagas retornou false, convertendo para array vazio");
    $vagas = [];
}

error_log("Debug - Vagas final: " . (is_array($vagas) ? count($vagas) : 'NÃO É ARRAY'));

// Buscar estatísticas das vagas
$stats = [
    'total_vagas' => count($vagas),
    'vagas_ativas' => count(array_filter($vagas, fn($v) => $v['status'] === 'ativa')),
    'vagas_pausadas' => count(array_filter($vagas, fn($v) => $v['status'] === 'pausada')),
    'vagas_finalizadas' => count(array_filter($vagas, fn($v) => $v['status'] === 'finalizada')),
    'total_candidaturas' => 0
];

// Calcular total de candidaturas
foreach ($vagas as $vaga) {
    $candidaturas_vaga = $candidaturaModel->findByVagaId($vaga['id']);
    if ($candidaturas_vaga !== false) {
        $stats['total_candidaturas'] += count($candidaturas_vaga);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Vagas - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Gerencie suas vagas publicadas no TalentsHUB.">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_DIR; ?>/public/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_DIR; ?>/public/images/favicon.ico">
    
    <style>
        .vaga-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
            border-left: 4px solid var(--primary-color);
        }
        
        .vaga-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .vaga-header {
            display: flex;
            justify-content: between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .vaga-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
            flex: 1;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-left: 1rem;
        }
        
        .status-ativa { background: #d1fae5; color: #065f46; }
        .status-pausada { background: #fef3c7; color: #92400e; }
        .status-finalizada { background: #fee2e2; color: #991b1b; }
        
        .vaga-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            color: var(--secondary-color);
            font-size: 0.875rem;
        }
        
        .info-item i {
            margin-right: 0.5rem;
            color: var(--primary-color);
            width: 16px;
        }
        
        .vaga-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }
        
        .btn-view {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-view:hover {
            background: var(--primary-dark);
            color: white;
        }
        
        .btn-edit {
            background: #3b82f6;
            color: white;
        }
        
        .btn-edit:hover {
            background: #2563eb;
            color: white;
        }
        
        .btn-candidates {
            background: #10b981;
            color: white;
        }
        
        .btn-candidates:hover {
            background: #059669;
            color: white;
        }
        
        .btn-delete {
            background: #ef4444;
            color: white;
        }
        
        .btn-delete:hover {
            background: #dc2626;
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--border-color);
            margin-bottom: 1.5rem;
        }
        
        .empty-state h3 {
            color: var(--dark-color);
            margin-bottom: 1rem;
        }
        
        .empty-state p {
            color: var(--secondary-color);
            margin-bottom: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.25rem;
            color: white;
        }
        
        .stat-icon.primary { background: var(--primary-color); }
        .stat-icon.success { background: #10b981; }
        .stat-icon.warning { background: #f59e0b; }
        .stat-icon.danger { background: #ef4444; }
        
        .stat-number {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--secondary-color);
            font-size: 0.875rem;
            margin: 0;
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 2rem;
        }
        
        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .breadcrumb-item a:hover {
            text-decoration: underline;
        }
        
        .breadcrumb-item.active {
            color: var(--secondary-color);
        }
        
        .alert {
            border-radius: var(--border-radius);
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }
        
        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        
        /* Estilos melhorados para o sidebar */
        .dashboard-sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .dashboard-sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
            z-index: 1;
        }
        
        .dashboard-sidebar > * {
            position: relative;
            z-index: 2;
        }
        
        .sidebar-brand {
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 700;
            padding: 0 2rem;
            margin-bottom: 2rem;
            display: block;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-nav .nav-item {
            margin: 0.5rem 0;
        }
        
        .sidebar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 0.75rem 2rem;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            border-radius: 0;
            position: relative;
        }
        
        .sidebar-nav .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border-right: 3px solid white;
            transform: translateX(5px);
        }
        
        .sidebar-nav .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.15);
            border-right: 3px solid white;
            box-shadow: inset 0 0 10px rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-nav .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }
        
        .dashboard-content {
            padding: 2rem;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .dashboard-header {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }
        
        .welcome-text {
            color: var(--secondary-color);
            margin-bottom: 0;
        }
        
        @media (max-width: 768px) {
            .vaga-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .status-badge {
                margin-left: 0;
                margin-top: 0.5rem;
            }
            
            .vaga-info {
                grid-template-columns: 1fr;
            }
            
            .vaga-actions {
                justify-content: flex-start;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            
            .dashboard-sidebar {
                min-height: auto;
            }
            
            .dashboard-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 col-md-4">
                <div class="dashboard-sidebar">
                    <a href="<?php echo BASE_DIR; ?>" class="sidebar-brand">
                        <i class="fas fa-users me-2"></i><?php echo SITE_NAME; ?>
                    </a>
                    
                    <ul class="sidebar-nav">
                        <li class="nav-item">
                            <a href="<?php echo BASE_DIR; ?>/app/views/painel_empresa.php" class="nav-link">
                                <i class="fas fa-tachometer-alt"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo BASE_DIR; ?>/app/views/criar_vaga.php" class="nav-link">
                                <i class="fas fa-plus-circle"></i>Criar Vaga
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="fas fa-briefcase"></i>Gerenciar Vagas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo BASE_DIR; ?>/app/views/buscar_candidatos.php" class="nav-link">
                                <i class="fas fa-search"></i>Buscar Candidatos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo BASE_DIR; ?>/app/views/editar_perfil_empresa.php" class="nav-link">
                                <i class="fas fa-building"></i>Editar Perfil
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a href="<?php echo BASE_DIR; ?>/app/controllers/LogoutController.php" class="nav-link">
                                <i class="fas fa-sign-out-alt"></i>Sair
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Conteúdo Principal -->
            <div class="col-lg-9 col-md-8">
                <div class="dashboard-content">
                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo BASE_DIR; ?>/app/views/painel_empresa.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Gerenciar Vagas</li>
                        </ol>
                    </nav>
                    
                    <!-- Header -->
                    <div class="dashboard-header">
                        <h1 class="fw-bold text-primary mb-2">
                            <i class="fas fa-briefcase me-2"></i>Gerenciar Vagas
                        </h1>
                        <p class="welcome-text">Gerencie suas vagas publicadas e acompanhe as candidaturas.</p>
                    </div>
                    
                    <!-- Alertas -->
                    <?php if (isset($_SESSION['vaga_sucesso'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['vaga_sucesso']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['vaga_sucesso']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['vaga_erro'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_SESSION['vaga_erro']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['vaga_erro']); ?>
                    <?php endif; ?>
                    
                    <!-- Ações Rápidas -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <a href="<?php echo BASE_DIR; ?>/app/views/criar_vaga.php" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i>Criar Nova Vaga
                            </a>
                        </div>
                        <div>
                            <a href="<?php echo BASE_DIR; ?>/app/views/painel_empresa.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
                            </a>
                        </div>
                    </div>
                    
                    <!-- Estatísticas -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon primary">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div class="stat-number"><?php echo $stats['total_vagas']; ?></div>
                            <p class="stat-label">Total de Vagas</p>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-number"><?php echo $stats['vagas_ativas']; ?></div>
                            <p class="stat-label">Vagas Ativas</p>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon warning">
                                <i class="fas fa-pause-circle"></i>
                            </div>
                            <div class="stat-number"><?php echo $stats['vagas_pausadas']; ?></div>
                            <p class="stat-label">Vagas Pausadas</p>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon danger">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="stat-number"><?php echo $stats['total_candidaturas']; ?></div>
                            <p class="stat-label">Total de Candidaturas</p>
                        </div>
        </div>

                    <!-- Lista de Vagas -->
                    <div class="section-card">
                        <div class="section-header">
                            <h2 class="section-title">
                                <i class="fas fa-list me-2"></i>Suas Vagas
                            </h2>
                        </div>
                        <div class="section-content">
            <?php if (empty($vagas)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-briefcase"></i>
                                    <h3>Nenhuma vaga publicada ainda</h3>
                                    <p>Você ainda não publicou nenhuma vaga. Que tal criar sua primeira oportunidade?</p>
                                    <a href="<?php echo BASE_DIR; ?>/app/views/criar_vaga.php" class="btn btn-primary">
                                        <i class="fas fa-plus-circle me-2"></i>Criar Primeira Vaga
                                    </a>
                                </div>
            <?php else: ?>
                <?php foreach ($vagas as $vaga): ?>
                                    <div class="vaga-card">
                                        <div class="vaga-header">
                                            <h3 class="vaga-title"><?php echo htmlspecialchars($vaga['titulo']); ?></h3>
                                            <span class="status-badge status-<?php echo $vaga['status']; ?>">
                                                <?php echo ucfirst($vaga['status']); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="vaga-info">
                                            <div class="info-item">
                                                <i class="fas fa-calendar"></i>
                                                <span>Publicada em <?php echo date('d/m/Y', strtotime($vaga['created_at'])); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <span><?php echo htmlspecialchars($vaga['localizacao']); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-briefcase"></i>
                                                <span><?php echo ucfirst($vaga['nivel_experiencia']); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-users"></i>
                                                <span><?php echo $vaga['total_candidaturas']; ?> candidaturas</span>
                                            </div>
                                        </div>
                                        
                                        <div class="vaga-actions">
                                            <a href="<?php echo BASE_DIR; ?>/app/views/vaga_detalhes.php?id=<?php echo $vaga['id']; ?>" 
                                               class="btn-action btn-view">
                                                <i class="fas fa-eye me-1"></i>Ver Vaga
                                            </a>
                                            <a href="<?php echo BASE_DIR; ?>/app/views/editar_vaga.php?id=<?php echo $vaga['id']; ?>" 
                                               class="btn-action btn-edit">
                                                <i class="fas fa-edit me-1"></i>Editar
                                            </a>
                                            <a href="<?php echo BASE_DIR; ?>/app/views/ver_candidatura.php?vaga_id=<?php echo $vaga['id']; ?>" 
                                               class="btn-action btn-candidates">
                                                <i class="fas fa-users me-1"></i>Candidatos (<?php echo $vaga['total_candidaturas']; ?>)
                                            </a>
                                            <form action="<?php echo BASE_DIR; ?>/app/controllers/VagaController.php" method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($vaga['id']); ?>">
                                                <button type="submit" class="btn-action btn-delete" 
                                                        onclick="return confirm('Tem certeza que deseja excluir esta vaga? Esta ação não pode ser desfeita.');">
                                                    <i class="fas fa-trash me-1"></i>Excluir
                                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_DIR; ?>/public/js/main.js"></script>
</body>
</html>
