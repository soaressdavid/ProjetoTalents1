<?php
require_once __DIR__ . '/../utils/init.php';

$vagaModel = new Vaga();

$termo_busca = $_GET['termo_busca'] ?? '';
$filtro_localizacao = $_GET['filtro_localizacao'] ?? '';

// Filtros adicionais do menu
$modalidade = $_GET['modalidade'] ?? '';
$tipo_contrato = $_GET['tipo_contrato'] ?? '';
$nivel_experiencia = $_GET['nivel_experiencia'] ?? '';
$area = $_GET['area'] ?? '';

// Buscar sugestões para autocomplete
$sugestoes_localizacao = [];
$sugestoes_area = [];

if (!empty($filtro_localizacao)) {
    $sugestoes_localizacao = $vagaModel->getLocalizacoesDisponiveis($filtro_localizacao);
    if ($sugestoes_localizacao === false) {
        $sugestoes_localizacao = [];
    }
}

if (!empty($area)) {
    $sugestoes_area = $vagaModel->getAreasDisponiveis($area);
    if ($sugestoes_area === false) {
        $sugestoes_area = [];
    }
}

$filters = [];
if (!empty($termo_busca)) {
    $filters['search'] = $termo_busca;
}
if (!empty($filtro_localizacao)) {
    $filters['localizacao'] = $filtro_localizacao;
}
if (!empty($modalidade)) {
    $filters['modalidade'] = $modalidade;
}
if (!empty($tipo_contrato)) {
    $filters['tipo_contrato'] = $tipo_contrato;
}
if (!empty($nivel_experiencia)) {
    $filters['nivel_experiencia'] = $nivel_experiencia;
}
if (!empty($area)) {
    $filters['area'] = $area;
}

$vagas = $vagaModel->getAll(20, 0, $filters);

