<?php
// Arquivo: app/views/painel_candidato.php
// TalentsHUB - Painel do Candidato

require_once __DIR__ . '/../utils/init.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'candidato') {
    header("Location: " . BASE_DIR . "/app/views/auth.php");
    exit();
}

$candidaturaModel = new Candidatura();
$vagaModel = new Vaga();
$candidatoModel = new Candidato();

// Buscar dados do candidato
$candidato = $candidatoModel->findByUsuarioId($_SESSION['usuario_id']);
$candidaturas = $candidaturaModel->findByCandidatoId($_SESSION['usuario_id']);

// Verificar se as consultas retornaram dados válidos
if ($candidaturas === false) {
    $candidaturas = [];
}

// Estatísticas
$stats = [
    'total_candidaturas' => count($candidaturas),
    'candidaturas_pendentes' => count(array_filter($candidaturas, fn($c) => $c['status'] === 'enviada')),
    'candidaturas_visualizadas' => count(array_filter($candidaturas, fn($c) => $c['status'] === 'visualizada')),
    'candidaturas_aprovadas' => count(array_filter($candidaturas, fn($c) => $c['status'] === 'aprovada'))
];

// Vagas recomendadas
$vagas_recomendadas = $vagaModel->getRecent(6);
if ($vagas_recomendadas === false) {
    $vagas_recomendadas = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Painel do candidato - Gerencie suas candidaturas e encontre novas oportunidades.">
    
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
            justify-content: between;
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
        
        .candidatura-item {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: var(--transition);
        }
        
        .candidatura-item:hover {
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
        
        .status-enviada { background: #dbeafe; color: #1e40af; }
        .status-visualizada { background: #fef3c7; color: #92400e; }
        .status-em_analise { background: #e0e7ff; color: #3730a3; }
        .status-aprovada { background: #d1fae5; color: #065f46; }
        .status-rejeitada { background: #fee2e2; color: #991b1b; }
        .status-entrevista_agendada { background: #f3e8ff; color: #6b21a8; }
        
        .vaga-card {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: var(--transition);
            height: 100%;
        }
        
        .vaga-card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }
        
        .vaga-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .vaga-company {
            color: var(--secondary-color);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }
        
        .vaga-location {
            color: var(--secondary-color);
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        
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
                            <a href="<?php echo BASE_DIR; ?>/app/views/vagas.php" class="nav-link">
                                <i class="fas fa-search"></i>Buscar Vagas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo BASE_DIR; ?>/app/views/editar_perfil_candidato.php" class="nav-link">
                                <i class="fas fa-user-edit"></i>Editar Perfil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo BASE_DIR; ?>/app/views/minhas_candidaturas.php" class="nav-link">
                                <i class="fas fa-file-alt"></i>Minhas Candidaturas
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
                    <!-- Header -->
                    <div class="dashboard-header">
                        <h1 class="fw-bold text-primary mb-2">Dashboard</h1>
                        <p class="welcome-text">Bem-vindo de volta, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</p>
                    </div>
                    
                    <!-- Estatísticas -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon primary">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <div class="stat-number"><?php echo $stats['total_candidaturas']; ?></div>
                            <p class="stat-label">Total de Candidaturas</p>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon warning">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-number"><?php echo $stats['candidaturas_pendentes']; ?></div>
                            <p class="stat-label">Pendentes</p>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon info">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="stat-number"><?php echo $stats['candidaturas_visualizadas']; ?></div>
                            <p class="stat-label">Visualizadas</p>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-number"><?php echo $stats['candidaturas_aprovadas']; ?></div>
                            <p class="stat-label">Aprovadas</p>
                        </div>
                    </div>
                    
                    <!-- Minhas Candidaturas -->
                    <div class="section-card">
                        <div class="section-header">
                            <h2 class="section-title">
                                <i class="fas fa-file-alt me-2"></i>Minhas Candidaturas Recentes
                            </h2>
                            <a href="<?php echo BASE_DIR; ?>/app/views/minhas_candidaturas.php" class="btn btn-outline-primary btn-sm">
                                Ver Todas
                            </a>
                        </div>
                        <div class="section-content">
                            <?php if (empty($candidaturas)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h4>Nenhuma candidatura ainda</h4>
                                    <p>Você ainda não se candidatou a nenhuma vaga. Que tal começar a buscar oportunidades?</p>
                                    <a href="<?php echo BASE_DIR; ?>/app/views/vagas.php" class="btn btn-primary">
                                        <i class="fas fa-search me-2"></i>Buscar Vagas
                                    </a>
                                </div>
                            <?php else: ?>
                                <?php foreach (array_slice($candidaturas, 0, 5) as $candidatura): ?>
                                    <div class="candidatura-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <h5 class="vaga-title"><?php echo htmlspecialchars($candidatura['titulo']); ?></h5>
                                                <p class="vaga-company">
                                                    <i class="fas fa-building me-1"></i><?php echo htmlspecialchars($candidatura['empresa_nome']); ?>
                                                </p>
                                                <p class="vaga-location">
                                                    <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($candidatura['localizacao']); ?>
                                                </p>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>Candidatou-se em <?php echo date('d/m/Y', strtotime($candidatura['data_candidatura'])); ?>
                                                </small>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <span class="status-badge status-<?php echo $candidatura['status']; ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $candidatura['status'])); ?>
                                                </span>
                                                <div class="mt-2">
                                                    <a href="<?php echo BASE_DIR; ?>/app/views/vaga_detalhes.php?id=<?php echo $candidatura['vaga_id']; ?>" 
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-eye me-1"></i>Ver Vaga
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Vagas Recomendadas -->
                    <div class="section-card">
                        <div class="section-header">
                            <h2 class="section-title">
                                <i class="fas fa-star me-2"></i>Vagas Recomendadas
                            </h2>
                            <a href="<?php echo BASE_DIR; ?>/app/views/vagas.php" class="btn btn-outline-primary btn-sm">
                                Ver Todas
                            </a>
                        </div>
                        <div class="section-content">
                            <div class="row">
                                <?php foreach ($vagas_recomendadas as $vaga): ?>
                                    <div class="col-lg-4 col-md-6 mb-3">
                                        <div class="vaga-card">
                                            <h6 class="vaga-title"><?php echo htmlspecialchars($vaga['titulo']); ?></h6>
                                            <p class="vaga-company">
                                                <i class="fas fa-building me-1"></i><?php echo htmlspecialchars($vaga['empresa_nome']); ?>
                                            </p>
                                            <p class="vaga-location">
                                                <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($vaga['localizacao']); ?>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-primary"><?php echo ucfirst($vaga['modalidade']); ?></span>
                                                <a href="<?php echo BASE_DIR; ?>/app/views/vaga_detalhes.php?id=<?php echo $vaga['id']; ?>" 
                                                   class="btn btn-primary btn-sm">
                                                    Ver Detalhes
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
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