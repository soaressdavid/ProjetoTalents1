<?php
// Arquivo: app/views/buscar_candidatos.php
// TalentsHUB - Buscar Candidatos

require_once __DIR__ . '/../utils/init.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'empresa') {
    header("Location: " . BASE_DIR . "/app/views/auth.php");
    exit();
}

$candidatoModel = new Candidato();
$perfilCandidatoModel = new PerfilCandidato();

// Filtros de busca
$filters = [
    'area' => $_GET['area'] ?? '',
    'nivel_experiencia' => $_GET['nivel_experiencia'] ?? '',
    'cidade' => $_GET['cidade'] ?? '',
    'search' => $_GET['search'] ?? ''
];

// Buscar candidatos
$candidatos = $candidatoModel->getAll($filters);

// Garantir que $candidatos seja sempre um array
if ($candidatos === false) {
    $candidatos = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Candidatos - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Encontre os melhores talentos para sua empresa no TalentsHUB.">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_DIR; ?>/public/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_DIR; ?>/public/images/favicon.ico">
    
    <style>
        .search-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .search-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .candidate-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }
        
        .candidate-card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }
        
        .candidate-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .candidate-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }
        
        .candidate-title {
            color: var(--primary-color);
            font-size: 0.875rem;
            margin: 0.25rem 0;
        }
        
        .candidate-info {
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
        
        .candidate-skills {
            margin-bottom: 1rem;
        }
        
        .skill-tag {
            display: inline-block;
            background: var(--light-color);
            color: var(--dark-color);
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            margin: 0.25rem 0.25rem 0.25rem 0;
        }
        
        .candidate-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .btn-candidate {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }
        
        .btn-view-profile {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-view-profile:hover {
            background: var(--primary-dark);
            color: white;
        }
        
        .btn-contact {
            background: #10b981;
            color: white;
        }
        
        .btn-contact:hover {
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
        
        .filter-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .filter-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1rem;
        }
        
        .form-control, .form-select {
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 0.75rem;
            font-size: 0.875rem;
            transition: var(--transition);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .btn-search {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .btn-search:hover {
            background: var(--primary-dark);
        }
        
        .btn-clear {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            padding: 0.75rem 2rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-left: 1rem;
        }
        
        .btn-clear:hover {
            background: var(--primary-color);
            color: white;
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
        
        /* Estilos do sidebar - igual aos outros */
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
            .search-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
            
            .search-card, .filter-card {
                padding: 1.5rem;
            }
            
            .candidate-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .candidate-info {
                grid-template-columns: 1fr;
            }
            
            .candidate-actions {
                justify-content: flex-start;
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
                            <a href="<?php echo BASE_DIR; ?>/app/views/gerenciar_vagas.php" class="nav-link">
                                <i class="fas fa-briefcase"></i>Gerenciar Vagas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
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
                            <li class="breadcrumb-item active">Buscar Candidatos</li>
                        </ol>
                    </nav>
                    
                    <!-- Header -->
                    <div class="dashboard-header">
                        <h1 class="fw-bold text-primary mb-2">
                            <i class="fas fa-search me-2"></i>Buscar Candidatos
                        </h1>
                        <p class="welcome-text">Encontre os melhores talentos para sua empresa.</p>
                    </div>
                    
                    <!-- Filtros de Busca -->
                    <div class="filter-card">
                        <h3 class="filter-title">
                            <i class="fas fa-filter me-2"></i>Filtros de Busca
                        </h3>
                        
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="search" class="form-label">Palavra-chave</label>
                                        <input type="text" id="search" name="search" class="form-control" 
                                               value="<?php echo htmlspecialchars($filters['search']); ?>" 
                                               placeholder="Nome, habilidades...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="area" class="form-label">Área</label>
                                        <select id="area" name="area" class="form-select">
                                            <option value="">Todas as áreas</option>
                                            <option value="tecnologia" <?php echo $filters['area'] === 'tecnologia' ? 'selected' : ''; ?>>Tecnologia</option>
                                            <option value="marketing" <?php echo $filters['area'] === 'marketing' ? 'selected' : ''; ?>>Marketing</option>
                                            <option value="vendas" <?php echo $filters['area'] === 'vendas' ? 'selected' : ''; ?>>Vendas</option>
                                            <option value="rh" <?php echo $filters['area'] === 'rh' ? 'selected' : ''; ?>>Recursos Humanos</option>
                                            <option value="financeiro" <?php echo $filters['area'] === 'financeiro' ? 'selected' : ''; ?>>Financeiro</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="nivel_experiencia" class="form-label">Nível</label>
                                        <select id="nivel_experiencia" name="nivel_experiencia" class="form-select">
                                            <option value="">Todos os níveis</option>
                                            <option value="estagiario" <?php echo $filters['nivel_experiencia'] === 'estagiario' ? 'selected' : ''; ?>>Estagiário</option>
                                            <option value="junior" <?php echo $filters['nivel_experiencia'] === 'junior' ? 'selected' : ''; ?>>Júnior</option>
                                            <option value="pleno" <?php echo $filters['nivel_experiencia'] === 'pleno' ? 'selected' : ''; ?>>Pleno</option>
                                            <option value="senior" <?php echo $filters['nivel_experiencia'] === 'senior' ? 'selected' : ''; ?>>Sênior</option>
                                            <option value="especialista" <?php echo $filters['nivel_experiencia'] === 'especialista' ? 'selected' : ''; ?>>Especialista</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="cidade" class="form-label">Cidade</label>
                                        <input type="text" id="cidade" name="cidade" class="form-control" 
                                               value="<?php echo htmlspecialchars($filters['cidade']); ?>" 
                                               placeholder="Ex: São Paulo">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn-search">
                                    <i class="fas fa-search me-2"></i>Buscar
                                </button>
                                <a href="<?php echo BASE_DIR; ?>/app/views/buscar_candidatos.php" class="btn-clear">
                                    <i class="fas fa-times me-2"></i>Limpar
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Resultados -->
                    <div class="search-card">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="mb-0">
                                <i class="fas fa-users me-2"></i>Candidatos Encontrados
                                <span class="badge bg-primary ms-2"><?php echo count($candidatos); ?></span>
                            </h2>
                        </div>
                        
                        <?php if (empty($candidatos)): ?>
                            <div class="empty-state">
                                <i class="fas fa-user-slash"></i>
                                <h3>Nenhum candidato encontrado</h3>
                                <p>Tente ajustar os filtros de busca para encontrar mais candidatos.</p>
                                <a href="<?php echo BASE_DIR; ?>/app/views/buscar_candidatos.php" class="btn btn-primary">
                                    <i class="fas fa-refresh me-2"></i>Limpar Filtros
                                </a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($candidatos as $candidato): ?>
                                <div class="candidate-card">
                                    <div class="candidate-header">
                                        <div>
                                            <h3 class="candidate-name"><?php echo htmlspecialchars($candidato['nome'] ?? 'Nome não disponível'); ?></h3>
                                            <p class="candidate-title"><?php echo htmlspecialchars($candidato['resumo_profissional'] ?? 'Resumo não disponível'); ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="candidate-info">
                                        <div class="info-item">
                                            <i class="fas fa-envelope"></i>
                                            <span><?php echo htmlspecialchars($candidato['email'] ?? 'Email não disponível'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span><?php echo htmlspecialchars($candidato['cidade'] ?? 'Cidade não informada'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-briefcase"></i>
                                            <span><?php echo ucfirst($candidato['nivel_experiencia'] ?? 'Nível não informado'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-calendar"></i>
                                            <span>Cadastrado em <?php echo date('d/m/Y', strtotime($candidato['created_at'])); ?></span>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($candidato['habilidades'])): ?>
                                    <div class="candidate-skills">
                                        <strong>Habilidades:</strong>
                                        <?php 
                                        $habilidades = explode(',', $candidato['habilidades']);
                                        foreach ($habilidades as $habilidade): 
                                        ?>
                                            <span class="skill-tag"><?php echo htmlspecialchars(trim($habilidade)); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="candidate-actions">
                                        <a href="<?php echo BASE_DIR; ?>/app/views/perfil_candidato.php?candidato_id=<?php echo $candidato['id']; ?>" 
                                           class="btn-candidate btn-view-profile">
                                            <i class="fas fa-user me-1"></i>Ver Perfil Completo
                                        </a>
                                        
                                        <a href="mailto:<?php echo htmlspecialchars($candidato['email']); ?>" 
                                           class="btn-candidate btn-contact">
                                            <i class="fas fa-envelope me-1"></i>Entrar em Contato
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
