<?php
require_once __DIR__ . '/../utils/init.php';

$vagaModel = new Vaga();

// Obter região selecionada e cidade
$regiao_selecionada = $_GET['regiao'] ?? '';
$cidade_buscada = $_GET['cidade'] ?? '';
$busca_personalizada = !empty($cidade_buscada);

// Obter filtros adicionais
$filtro_area = $_GET['area'] ?? '';
$filtro_modalidade = $_GET['modalidade'] ?? '';
$filtro_nivel = $_GET['nivel_experiencia'] ?? '';
$filtro_contrato = $_GET['tipo_contrato'] ?? '';

// Preparar array de filtros
$filtros = [];
if (!empty($filtro_area)) $filtros['area'] = $filtro_area;
if (!empty($filtro_modalidade)) $filtros['modalidade'] = $filtro_modalidade;
if (!empty($filtro_nivel)) $filtros['nivel_experiencia'] = $filtro_nivel;
if (!empty($filtro_contrato)) $filtros['tipo_contrato'] = $filtro_contrato;

// Buscar regiões disponíveis
$regioes = $vagaModel->getRegioesDisponiveis();
if ($regioes === false) {
    $regioes = [];
}

// Buscar sugestões de cidades se houver busca
$sugestoes_cidades = [];
if (!empty($cidade_buscada)) {
    $sugestoes_cidades = $vagaModel->buscarPorCidade($cidade_buscada);
    if ($sugestoes_cidades === false) {
        $sugestoes_cidades = [];
    }
}

// Buscar top áreas - por região ou por cidade (com filtros)
if ($busca_personalizada) {
    $top_areas = $vagaModel->getTopAreasPorCidade($cidade_buscada, 15, $filtros);
} else {
    $top_areas = $vagaModel->getTopAreasPorRegiaoComFiltros($regiao_selecionada, 15, $filtros);
}

if ($top_areas === false) {
    $top_areas = [];
}

