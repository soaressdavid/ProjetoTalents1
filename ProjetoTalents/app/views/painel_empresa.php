<?php
// Arquivo: app/views/painel_empresa.php
// TalentsHUB - Painel da Empresa

require_once __DIR__ . '/../utils/init.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'empresa') {
    header("Location: " . BASE_DIR . "/app/views/auth.php");
    exit();
}

$vagaModel = new Vaga();
$empresaModel = new Empresa();
$candidaturaModel = new Candidatura();

// Buscar dados da empresa
$empresa = $empresaModel->findByUsuarioId($_SESSION['usuario_id']);

// Verificar se a empresa existe
if (!$empresa) {
    $_SESSION['vaga_erro'] = "Empresa não encontrada. Complete seu cadastro de empresa.";
    header("Location: " . BASE_DIR . "/app/views/editar_perfil_empresa.php");
    exit();
}

$vagas = $vagaModel->getByEmpresa($empresa['id']);

// Verificar se as consultas retornaram dados válidos
if ($vagas === false) {
    $vagas = [];
}

// Estatísticas
$stats = [
    'total_vagas' => count($vagas),
    'vagas_ativas' => count(array_filter($vagas, fn($v) => $v['status'] === 'ativa')),
    'total_candidaturas' => 0,
    'candidaturas_pendentes' => 0
];

// Calcular candidaturas
foreach ($vagas as $vaga) {
    $candidaturas_vaga = $candidaturaModel->findByVagaId($vaga['id']);
    if ($candidaturas_vaga !== false) {
        $stats['total_candidaturas'] += count($candidaturas_vaga);
        $stats['candidaturas_pendentes'] += count(array_filter($candidaturas_vaga, fn($c) => $c['status'] === 'enviada'));
    }
}

