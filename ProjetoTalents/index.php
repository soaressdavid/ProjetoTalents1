<?php
// Arquivo: index.php
// TalentsHUB - Página Inicial

require_once __DIR__ . '/app/utils/init.php';

// Buscar dados para a página inicial
$vagaModel = new Vaga();
$empresaModel = new Empresa();
$candidaturaModel = new Candidatura();

// Estatísticas gerais
$vaga_stats = $vagaModel->getStats();
$empresa_stats = $empresaModel->getStats();
$candidatura_stats = $candidaturaModel->getStats();

$stats = [
    'total_vagas' => $vaga_stats['vagas_ativas'] ?? 0,
    'total_empresas' => $empresa_stats['total_empresas'] ?? 0,
    'total_candidaturas' => $candidatura_stats['total_candidaturas'] ?? 0
];

// Vagas em destaque
$vagas_destaque = $vagaModel->getFeatured(6);
if ($vagas_destaque === false) {
    $vagas_destaque = [];
}

// Vagas recentes
$vagas_recentes = $vagaModel->getRecent(6);
if ($vagas_recentes === false) {
    $vagas_recentes = [];
}

// Top empresas
$top_empresas = $empresaModel->getTopEmpresas(8);
if ($top_empresas === false) {
    $top_empresas = [];
}