// Buscar demanda detalhada por região
$demanda_detalhada = $vagaModel->getDemandaPorRegiao($regiao_selecionada);
if ($demanda_detalhada === false) {
    $demanda_detalhada = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Áreas com Mais Demanda - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Descubra as áreas profissionais com maior demanda na sua região no TalentsHUB.">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_DIR; ?>/public/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_DIR; ?>/public/images/favicon.ico">
    
    <style>
        .demanda-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-radius: var(--border-radius);
        }
        
        .stats-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .area-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }
        
        .area-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }
        
        .area-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary-color);
        }
        
        .area-rank {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--primary-color);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .area-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            padding-right: 40px;
        }
        
        .area-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            display: block;
        }
        
        .stat-label {
            font-size: 0.875rem;
            color: var(--secondary-color);
            margin-top: 0.25rem;
        }
        
        .salario-info {
            background: var(--light-color);
            padding: 0.75rem;
            border-radius: var(--border-radius);
            margin-top: 1rem;
        }
        
        .salario-range {
            font-weight: 600;
            color: var(--success-color);
        }
        
        .regiao-selector {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .regiao-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .regiao-btn {
            padding: 0.5rem 1rem;
            border: 2px solid var(--border-color);
            background: white;
            color: var(--secondary-color);
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .regiao-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .regiao-btn.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .cidade-sugestao {
            background: var(--light-color);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 0.5rem 1rem;
            margin-bottom: 0.5rem;
            transition: var(--transition);
        }
        
        .cidade-sugestao:hover {
            background: var(--primary-color);
            color: white;
            text-decoration: none;
        }
        
        .busca-cidade-form {
            background: var(--light-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .sugestoes-container {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            background: white;
            padding: 0.5rem;
        }
        
        .filtros-ativos {
            background: var(--light-color);
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .filtro-badge {
            background: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            margin: 0.25rem;
            display: inline-block;
        }
        
        .form-select, .form-control {
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            transition: var(--transition);
        }
        
        .form-select:focus, .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .filtros-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
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
        
        .chart-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .trend-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .trend-up {
            color: var(--success-color);
        }
        
        .trend-down {
            color: var(--danger-color);
        }
        
        .trend-stable {
            color: var(--warning-color);
        }
        
        @media (max-width: 768px) {
            .demanda-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
            
            .hero-section {
                padding: 2rem 0;
            }
            
            .area-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .regiao-buttons {
                justify-content: center;
            }
        }
    </style>
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
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_DIR; ?>/app/views/vagas.php">
                            <i class="fas fa-briefcase me-1"></i>Vagas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_DIR; ?>/app/views/empresas.php">
                            <i class="fas fa-building me-1"></i>Empresas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_DIR; ?>/app/views/candidatos.php">
                            <i class="fas fa-users me-1"></i>Candidatos
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex">
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if ($_SESSION['usuario_tipo'] === 'candidato'): ?>
                                    <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/painel_candidato.php">
                                        <i class="fas fa-tachometer-alt me-2"></i>Meu Painel
                                    </a></li>
                                <?php elseif ($_SESSION['usuario_tipo'] === 'empresa'): ?>
                                    <li><a class="dropdown-item" href="<?php echo BASE_DIR; ?>/app/views/painel_empresa.php">
                                        <i class="fas fa-tachometer-alt me-2"></i>Meu Painel
                                    </a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
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

    <div class="demanda-container">
        <!-- Hero Section -->
        <div class="hero-section">
            <div class="container text-center">
                <h1 class="display-4 fw-bold mb-3">
                    <i class="fas fa-chart-line me-3"></i>Áreas com Mais Demanda
                </h1>
                <p class="lead mb-4">
                    Descubra as áreas profissionais com maior demanda na sua região e encontre as melhores oportunidades de carreira.
                </p>
                <?php if ($busca_personalizada): ?>
                    <div class="alert alert-light d-inline-block">
                        <i class="fas fa-search me-2"></i>
                        <strong>Buscando em:</strong> <?php echo htmlspecialchars($cidade_buscada); ?>
                        <?php if (!empty($sugestoes_cidades)): ?>
                            <br><small class="text-muted">
                                <?php echo count($sugestoes_cidades); ?> localização(ões) encontrada(s)
                            </small>
                        <?php endif; ?>
                    </div>
                <?php elseif (!empty($regiao_selecionada)): ?>
                    <div class="alert alert-light d-inline-block">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <strong>Região selecionada:</strong> <?php echo htmlspecialchars($regiao_selecionada); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Seletor de Região e Busca por Cidade -->
        <div class="regiao-selector">
            <h3 class="mb-3">
                <i class="fas fa-map-marked-alt me-2"></i>Buscar por Região ou Cidade
            </h3>
            <p class="text-muted mb-3">Escolha uma região predefinida ou digite o nome de uma cidade específica:</p>
            
            <!-- Busca por Cidade -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <form method="GET" action="" class="d-flex">
                        <input type="hidden" name="regiao" value="">
                        <input type="text" 
                               name="cidade" 
                               class="form-control me-2" 
                               placeholder="Digite o nome da cidade (ex: São Paulo, Rio de Janeiro, Belo Horizonte...)"
                               value="<?php echo htmlspecialchars($cidade_buscada); ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>Buscar
                        </button>
                    </form>
                    
                    <?php if (!empty($sugestoes_cidades) && count($sugestoes_cidades) > 1): ?>
                        <div class="mt-2">
                            <small class="text-muted">Cidades encontradas:</small>
                            <div class="mt-1">
                                <?php foreach ($sugestoes_cidades as $sugestao): ?>
                                    <a href="<?php echo BASE_DIR; ?>/app/views/demanda_regiao.php?cidade=<?php echo urlencode($sugestao['localizacao']); ?>" 
                                       class="badge bg-light text-dark me-1 mb-1 text-decoration-none">
                                        <?php echo htmlspecialchars($sugestao['localizacao']); ?> (<?php echo $sugestao['total_vagas']; ?>)
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <a href="<?php echo BASE_DIR; ?>/app/views/demanda_regiao.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Limpar Busca
                    </a>
                </div>
            </div>
            
            <!-- Regiões Predefinidas -->
            <div class="mb-3">
                <h5 class="mb-2">Ou escolha uma região predefinida:</h5>
                <div class="regiao-buttons">
                    <a href="<?php echo BASE_DIR; ?>/app/views/demanda_regiao.php" 
                       class="regiao-btn <?php echo (empty($regiao_selecionada) && empty($cidade_buscada)) ? 'active' : ''; ?>">
                        <i class="fas fa-globe"></i>
                        Todas as Regiões
                    </a>
                    <?php foreach ($regioes as $regiao): ?>
                        <a href="<?php echo BASE_DIR; ?>/app/views/demanda_regiao.php?regiao=<?php echo urlencode($regiao['regiao']); ?>" 
                           class="regiao-btn <?php echo ($regiao_selecionada == $regiao['regiao']) ? 'active' : ''; ?>">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($regiao['regiao']); ?>
                            <span class="badge bg-secondary ms-1"><?php echo $regiao['total_vagas']; ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Filtros Avançados -->
        <div class="regiao-selector">
            <h3 class="mb-3">
                <i class="fas fa-filter me-2"></i>Filtros Avançados
            </h3>
            <p class="text-muted mb-3">Refine sua busca aplicando filtros específicos:</p>
            
            <form method="GET" action="" class="row g-3">
                <!-- Manter parâmetros existentes -->
                <input type="hidden" name="regiao" value="<?php echo htmlspecialchars($regiao_selecionada); ?>">
                <input type="hidden" name="cidade" value="<?php echo htmlspecialchars($cidade_buscada); ?>">
                
                <div class="col-md-3">
                    <label for="area" class="form-label">Área</label>
                    <select id="area" name="area" class="form-select">
                        <option value="">Todas as Áreas</option>
                        <option value="tecnologia" <?php echo ($filtro_area == 'tecnologia') ? 'selected' : ''; ?>>Tecnologia</option>
                        <option value="marketing" <?php echo ($filtro_area == 'marketing') ? 'selected' : ''; ?>>Marketing</option>
                        <option value="vendas" <?php echo ($filtro_area == 'vendas') ? 'selected' : ''; ?>>Vendas</option>
                        <option value="rh" <?php echo ($filtro_area == 'rh') ? 'selected' : ''; ?>>Recursos Humanos</option>
                        <option value="financeiro" <?php echo ($filtro_area == 'financeiro') ? 'selected' : ''; ?>>Financeiro</option>
                        <option value="administrativo" <?php echo ($filtro_area == 'administrativo') ? 'selected' : ''; ?>>Administrativo</option>
                        <option value="design" <?php echo ($filtro_area == 'design') ? 'selected' : ''; ?>>Design</option>
                        <option value="juridico" <?php echo ($filtro_area == 'juridico') ? 'selected' : ''; ?>>Jurídico</option>
                        <option value="saude" <?php echo ($filtro_area == 'saude') ? 'selected' : ''; ?>>Saúde</option>
                        <option value="educacao" <?php echo ($filtro_area == 'educacao') ? 'selected' : ''; ?>>Educação</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="modalidade" class="form-label">Modalidade</label>
                    <select id="modalidade" name="modalidade" class="form-select">
                        <option value="">Todas as Modalidades</option>
                        <option value="presencial" <?php echo ($filtro_modalidade == 'presencial') ? 'selected' : ''; ?>>Presencial</option>
                        <option value="remoto" <?php echo ($filtro_modalidade == 'remoto') ? 'selected' : ''; ?>>Remoto</option>
                        <option value="hibrido" <?php echo ($filtro_modalidade == 'hibrido') ? 'selected' : ''; ?>>Híbrido</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="nivel_experiencia" class="form-label">Nível de Experiência</label>
                    <select id="nivel_experiencia" name="nivel_experiencia" class="form-select">
                        <option value="">Todos os Níveis</option>
                        <option value="estagiario" <?php echo ($filtro_nivel == 'estagiario') ? 'selected' : ''; ?>>Estagiário</option>
                        <option value="junior" <?php echo ($filtro_nivel == 'junior') ? 'selected' : ''; ?>>Júnior</option>
                        <option value="pleno" <?php echo ($filtro_nivel == 'pleno') ? 'selected' : ''; ?>>Pleno</option>
                        <option value="senior" <?php echo ($filtro_nivel == 'senior') ? 'selected' : ''; ?>>Sênior</option>
                        <option value="especialista" <?php echo ($filtro_nivel == 'especialista') ? 'selected' : ''; ?>>Especialista</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="tipo_contrato" class="form-label">Tipo de Contrato</label>
                    <select id="tipo_contrato" name="tipo_contrato" class="form-select">
                        <option value="">Todos os Tipos</option>
                        <option value="clt" <?php echo ($filtro_contrato == 'clt') ? 'selected' : ''; ?>>CLT</option>
                        <option value="pj" <?php echo ($filtro_contrato == 'pj') ? 'selected' : ''; ?>>PJ</option>
                        <option value="estagio" <?php echo ($filtro_contrato == 'estagio') ? 'selected' : ''; ?>>Estágio</option>
                        <option value="trainee" <?php echo ($filtro_contrato == 'trainee') ? 'selected' : ''; ?>>Trainee</option>
                        <option value="freelancer" <?php echo ($filtro_contrato == 'freelancer') ? 'selected' : ''; ?>>Freelancer</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i>Aplicar Filtros
                        </button>
                        <a href="<?php echo BASE_DIR; ?>/app/views/demanda_regiao.php<?php echo !empty($regiao_selecionada) ? '?regiao=' . urlencode($regiao_selecionada) : ''; ?><?php echo !empty($cidade_buscada) ? (empty($regiao_selecionada) ? '?' : '&') . 'cidade=' . urlencode($cidade_buscada) : ''; ?>" 
                           class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Limpar Filtros
                        </a>
                    </div>
                </div>
            </form>
            
            <?php if (!empty($filtros)): ?>
                <div class="mt-3">
                    <h6 class="mb-2">Filtros Ativos:</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($filtros as $tipo => $valor): ?>
                            <span class="badge bg-primary">
                                <?php 
                                $labels = [
                                    'area' => 'Área',
                                    'modalidade' => 'Modalidade', 
                                    'nivel_experiencia' => 'Nível',
                                    'tipo_contrato' => 'Contrato'
                                ];
                                echo $labels[$tipo] ?? ucfirst($tipo);
                                ?>: <?php echo ucfirst($valor); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($top_areas)): ?>
            <!-- Estatísticas Gerais -->
            <div class="stats-card">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo count($top_areas); ?></span>
                            <span class="stat-label">Áreas Analisadas</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo array_sum(array_column($top_areas, 'total_vagas')); ?></span>
                            <span class="stat-label">Total de Vagas</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo array_sum(array_column($top_areas, 'total_empresas')); ?></span>
                            <span class="stat-label">Empresas Contratando</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <span class="stat-number">R$ <?php echo number_format(array_sum(array_column($top_areas, 'salario_medio_min')) / count($top_areas), 0, ',', '.'); ?></span>
                            <span class="stat-label">Salário Médio</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Áreas -->
            <div class="chart-container">
                <h3 class="mb-4">
                    <i class="fas fa-trophy me-2"></i>
                    <?php if ($busca_personalizada): ?>
                        Top Áreas em "<?php echo htmlspecialchars($cidade_buscada); ?>"
                    <?php elseif (!empty($regiao_selecionada)): ?>
                        Top Áreas em <?php echo htmlspecialchars($regiao_selecionada); ?>
                    <?php else: ?>
                        Top Áreas em Todas as Regiões
                    <?php endif; ?>
                    
                    <?php if (!empty($filtros)): ?>
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-filter me-1"></i>Filtros aplicados: 
                            <?php 
                            $filtros_texto = [];
                            foreach ($filtros as $tipo => $valor) {
                                $labels = [
                                    'area' => 'Área',
                                    'modalidade' => 'Modalidade', 
                                    'nivel_experiencia' => 'Nível',
                                    'tipo_contrato' => 'Contrato'
                                ];
                                $filtros_texto[] = ($labels[$tipo] ?? ucfirst($tipo)) . ': ' . ucfirst($valor);
                            }
                            echo implode(', ', $filtros_texto);
                            ?>
                        </small>
                    <?php endif; ?>
                </h3>
                
                <div class="row">
                    <?php foreach ($top_areas as $index => $area): ?>
                        <div class="col-lg-6 mb-3">
                            <div class="area-card">
                                <div class="area-rank"><?php echo $index + 1; ?></div>
                                
                                <h4 class="area-title">
                                    <?php echo ucfirst(htmlspecialchars($area['area'])); ?>
                                </h4>
                                
                                <div class="area-stats">
                                    <div class="stat-item">
                                        <span class="stat-number"><?php echo $area['total_vagas']; ?></span>
                                        <span class="stat-label">Vagas</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number"><?php echo $area['total_empresas']; ?></span>
                                        <span class="stat-label">Empresas</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number"><?php echo round($area['total_vagas'] / $area['total_empresas'], 1); ?></span>
                                        <span class="stat-label">Vagas/Empresa</span>
                                    </div>
                                </div>
                                
                                <?php if ($area['salario_medio_min'] > 0 || $area['salario_medio_max'] > 0): ?>
                                    <div class="salario-info">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Faixa Salarial:</span>
                                            <span class="salario-range">
                                                <?php if ($area['salario_medio_min'] > 0 && $area['salario_medio_max'] > 0): ?>
                                                    R$ <?php echo number_format($area['salario_medio_min'], 0, ',', '.'); ?> - 
                                                    R$ <?php echo number_format($area['salario_medio_max'], 0, ',', '.'); ?>
                                                <?php elseif ($area['salario_medio_min'] > 0): ?>
                                                    A partir de R$ <?php echo number_format($area['salario_medio_min'], 0, ',', '.'); ?>
                                                <?php else: ?>
                                                    Salário a combinar
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="mt-3">
                                    <?php 
                                    $link_vagas = BASE_DIR . '/app/views/vagas.php?area=' . urlencode($area['area']);
                                    
                                    // Adicionar localização
                                    if ($busca_personalizada) {
                                        $link_vagas .= '&localizacao=' . urlencode($cidade_buscada);
                                    } elseif (!empty($regiao_selecionada)) {
                                        $link_vagas .= '&localizacao=' . urlencode($regiao_selecionada);
                                    }
                                    
                                    // Adicionar outros filtros
                                    if (!empty($filtro_modalidade)) {
                                        $link_vagas .= '&modalidade=' . urlencode($filtro_modalidade);
                                    }
                                    if (!empty($filtro_nivel)) {
                                        $link_vagas .= '&nivel_experiencia=' . urlencode($filtro_nivel);
                                    }
                                    if (!empty($filtro_contrato)) {
                                        $link_vagas .= '&tipo_contrato=' . urlencode($filtro_contrato);
                                    }
                                    ?>
                                    <a href="<?php echo $link_vagas; ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-search me-1"></i>Ver Vagas
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Estado Vazio -->
            <div class="empty-state">
                <i class="fas fa-chart-line"></i>
                <h3>Nenhum dado encontrado</h3>
                <p>Não foram encontradas vagas para a região selecionada. Tente selecionar outra região ou verifique novamente mais tarde.</p>
                <a href="<?php echo BASE_DIR; ?>/app/views/vagas.php" class="btn btn-primary">
                    <i class="fas fa-briefcase me-2"></i>Ver Todas as Vagas
                </a>
            </div>
        <?php endif; ?>

        <!-- Call to Action -->
        <div class="text-center mt-5">
            <div class="card bg-light">
                <div class="card-body py-5">
                    <h3 class="mb-3">Encontrou sua área ideal?</h3>
                    <p class="text-muted mb-4">Explore as vagas disponíveis e encontre a oportunidade perfeita para sua carreira.</p>
                    <a href="<?php echo BASE_DIR; ?>/app/views/vagas.php" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-search me-2"></i>Buscar Vagas
                    </a>
                    <?php if (!isset($_SESSION['usuario_id'])): ?>
                        <a href="<?php echo BASE_DIR; ?>/app/views/auth.php" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Cadastrar-se
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
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
    <script src="<?php echo BASE_DIR; ?>/public/js/main.js"></script>
</body>
</html>