// Verificar se a consulta retornou dados válidos
if ($vagas === false) {
    $vagas = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR"></html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vagas Disponíveis - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Encontre as melhores oportunidades de trabalho no TalentsHUB.">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_DIR; ?>/public/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_DIR; ?>/public/images/favicon.ico">
    
    <style>
        .vagas-container {
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
        
        .vaga-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }
        
        .vaga-card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }
        
        .vaga-header {
            display: flex;
            justify-content: space-between;
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
        
        .vaga-company {
            color: var(--primary-color);
            font-size: 0.875rem;
            margin: 0.25rem 0;
        }
        
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
        
        .btn-vaga {
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
        
        .btn-apply {
            background: #10b981;
            color: white;
        }
        
        .btn-apply:hover {
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
        
        .sugestoes-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid var(--border-color);
            border-top: none;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            max-height: 200px;
            overflow-y: auto;
        }
        
        .sugestao-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            border-bottom: 1px solid var(--border-color);
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .sugestao-item:hover {
            background: var(--light-color);
            color: var(--primary-color);
        }
        
        .sugestao-item:last-child {
            border-bottom: none;
        }
        
        .sugestao-item i {
            color: var(--primary-color);
        }
        
        .position-relative {
            position: relative;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        @media (max-width: 768px) {
            .vagas-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
            
            .search-card, .filter-card {
                padding: 1.5rem;
            }
            
            .vaga-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .vaga-info {
                grid-template-columns: 1fr;
            }
            
            .vaga-actions {
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

    <div class="vagas-container">
        <!-- Filtros de Busca -->
        <div class="filter-card">
            <h3 class="filter-title">
                <i class="fas fa-search me-2"></i>Filtros de Busca
            </h3>
            
            <form method="GET" action="">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="termo_busca" class="form-label">Palavra-chave</label>
                            <input type="text" id="termo_busca" name="termo_busca" class="form-control" 
                                   value="<?php echo htmlspecialchars($termo_busca); ?>" 
                                   placeholder="Pesquisar por título ou descrição...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="filtro_localizacao" class="form-label">Localização</label>
                            <div class="position-relative">
                                <input type="text" 
                                       id="filtro_localizacao" 
                                       name="filtro_localizacao" 
                                       class="form-control" 
                                       value="<?php echo htmlspecialchars($filtro_localizacao); ?>" 
                                       placeholder="Digite cidade, bairro ou estado..."
                                       autocomplete="off">
                                
                                <?php if (!empty($sugestoes_localizacao) && count($sugestoes_localizacao) > 1): ?>
                                    <div class="sugestoes-dropdown" id="sugestoes-localizacao">
                                        <?php foreach ($sugestoes_localizacao as $sugestao): ?>
                                            <div class="sugestao-item" onclick="selecionarLocalizacao('<?php echo htmlspecialchars($sugestao['localizacao']); ?>')">
                                                <i class="fas fa-map-marker-alt me-2"></i>
                                                <?php echo htmlspecialchars($sugestao['localizacao']); ?>
                                                <span class="badge bg-secondary ms-2"><?php echo $sugestao['total_vagas']; ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Sugestões rápidas -->
                            <div class="mt-2">
                                <small class="text-muted">Sugestões:</small>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="definirLocalizacao('Remoto')">Remoto</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="definirLocalizacao('São Paulo')">São Paulo</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="definirLocalizacao('Rio de Janeiro')">Rio de Janeiro</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="definirLocalizacao('Belo Horizonte')">Belo Horizonte</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="modalidade" class="form-label">Modalidade</label>
                            <select id="modalidade" name="modalidade" class="form-select">
                                <option value="">Todas as Modalidades</option>
                                <option value="presencial" <?php echo ($modalidade == 'presencial') ? 'selected' : ''; ?>>Presencial</option>
                                <option value="remoto" <?php echo ($modalidade == 'remoto') ? 'selected' : ''; ?>>Remoto</option>
                                <option value="hibrido" <?php echo ($modalidade == 'hibrido') ? 'selected' : ''; ?>>Híbrido</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="nivel_experiencia" class="form-label">Nível</label>
                            <select id="nivel_experiencia" name="nivel_experiencia" class="form-select">
                                <option value="">Todos os Níveis</option>
                                <option value="estagiario" <?php echo ($nivel_experiencia == 'estagiario') ? 'selected' : ''; ?>>Estagiário</option>
                                <option value="junior" <?php echo ($nivel_experiencia == 'junior') ? 'selected' : ''; ?>>Júnior</option>
                                <option value="pleno" <?php echo ($nivel_experiencia == 'pleno') ? 'selected' : ''; ?>>Pleno</option>
                                <option value="senior" <?php echo ($nivel_experiencia == 'senior') ? 'selected' : ''; ?>>Sênior</option>
                                <option value="especialista" <?php echo ($nivel_experiencia == 'especialista') ? 'selected' : ''; ?>>Especialista</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="tipo_contrato" class="form-label">Tipo de Contrato</label>
                            <select id="tipo_contrato" name="tipo_contrato" class="form-select">
                                <option value="">Todos os Tipos</option>
                                <option value="clt" <?php echo ($tipo_contrato == 'clt') ? 'selected' : ''; ?>>CLT</option>
                                <option value="pj" <?php echo ($tipo_contrato == 'pj') ? 'selected' : ''; ?>>PJ</option>
                                <option value="estagio" <?php echo ($tipo_contrato == 'estagio') ? 'selected' : ''; ?>>Estágio</option>
                                <option value="trainee" <?php echo ($tipo_contrato == 'trainee') ? 'selected' : ''; ?>>Trainee</option>
                                <option value="freelancer" <?php echo ($tipo_contrato == 'freelancer') ? 'selected' : ''; ?>>Freelancer</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="area" class="form-label">Área</label>
                            <div class="position-relative">
                                <input type="text" 
                                       id="area" 
                                       name="area" 
                                       class="form-control" 
                                       value="<?php echo htmlspecialchars($area); ?>" 
                                       placeholder="Digite a área desejada..."
                                       autocomplete="off">
                                
                                <?php if (!empty($sugestoes_area) && count($sugestoes_area) > 1): ?>
                                    <div class="sugestoes-dropdown" id="sugestoes-area">
                                        <?php foreach ($sugestoes_area as $sugestao): ?>
                                            <div class="sugestao-item" onclick="selecionarArea('<?php echo htmlspecialchars($sugestao['area']); ?>')">
                                                <i class="fas fa-briefcase me-2"></i>
                                                <?php echo htmlspecialchars($sugestao['area']); ?>
                                                <span class="badge bg-secondary ms-2"><?php echo $sugestao['total_vagas']; ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Sugestões rápidas -->
                            <div class="mt-2">
                                <small class="text-muted">Sugestões:</small>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="definirArea('tecnologia')">Tecnologia</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="definirArea('marketing')">Marketing</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="definirArea('vendas')">Vendas</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="definirArea('rh')">RH</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex">
                                <button type="submit" class="btn-search">
                                    <i class="fas fa-search me-2"></i>Pesquisar
                                </button>
                                <a href="<?php echo BASE_DIR; ?>/app/views/calculadora_deslocamento.php" class="btn btn-outline-primary ms-2">
                                    <i class="fas fa-route me-2"></i>Calcular Deslocamento
                                </a>
                                <a href="<?php echo BASE_DIR; ?>/app/views/vagas.php" class="btn-clear">
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
                    <i class="fas fa-briefcase me-2"></i>Vagas Disponíveis
                    <span class="badge bg-primary ms-2"><?php echo count($vagas); ?></span>
                </h2>
            </div>
            
            <?php if (empty($vagas)): ?>
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3>Nenhuma vaga encontrada</h3>
                    <p>Tente ajustar os filtros de busca para encontrar mais oportunidades.</p>
                    <a href="<?php echo BASE_DIR; ?>/app/views/vagas.php" class="btn btn-primary">
                        <i class="fas fa-refresh me-2"></i>Limpar Filtros
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($vagas as $vaga): ?>
                    <div class="vaga-card">
                        <div class="vaga-header">
                            <div>
                                <h3 class="vaga-title"><?php echo htmlspecialchars($vaga['titulo']); ?></h3>
                                <p class="vaga-company"><?php echo htmlspecialchars($vaga['razao_social'] ?? $vaga['empresa_nome'] ?? 'Empresa'); ?></p>
                            </div>
                        </div>
                        
                        <div class="vaga-info">
                            <div class="info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($vaga['localizacao']); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-briefcase"></i>
                                <span><?php echo ucfirst($vaga['nivel_experiencia']); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <span><?php echo ucfirst($vaga['tipo_contrato']); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-home"></i>
                                <span><?php echo ucfirst($vaga['modalidade']); ?></span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <p class="text-muted"><?php echo substr(strip_tags($vaga['descricao']), 0, 200) . '...'; ?></p>
                        </div>
                        
                        <div class="vaga-actions">
                            <a href="<?php echo BASE_DIR; ?>/app/views/vaga_detalhes.php?id=<?php echo $vaga['id']; ?>" 
                               class="btn-vaga btn-view">
                                <i class="fas fa-eye me-1"></i>Ver Detalhes
                            </a>
                            
                            <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_tipo'] === 'candidato'): ?>
                            <a href="<?php echo BASE_DIR; ?>/app/views/candidatar.php?vaga_id=<?php echo $vaga['id']; ?>" 
                               class="btn-vaga btn-apply">
                                <i class="fas fa-paper-plane me-1"></i>Candidatar-se
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_DIR; ?>/public/js/main.js"></script>
    
    <script>
        // Funções para seleção de localização
        function selecionarLocalizacao(localizacao) {
            document.getElementById('filtro_localizacao').value = localizacao;
            document.getElementById('sugestoes-localizacao').style.display = 'none';
        }
        
        function definirLocalizacao(localizacao) {
            document.getElementById('filtro_localizacao').value = localizacao;
        }
        
        // Funções para seleção de área
        function selecionarArea(area) {
            document.getElementById('area').value = area;
            document.getElementById('sugestoes-area').style.display = 'none';
        }
        
        function definirArea(area) {
            document.getElementById('area').value = area;
        }
        
        // Esconder sugestões ao clicar fora
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.position-relative')) {
                const dropdowns = document.querySelectorAll('.sugestoes-dropdown');
                dropdowns.forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
            }
        });
        
        // Mostrar/esconder sugestões ao digitar
        document.getElementById('filtro_localizacao').addEventListener('input', function() {
            const valor = this.value.trim();
            const dropdown = document.getElementById('sugestoes-localizacao');
            
            if (valor.length > 1) {
                // Fazer requisição AJAX para buscar sugestões
                fetch(`<?php echo BASE_DIR; ?>/api/sugestoes_localizacao.php?termo=${encodeURIComponent(valor)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            mostrarSugestoesLocalizacao(data);
                        } else {
                            dropdown.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao buscar sugestões:', error);
                    });
            } else {
                dropdown.style.display = 'none';
            }
        });
        
        document.getElementById('area').addEventListener('input', function() {
            const valor = this.value.trim();
            const dropdown = document.getElementById('sugestoes-area');
            
            if (valor.length > 1) {
                // Fazer requisição AJAX para buscar sugestões
                fetch(`<?php echo BASE_DIR; ?>/api/sugestoes_area.php?termo=${encodeURIComponent(valor)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            mostrarSugestoesArea(data);
                        } else {
                            dropdown.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao buscar sugestões:', error);
                    });
            } else {
                dropdown.style.display = 'none';
            }
        });
        
        function mostrarSugestoesLocalizacao(sugestoes) {
            const dropdown = document.getElementById('sugestoes-localizacao');
            dropdown.innerHTML = '';
            
            sugestoes.forEach(sugestao => {
                const item = document.createElement('div');
                item.className = 'sugestao-item';
                item.innerHTML = `
                    <div>
                        <i class="fas fa-map-marker-alt me-2"></i>
                        ${sugestao.localizacao}
                    </div>
                    <span class="badge bg-secondary">${sugestao.total_vagas}</span>
                `;
                item.onclick = () => selecionarLocalizacao(sugestao.localizacao);
                dropdown.appendChild(item);
            });
            
            dropdown.style.display = 'block';
        }
        
        function mostrarSugestoesArea(sugestoes) {
            const dropdown = document.getElementById('sugestoes-area');
            dropdown.innerHTML = '';
            
            sugestoes.forEach(sugestao => {
                const item = document.createElement('div');
                item.className = 'sugestao-item';
                item.innerHTML = `
                    <div>
                        <i class="fas fa-briefcase me-2"></i>
                        ${sugestao.area}
                    </div>
                    <span class="badge bg-secondary">${sugestao.total_vagas}</span>
                `;
                item.onclick = () => selecionarArea(sugestao.area);
                dropdown.appendChild(item);
            });
            
            dropdown.style.display = 'block';
        }
    </script>

</body>
</html>