// Áreas mais populares
$areas = $vagaModel->getAreas();
if ($areas === false) {
    $areas = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - <?php echo SITE_DESCRIPTION; ?></title>
    <meta name="description" content="Conecte-se às melhores oportunidades de trabalho. Encontre vagas, candidatos e empresas no TalentsHUB.">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="public/css/style.css" rel="stylesheet">
    
    <!-- Meta Tags para SEO -->
    <meta property="og:title" content="<?php echo SITE_NAME; ?> - <?php echo SITE_DESCRIPTION; ?>">
    <meta property="og:description" content="Conecte-se às melhores oportunidades de trabalho. Encontre vagas, candidatos e empresas no TalentsHUB.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo BASE_DIR; ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="public/images/favicon.ico">
</head>
<body>
    <!-- Header -->
    <header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="<?php echo BASE_DIR; ?>">
                <i class="fas fa-users me-2"></i><?php echo SITE_NAME; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Menu Principal -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-briefcase me-1"></i>Vagas
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/vagas.php">
                                <i class="fas fa-search me-2"></i>Buscar Vagas
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/vagas.php?modalidade=remoto">
                                <i class="fas fa-home me-2"></i>Vagas Remotas
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/vagas.php?tipo_contrato=estagio">
                                <i class="fas fa-graduation-cap me-2"></i>Estágios
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/vagas.php?tipo_contrato=freelancer">
                                <i class="fas fa-laptop me-2"></i>Freelance
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/vagas.php?nivel_experiencia=junior">
                                <i class="fas fa-user-graduate me-2"></i>Vagas Júnior
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/vagas.php?nivel_experiencia=senior">
                                <i class="fas fa-user-tie me-2"></i>Vagas Sênior
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/demanda_regiao.php">
                                <i class="fas fa-map-marked-alt me-2"></i>Áreas com Mais Demanda
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/calculadora_deslocamento.php">
                                <i class="fas fa-route me-2"></i>Calculadora de Deslocamento
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/salarios.php">
                                <i class="fas fa-dollar-sign me-2"></i>Pesquisar Salários
                            </a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-building me-1"></i>Empresas
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/empresas.php">
                                <i class="fas fa-search me-2"></i>Buscar Empresas
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/empresas.php?tipo=startup">
                                <i class="fas fa-rocket me-2"></i>Startups
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/empresas.php?tipo=multinacional">
                                <i class="fas fa-globe me-2"></i>Multinacionais
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/avaliacoes_empresas.php">
                                <i class="fas fa-star me-2"></i>Avaliações de Empresas
                            </a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-users me-1"></i>Candidatos
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/candidatos.php">
                                <i class="fas fa-search me-2"></i>Buscar Candidatos
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/candidatos.php?area=tecnologia">
                                <i class="fas fa-code me-2"></i>Desenvolvedores
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/candidatos.php?area=marketing">
                                <i class="fas fa-chart-line me-2"></i>Marketing
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/candidatos.php?area=vendas">
                                <i class="fas fa-handshake me-2"></i>Vendas
                            </a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-graduation-cap me-1"></i>Carreiras
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/dicas_carreira.php">
                                <i class="fas fa-lightbulb me-2"></i>Dicas de Carreira
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/entrevistas.php">
                                <i class="fas fa-comments me-2"></i>Dicas de Entrevista
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/curriculo.php">
                                <i class="fas fa-file-alt me-2"></i>Como Fazer Currículo
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/salarios.php">
                                <i class="fas fa-dollar-sign me-2"></i>Pesquisar Salários
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/cursos.php">
                                <i class="fas fa-book me-2"></i>Cursos Online
                            </a></li>
                        </ul>
                    </li>
                </ul>
                
                <!-- Barra de Busca Rápida -->
                <div class="d-flex me-3">
                    <form class="d-flex" action="<?php echo BASE_DIR; ?>/app/views/vagas.php" method="GET">
                        <input class="form-control me-2" type="search" name="search" placeholder="Cargo, empresa..." style="width: 200px;">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                
                <!-- Menu do Usuário -->
                <div class="d-flex">
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <!-- Notificações -->
                        <div class="dropdown me-2">
                            <button class="btn btn-outline-secondary position-relative" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    3
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                                <li class="dropdown-header">
                                    <i class="fas fa-bell me-2"></i>Notificações
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-briefcase text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <div class="fw-bold">Nova vaga encontrada</div>
                                            <small class="text-muted">Desenvolvedor PHP - São Paulo</small>
                                        </div>
                                    </div>
                                </a></li>
                                <li><a class="dropdown-item" href="#">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-eye text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <div class="fw-bold">Seu perfil foi visualizado</div>
                                            <small class="text-muted">Há 2 horas</small>
                                        </div>
                                    </div>
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center" href="<?php echo BASE_DIR; ?>/app/views/notifications.php">
                                    Ver todas as notificações
                                </a></li>
                            </ul>
                        </div>
                        
                        <!-- Menu do Usuário -->
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if ($_SESSION['usuario_tipo'] === 'candidato'): ?>
                                    <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/painel_candidato.php">
                                        <i class="fas fa-tachometer-alt me-2"></i>Meu Painel
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/minhas_candidaturas.php">
                                        <i class="fas fa-file-alt me-2"></i>Minhas Candidaturas
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/meu_curriculo.php">
                                        <i class="fas fa-file-pdf me-2"></i>Meu Currículo
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/alertas_vagas.php">
                                        <i class="fas fa-bell me-2"></i>Alertas de Vagas
                                    </a></li>
                                <?php elseif ($_SESSION['usuario_tipo'] === 'empresa'): ?>
                                    <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/painel_empresa.php">
                                        <i class="fas fa-tachometer-alt me-2"></i>Meu Painel
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/gerenciar_vagas.php">
                                        <i class="fas fa-briefcase me-2"></i>Gerenciar Vagas
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/candidatos.php">
                                        <i class="fas fa-users me-2"></i>Buscar Candidatos
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/relatorios.php">
                                        <i class="fas fa-chart-bar me-2"></i>Relatórios
                                    </a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/configuracoes.php">
                                    <i class="fas fa-cog me-2"></i>Configurações
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/controllers/LogoutController.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Sair
                                </a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo BASE_DIR; ?>/app/views/auth.php" class="btn btn-primary me-2">
                            <i class="fas fa-sign-in-alt me-1"></i>Entrar
                        </a>
                        <a href="<?php echo BASE_DIR; ?>/app/views/auth.php?action=register" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus me-1"></i>Cadastrar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section bg-gradient-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center min-vh-50">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">
                        Conecte-se às melhores oportunidades de trabalho
                    </h1>
                    <p class="lead mb-4">
                        Encontre vagas ideais, candidatos talentosos e empresas inovadoras. 
                        O TalentsHUB é a plataforma que conecta talentos às oportunidades.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="<?php echo BASE_DIR; ?>/app/views/vagas.php" class="btn btn-light btn-lg">
                            <i class="fas fa-search me-2"></i>Buscar Vagas
                        </a>
                        <a href="<?php echo BASE_DIR; ?>/app/views/auth.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Cadastrar-se
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="hero-image">
                        <i class="fas fa-users fa-10x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <i class="fas fa-briefcase fa-3x text-primary mb-3"></i>
                        <h3 class="fw-bold"><?php echo number_format($stats['total_vagas']); ?></h3>
                        <p class="text-muted">Vagas Ativas</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <i class="fas fa-building fa-3x text-success mb-3"></i>
                        <h3 class="fw-bold"><?php echo number_format($stats['total_empresas']); ?></h3>
                        <p class="text-muted">Empresas Cadastradas</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <i class="fas fa-handshake fa-3x text-warning mb-3"></i>
                        <h3 class="fw-bold"><?php echo number_format($stats['total_candidaturas']); ?></h3>
                        <p class="text-muted">Conexões Realizadas</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="search-card bg-white shadow-lg rounded-3 p-4">
                        <h3 class="text-center mb-4">Encontre sua próxima oportunidade</h3>
                        <form action="<?php echo BASE_DIR; ?>/app/controllers/SearchController.php" method="GET">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control form-control-lg" name="search" 
                                           placeholder="Cargo, empresa ou palavra-chave" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control form-control-lg" name="localizacao" 
                                           placeholder="Cidade ou estado">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Jobs -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="text-center mb-5">Vagas em Destaque</h2>
                </div>
            </div>
            <div class="row">
                <?php foreach ($vagas_destaque as $vaga): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm job-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title"><?php echo htmlspecialchars($vaga['titulo']); ?></h5>
                                <span class="badge bg-primary"><?php echo ucfirst($vaga['modalidade']); ?></span>
                            </div>
                            <p class="text-muted mb-2">
                                <i class="fas fa-building me-1"></i><?php echo htmlspecialchars($vaga['empresa_nome']); ?>
                            </p>
                            <p class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($vaga['localizacao']); ?>
                            </p>
                            <p class="text-muted mb-3">
                                <i class="fas fa-briefcase me-1"></i><?php echo ucfirst($vaga['nivel_experiencia']); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-eye me-1"></i><?php echo number_format($vaga['visualizacoes']); ?> visualizações
                                </small>
                                <a href="<?php echo BASE_DIR; ?>/app/views/vaga_detalhes.php?id=<?php echo $vaga['id']; ?>" 
                                   class="btn btn-outline-primary btn-sm">Ver Detalhes</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="<?php echo BASE_DIR; ?>/app/views/vagas.php" class="btn btn-primary btn-lg">
                    Ver Todas as Vagas
                </a>
            </div>
        </div>
    </section>

    <!-- Top Companies -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="text-center mb-5">Empresas em Destaque</h2>
                </div>
            </div>
            <div class="row">
                <?php foreach ($top_empresas as $empresa): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card h-100 text-center company-card">
                        <div class="card-body">
                            <div class="company-logo mb-3">
                                <i class="fas fa-building fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($empresa['razao_social']); ?></h5>
                            <p class="text-muted mb-2"><?php echo htmlspecialchars($empresa['setor']); ?></p>
                            <p class="text-muted mb-3">
                                <i class="fas fa-briefcase me-1"></i><?php echo $empresa['vagas_ativas']; ?> vagas ativas
                            </p>
                            <a href="<?php echo BASE_DIR; ?>/app/views/empresa_detalhes.php?id=<?php echo $empresa['id']; ?>" 
                               class="btn btn-outline-primary btn-sm">Ver Empresa</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="mb-3">Pronto para encontrar sua próxima oportunidade?</h2>
                    <p class="lead mb-0">Junte-se a milhares de profissionais que já encontraram seu lugar ideal no TalentsHUB.</p>
                </div>
                <div class="col-lg-4 text-end">
                    <a href="<?php echo BASE_DIR; ?>/app/views/auth.php" class="btn btn-light btn-lg">
                        <i class="fas fa-rocket me-2"></i>Começar Agora
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-users me-2"></i><?php echo SITE_NAME; ?>
                    </h5>
                    <p class="text-muted">
                        Conectando talentos às melhores oportunidades de trabalho. 
                        A plataforma de recrutamento e seleção mais completa do Brasil.
                    </p>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6 class="fw-bold mb-3">Para Candidatos</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo BASE_DIR; ?>/app/views/vagas.php" class="text-muted text-decoration-none">Buscar Vagas</a></li>
                        <li><a href="<?php echo BASE_DIR; ?>/app/views/auth.php" class="text-muted text-decoration-none">Cadastrar-se</a></li>
                        <li><a href="<?php echo BASE_DIR; ?>/app/views/dicas_carreira.php" class="text-muted text-decoration-none">Dicas de Carreira</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6 class="fw-bold mb-3">Para Empresas</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo BASE_DIR; ?>/app/views/auth.php" class="text-muted text-decoration-none">Cadastrar Empresa</a></li>
                        <li><a href="<?php echo BASE_DIR; ?>/app/views/candidatos.php" class="text-muted text-decoration-none">Buscar Candidatos</a></li>
                        <li><a href="<?php echo BASE_DIR; ?>/app/views/criar_vaga.php" class="text-muted text-decoration-none">Publicar Vaga</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6 class="fw-bold mb-3">Suporte</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Central de Ajuda</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Contato</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6 class="fw-bold mb-3">Legal</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Termos de Uso</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Política de Privacidade</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Cookies</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Todos os direitos reservados.</p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="social-links">
                        <a href="#" class="text-muted me-3"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-muted me-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-muted me-3"><i class="fab fa-linkedin fa-lg"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-instagram fa-lg"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/main.js"></script>
</body>
</html>