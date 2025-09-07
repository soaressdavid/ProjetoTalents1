<?php
require_once __DIR__ . '/../utils/init.php';

$empresaModel = new Empresa();

$termo_busca = $_GET['termo_busca'] ?? '';
$filtro_cidade = $_GET['filtro_cidade'] ?? '';

$filters = [];
if (!empty($termo_busca)) {
    $filters['search'] = $termo_busca;
}
if (!empty($filtro_cidade)) {
    $filters['cidade'] = $filtro_cidade;
}

// Buscar empresas
$empresas = $empresaModel->getAll($filters);

// Verificar se a consulta retornou dados válidos
if ($empresas === false) {
    $empresas = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empresas - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Conheça as empresas parceiras do TalentsHUB.">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_DIR; ?>/public/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_DIR; ?>/public/images/favicon.ico">
    
    <style>
        .empresas-container {
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
        
        .empresa-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }
        
        .empresa-card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }
        
        .empresa-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .empresa-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
            flex: 1;
        }
        
        .empresa-industry {
            color: var(--primary-color);
            font-size: 0.875rem;
            margin: 0.25rem 0;
        }
        
        .empresa-info {
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
        
        .empresa-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .btn-empresa {
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
        
        .btn-vagas {
            background: #10b981;
            color: white;
        }
        
        .btn-vagas:hover {
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
            .empresas-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
            
            .search-card, .filter-card {
                padding: 1.5rem;
            }
            
            .empresa-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .empresa-info {
                grid-template-columns: 1fr;
            }
            
            .empresa-actions {
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
                <a href="<?php echo BASE_DIR; ?>/app/views/empresas.php">Empresas</a>
                <a href="<?php echo BASE_DIR; ?>/app/views/candidatos.php">Candidatos</a>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <?php if ($_SESSION['usuario_tipo'] === 'candidato'): ?>
                        <a href="<?php echo BASE_DIR; ?>/app/views/painel_candidato.php">Meu Painel</a>
                        <a href="<?php echo BASE_DIR; ?>/app/views/minhas_candidaturas.php">Minhas Candidaturas</a>
                    <?php elseif ($_SESSION['usuario_tipo'] === 'empresa'): ?>
                        <a href="<?php echo BASE_DIR; ?>/app/views/painel_empresa.php">Meu Painel</a>
                        <a href="<?php echo BASE_DIR; ?>/app/views/gerenciar_vagas.php">Gerenciar Vagas</a>
                    <?php endif; ?>
                    <a href="<?php echo BASE_DIR; ?>/app/controllers/LogoutController.php">Sair</a>
                <?php else: ?>
                    <a href="<?php echo BASE_DIR; ?>/app/views/auth.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="empresas-container">
        <!-- Filtros de Busca -->
        <div class="filter-card">
            <h3 class="filter-title">
                <i class="fas fa-search me-2"></i>Filtros de Busca
            </h3>
            
            <form method="GET" action="">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="termo_busca" class="form-label">Palavra-chave</label>
                            <input type="text" id="termo_busca" name="termo_busca" class="form-control" 
                                   value="<?php echo htmlspecialchars($termo_busca); ?>" 
                                   placeholder="Pesquisar por nome da empresa...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="filtro_cidade" class="form-label">Cidade</label>
                            <select id="filtro_cidade" name="filtro_cidade" class="form-select">
                                <option value="">Todas as Cidades</option>
                                <option value="São Paulo" <?php echo ($filtro_cidade == 'São Paulo') ? 'selected' : ''; ?>>São Paulo</option>
                                <option value="Rio de Janeiro" <?php echo ($filtro_cidade == 'Rio de Janeiro') ? 'selected' : ''; ?>>Rio de Janeiro</option>
                                <option value="Belo Horizonte" <?php echo ($filtro_cidade == 'Belo Horizonte') ? 'selected' : ''; ?>>Belo Horizonte</option>
                                <option value="Brasília" <?php echo ($filtro_cidade == 'Brasília') ? 'selected' : ''; ?>>Brasília</option>
                                <option value="Salvador" <?php echo ($filtro_cidade == 'Salvador') ? 'selected' : ''; ?>>Salvador</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex">
                                <button type="submit" class="btn-search">
                                    <i class="fas fa-search me-2"></i>Pesquisar
                                </button>
                                <a href="<?php echo BASE_DIR; ?>/app/views/empresas.php" class="btn-clear">
                                    <i class="fas fa-times me-2"></i>Limpar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Resultados -->
        <div class="search-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-building me-2"></i>Empresas Parceiras
                    <span class="badge bg-primary ms-2"><?php echo count($empresas); ?></span>
                </h2>
            </div>
            
            <?php if (empty($empresas)): ?>
                <div class="empty-state">
                    <i class="fas fa-building"></i>
                    <h3>Nenhuma empresa encontrada</h3>
                    <p>Tente ajustar os filtros de busca para encontrar mais empresas.</p>
                    <a href="<?php echo BASE_DIR; ?>/app/views/empresas.php" class="btn btn-primary">
                        <i class="fas fa-refresh me-2"></i>Limpar Filtros
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($empresas as $empresa): ?>
                    <div class="empresa-card">
                        <div class="empresa-header">
                            <div>
                                <h3 class="empresa-title"><?php echo htmlspecialchars($empresa['razao_social']); ?></h3>
                                <p class="empresa-industry"><?php echo htmlspecialchars($empresa['ramo_atividade'] ?? 'Tecnologia'); ?></p>
                            </div>
                        </div>
                        
                        <div class="empresa-info">
                            <div class="info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($empresa['cidade'] . ', ' . $empresa['estado']); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-users"></i>
                                <span><?php echo htmlspecialchars($empresa['tamanho_empresa'] ?? 'Não informado'); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-calendar"></i>
                                <span>Cadastrada em <?php echo date('d/m/Y', strtotime($empresa['created_at'])); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-briefcase"></i>
                                <span><?php echo $empresa['total_vagas'] ?? 0; ?> vagas ativas</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <p class="text-muted"><?php echo substr(strip_tags($empresa['descricao'] ?? 'Empresa parceira do TalentsHUB'), 0, 200) . '...'; ?></p>
                        </div>
                        
                        <div class="empresa-actions">
                            <a href="<?php echo BASE_DIR; ?>/app/views/empresa_detalhes.php?id=<?php echo $empresa['id']; ?>" 
                               class="btn-empresa btn-view">
                                <i class="fas fa-eye me-1"></i>Ver Perfil
                            </a>
                            
                            <a href="<?php echo BASE_DIR; ?>/app/views/vagas.php?empresa_id=<?php echo $empresa['id']; ?>" 
                               class="btn-empresa btn-vagas">
                                <i class="fas fa-briefcase me-1"></i>Ver Vagas
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