// Vagas recentes
$vagas_recentes = array_slice($vagas, 0, 5);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Empresa - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Painel da empresa - Gerencie suas vagas e candidatos.">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_DIR; ?>/public/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_DIR; ?>/public/images/favicon.ico">
    
    <style>
        .dashboard-sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            padding: 2rem 0;
        }
        
        .sidebar-brand {
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 700;
            padding: 0 2rem;
            margin-bottom: 2rem;
            display: block;
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
            transition: var(--transition);
            border-radius: 0;
        }
        
        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border-right: 3px solid white;
        }
        
        .sidebar-nav .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
        }
        
        .dashboard-content {
            padding: 2rem;
            background: var(--light-color);
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
        }
        
        .stat-icon.primary { background: var(--primary-color); }
        .stat-icon.success { background: var(--success-color); }
        .stat-icon.warning { background: var(--warning-color); }
        .stat-icon.info { background: #3b82f6; }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--secondary-color);
            font-size: 0.875rem;
            margin: 0;
        }
        
        .section-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }
        
        .section-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }
        
        .section-content {
            padding: 2rem;
        }
        
        .vaga-item {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: var(--transition);
        }
        
        .vaga-item:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-ativa { background: #d1fae5; color: #065f46; }
        .status-pausada { background: #fef3c7; color: #92400e; }
        .status-finalizada { background: #fee2e2; color: #991b1b; }
        
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--secondary-color);
        }
        
        .empty-state i {
            font-size: 3rem;
            color: var(--border-color);
            margin-bottom: 1rem;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .action-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            text-align: center;
            transition: var(--transition);
            text-decoration: none;
            color: inherit;
        }
        
        .action-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            color: inherit;
            text-decoration: none;
        }
        
        .action-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
            background: var(--primary-color);
        }
        
        .action-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .action-description {
            color: var(--secondary-color);
            font-size: 0.875rem;
            margin: 0;
        }
        
        @media (max-width: 768px) {
            .dashboard-sidebar {
                min-height: auto;
            }
            
            .dashboard-content {
                padding: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            
            .stat-card {
                padding: 1.5rem;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
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
                            <a href="#" class="nav-link active">
                                <i class="fas fa-tachometer-alt"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo BASE_DIR; ?>/app/views/criar_vaga.php" class="nav-link">
                                <i class="fas fa-plus-circle"></i>Criar Vaga
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo BASE_DIR; ?>/app/views/gerenciar_vagas.php" class="nav-link">
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
                        <li class="nav-item">
                            <a href="<?php echo BASE_DIR; ?>/app/views/notifications.php" class="nav-link">
                                <i class="fas fa-bell"></i>Notificações
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

                    <!-- Header -->
                    <div class="dashboard-header">
                        <h1 class="fw-bold text-primary mb-2">Dashboard Empresa</h1>
                        <p class="welcome-text">Bem-vindo de volta, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</p>
                    </div>
                    
                    <!-- Ações Rápidas -->
                    <div class="quick-actions">
                        <a href="<?php echo BASE_DIR; ?>/app/views/criar_vaga.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <h3 class="action-title">Criar Nova Vaga</h3>
                            <p class="action-description">Publique uma nova oportunidade de trabalho</p>
                        </a>
                        
                        <a href="<?php echo BASE_DIR; ?>/app/views/gerenciar_vagas.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <h3 class="action-title">Gerenciar Vagas</h3>
                            <p class="action-description">Visualize e edite suas vagas publicadas</p>
                        </a>
                        
                        <a href="<?php echo BASE_DIR; ?>/app/views/buscar_candidatos.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <h3 class="action-title">Buscar Candidatos</h3>
                            <p class="action-description">Encontre talentos para sua empresa</p>
                        </a>
                        
                        <a href="<?php echo BASE_DIR; ?>/app/views/editar_perfil_empresa.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <h3 class="action-title">Editar Perfil</h3>
                            <p class="action-description">Atualize as informações da sua empresa</p>
                        </a>
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
                            <div class="stat-icon info">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="stat-number"><?php echo $stats['total_candidaturas']; ?></div>
                            <p class="stat-label">Total de Candidaturas</p>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon warning">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-number"><?php echo $stats['candidaturas_pendentes']; ?></div>
                            <p class="stat-label">Candidaturas Pendentes</p>
                        </div>
                    </div>
                    
                    <!-- Vagas Recentes -->
                    <div class="section-card">
                        <div class="section-header">
                            <h2 class="section-title">
                                <i class="fas fa-briefcase me-2"></i>Suas Vagas Recentes
                            </h2>
                            <a href="<?php echo BASE_DIR; ?>/app/views/gerenciar_vagas.php" class="btn btn-outline-primary btn-sm">
                                Gerenciar Todas
                            </a>
                        </div>
                        <div class="section-content">
                            <?php if (empty($vagas)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-briefcase"></i>
                                    <h4>Nenhuma vaga publicada ainda</h4>
                                    <p>Você ainda não publicou nenhuma vaga. Que tal criar sua primeira oportunidade?</p>
                                    <a href="<?php echo BASE_DIR; ?>/app/views/criar_vaga.php" class="btn btn-primary">
                                        <i class="fas fa-plus-circle me-2"></i>Criar Primeira Vaga
                                    </a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($vagas_recentes as $vaga): ?>
                                    <div class="vaga-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <h5 class="fw-bold text-primary"><?php echo htmlspecialchars($vaga['titulo']); ?></h5>
                                                <p class="text-muted mb-2">
                                                    <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($vaga['localizacao']); ?>
                                                </p>
                                                <p class="text-muted mb-2">
                                                    <i class="fas fa-briefcase me-1"></i><?php echo ucfirst($vaga['nivel_experiencia']); ?>
                                                </p>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>Publicada em <?php echo date('d/m/Y', strtotime($vaga['created_at'])); ?>
                                                </small>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <span class="status-badge status-<?php echo $vaga['status']; ?>">
                                                    <?php echo ucfirst($vaga['status']); ?>
                                                </span>
                                                <div class="mt-2">
                                                    <a href="<?php echo BASE_DIR; ?>/app/views/editar_vaga.php?id=<?php echo $vaga['id']; ?>" 
                                                       class="btn btn-outline-primary btn-sm me-2">
                                                        <i class="fas fa-edit me-1"></i>Editar
                                                    </a>
                                                    <a href="<?php echo BASE_DIR; ?>/app/views/vaga_detalhes.php?id=<?php echo $vaga['id']; ?>" 
                                                       class="btn btn-primary btn-sm">
                                                        <i class="fas fa-eye me-1"></i>Ver
                                                    </a>
                                                </div>
                                            </div>
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
