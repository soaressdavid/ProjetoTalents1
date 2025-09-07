<?php
// Arquivo: app/views/minhas_candidaturas.php

require_once __DIR__ . '/../utils/init.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'candidato') {
    header("Location: " . BASE_DIR . "/app/views/auth.php");
    exit();
}

$candidaturaModel = new Candidatura();
$vagaModel = new Vaga();

// Buscar candidaturas do candidato
$candidaturas = $candidaturaModel->findByCandidatoId($_SESSION['usuario_id']);

// Verificar se as consultas retornaram dados válidos
if ($candidaturas === false) {
    $candidaturas = [];
}

// Buscar detalhes das vagas para cada candidatura
$candidaturas_completas = [];
foreach ($candidaturas as $candidatura) {
    $vaga = $vagaModel->findById($candidatura['vaga_id']);
    if ($vaga) {
        $candidatura['vaga'] = $vaga;
        $candidaturas_completas[] = $candidatura;
    }
}

// Estatísticas
$stats = [
    'total' => count($candidaturas_completas),
    'enviadas' => count(array_filter($candidaturas_completas, fn($c) => $c['status'] === 'enviada')),
    'visualizadas' => count(array_filter($candidaturas_completas, fn($c) => $c['status'] === 'visualizada')),
    'aprovadas' => count(array_filter($candidaturas_completas, fn($c) => $c['status'] === 'aprovada')),
    'rejeitadas' => count(array_filter($candidaturas_completas, fn($c) => $c['status'] === 'rejeitada'))
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Candidaturas - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Acompanhe suas candidaturas no TalentsHUB.">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_DIR; ?>/public/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_DIR; ?>/public/images/favicon.ico">
    
    <style>
        .candidaturas-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .stats-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        
        .stat-item {
            text-align: center;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            background: #f8fafc;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
        }
        
        .stat-label {
            color: var(--secondary-color);
            font-size: 0.875rem;
            margin: 0.5rem 0 0;
        }
        
        .candidatura-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }
        
        .candidatura-card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }
        
        .candidatura-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .candidatura-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
            flex: 1;
        }
        
        .candidatura-company {
            color: var(--primary-color);
            font-size: 0.875rem;
            margin: 0.25rem 0;
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-enviada {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-visualizada {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-aprovada {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-rejeitada {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .candidatura-info {
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
        
        .candidatura-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .btn-candidatura {
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
        
        .btn-vaga {
            background: #10b981;
            color: white;
        }
        
        .btn-vaga:hover {
            background: #059669;
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
        
        .header {
            background: white;
            box-shadow: var(--shadow);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        
        .header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
        }
        
        .nav {
            display: flex;
            gap: 2rem;
        }
        
        .nav a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .nav a:hover {
            color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .candidaturas-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
            
            .stats-card {
                padding: 1.5rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            
            .candidatura-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .candidatura-info {
                grid-template-columns: 1fr;
            }
            
            .candidatura-actions {
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <h1 class="logo"><?php echo SITE_NAME; ?></h1>
            <nav class="nav">
                <a href="<?php echo BASE_DIR; ?>/app/views/vagas.php">Vagas</a>
                <a href="<?php echo BASE_DIR; ?>/app/views/painel_candidato.php">Meu Painel</a>
                <a href="<?php echo BASE_DIR; ?>/app/controllers/LogoutController.php">Sair</a>
            </nav>
        </div>
    </header>

    <div class="candidaturas-container">
        <!-- Estatísticas -->
        <div class="stats-card">
            <h2 class="mb-4">
                <i class="fas fa-chart-bar me-2"></i>Resumo das Candidaturas
            </h2>
            
            <div class="stats-grid">
                <div class="stat-item">
                    <p class="stat-number"><?php echo $stats['total']; ?></p>
                    <p class="stat-label">Total</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number"><?php echo $stats['enviadas']; ?></p>
                    <p class="stat-label">Enviadas</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number"><?php echo $stats['visualizadas']; ?></p>
                    <p class="stat-label">Visualizadas</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number"><?php echo $stats['aprovadas']; ?></p>
                    <p class="stat-label">Aprovadas</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number"><?php echo $stats['rejeitadas']; ?></p>
                    <p class="stat-label">Rejeitadas</p>
                </div>
            </div>
        </div>
        
        <!-- Lista de Candidaturas -->
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-file-alt me-2"></i>Minhas Candidaturas
                    <span class="badge bg-primary ms-2"><?php echo count($candidaturas_completas); ?></span>
                </h2>
            </div>
            
            <?php if (empty($candidaturas_completas)): ?>
                <div class="empty-state">
                    <i class="fas fa-file-alt"></i>
                    <h3>Nenhuma candidatura encontrada</h3>
                    <p>Você ainda não se candidatou a nenhuma vaga. Que tal começar a procurar oportunidades?</p>
                    <a href="<?php echo BASE_DIR; ?>/app/views/vagas.php" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Buscar Vagas
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($candidaturas_completas as $candidatura): ?>
                    <div class="candidatura-card">
                        <div class="candidatura-header">
                            <div>
                                <h3 class="candidatura-title"><?php echo htmlspecialchars($candidatura['vaga']['titulo']); ?></h3>
                                <p class="candidatura-company"><?php echo htmlspecialchars($candidatura['vaga']['razao_social'] ?? $candidatura['vaga']['empresa_nome'] ?? 'Empresa'); ?></p>
                            </div>
                            <span class="status-badge status-<?php echo $candidatura['status']; ?>">
                                <?php echo ucfirst($candidatura['status']); ?>
                            </span>
                        </div>
                        
                        <div class="candidatura-info">
                            <div class="info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($candidatura['vaga']['localizacao']); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-briefcase"></i>
                                <span><?php echo ucfirst($candidatura['vaga']['nivel_experiencia']); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <span><?php echo ucfirst($candidatura['vaga']['tipo_contrato']); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-calendar"></i>
                                <span>Candidatou-se em <?php echo date('d/m/Y', strtotime($candidatura['created_at'])); ?></span>
                            </div>
                        </div>
                        
                        <div class="candidatura-actions">
                            <a href="<?php echo BASE_DIR; ?>/app/views/ver_candidatura.php?id=<?php echo $candidatura['id']; ?>" 
                               class="btn-candidatura btn-view">
                                <i class="fas fa-eye me-1"></i>Ver Candidatura
                            </a>
                            
                            <a href="<?php echo BASE_DIR; ?>/app/views/vaga_detalhes.php?id=<?php echo $candidatura['vaga']['id']; ?>" 
                               class="btn-candidatura btn-vaga">
                                <i class="fas fa-briefcase me-1"></i>Ver Vaga
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_DIR; ?>/public/js/main.js"></script>
</body>
</html>
