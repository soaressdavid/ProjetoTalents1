<?php
require_once __DIR__ . '/../utils/init.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisar Salários - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Pesquise salários por cargo, localização e experiência no TalentsHUB.">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_DIR; ?>/public/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_DIR; ?>/public/images/favicon.ico">
    
    <style>
        .salaries-container {
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
        
        .salary-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }
        
        .salary-card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }
        
        .salary-amount {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
        }
        
        .salary-range {
            color: var(--secondary-color);
            font-size: 0.875rem;
        }
        
        .salary-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .info-item {
            text-align: center;
            padding: 0.5rem;
            background: #f8fafc;
            border-radius: var(--border-radius);
        }
        
        .info-label {
            font-size: 0.75rem;
            color: var(--secondary-color);
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .info-value {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark-color);
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
            .salaries-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
            
            .search-card {
                padding: 1.5rem;
            }
            
            .salary-info {
                grid-template-columns: 1fr;
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
                    <?php elseif ($_SESSION['usuario_tipo'] === 'empresa'): ?>
                        <a href="<?php echo BASE_DIR; ?>/app/views/painel_empresa.php">Meu Painel</a>
                    <?php endif; ?>
                    <a href="<?php echo BASE_DIR; ?>/app/controllers/LogoutController.php">Sair</a>
                <?php else: ?>
                    <a href="<?php echo BASE_DIR; ?>/app/views/auth.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="salaries-container">
        <!-- Filtros de Busca -->
        <div class="search-card">
            <h3 class="mb-4">
                <i class="fas fa-dollar-sign me-2"></i>Pesquisar Salários
            </h3>
            
            <form method="GET" action="">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="cargo" class="form-label">Cargo</label>
                            <input type="text" id="cargo" name="cargo" class="form-control" 
                                   placeholder="Ex: Desenvolvedor, Analista, Gerente">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="cidade" class="form-label">Cidade</label>
                            <select id="cidade" name="cidade" class="form-select">
                                <option value="">Todas as Cidades</option>
                                <option value="São Paulo">São Paulo</option>
                                <option value="Rio de Janeiro">Rio de Janeiro</option>
                                <option value="Belo Horizonte">Belo Horizonte</option>
                                <option value="Brasília">Brasília</option>
                                <option value="Salvador">Salvador</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="experiencia" class="form-label">Experiência</label>
                            <select id="experiencia" name="experiencia" class="form-select">
                                <option value="">Todas as Experiências</option>
                                <option value="junior">Júnior (0-2 anos)</option>
                                <option value="pleno">Pleno (3-5 anos)</option>
                                <option value="senior">Sênior (6+ anos)</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-search me-2"></i>Pesquisar Salários
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Resultados -->
        <div class="search-card">
            <h3 class="mb-4">
                <i class="fas fa-chart-bar me-2"></i>Salários por Cargo
            </h3>
            
            <!-- Dados de exemplo -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="salary-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 class="mb-1">Desenvolvedor PHP</h4>
                                <p class="text-muted mb-0">São Paulo, SP</p>
                            </div>
                            <span class="badge bg-primary">Pleno</span>
                        </div>
                        
                        <div class="text-center mb-3">
                            <p class="salary-amount">R$ 6.500</p>
                            <p class="salary-range">R$ 5.000 - R$ 8.000</p>
                        </div>
                        
                        <div class="salary-info">
                            <div class="info-item">
                                <div class="info-label">Média</div>
                                <div class="info-value">R$ 6.500</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Mínimo</div>
                                <div class="info-value">R$ 5.000</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Máximo</div>
                                <div class="info-value">R$ 8.000</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Amostras</div>
                                <div class="info-value">127</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-4">
                    <div class="salary-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 class="mb-1">Analista de Marketing</h4>
                                <p class="text-muted mb-0">Rio de Janeiro, RJ</p>
                            </div>
                            <span class="badge bg-success">Júnior</span>
                        </div>
                        
                        <div class="text-center mb-3">
                            <p class="salary-amount">R$ 4.200</p>
                            <p class="salary-range">R$ 3.500 - R$ 5.000</p>
                        </div>
                        
                        <div class="salary-info">
                            <div class="info-item">
                                <div class="info-label">Média</div>
                                <div class="info-value">R$ 4.200</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Mínimo</div>
                                <div class="info-value">R$ 3.500</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Máximo</div>
                                <div class="info-value">R$ 5.000</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Amostras</div>
                                <div class="info-value">89</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-4">
                    <div class="salary-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 class="mb-1">Gerente de Vendas</h4>
                                <p class="text-muted mb-0">Belo Horizonte, MG</p>
                            </div>
                            <span class="badge bg-warning">Sênior</span>
                        </div>
                        
                        <div class="text-center mb-3">
                            <p class="salary-amount">R$ 12.000</p>
                            <p class="salary-range">R$ 10.000 - R$ 15.000</p>
                        </div>
                        
                        <div class="salary-info">
                            <div class="info-item">
                                <div class="info-label">Média</div>
                                <div class="info-value">R$ 12.000</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Mínimo</div>
                                <div class="info-value">R$ 10.000</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Máximo</div>
                                <div class="info-value">R$ 15.000</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Amostras</div>
                                <div class="info-value">45</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-4">
                    <div class="salary-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 class="mb-1">Designer UX/UI</h4>
                                <p class="text-muted mb-0">Brasília, DF</p>
                            </div>
                            <span class="badge bg-info">Pleno</span>
                        </div>
                        
                        <div class="text-center mb-3">
                            <p class="salary-amount">R$ 7.800</p>
                            <p class="salary-range">R$ 6.000 - R$ 10.000</p>
                        </div>
                        
                        <div class="salary-info">
                            <div class="info-item">
                                <div class="info-label">Média</div>
                                <div class="info-value">R$ 7.800</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Mínimo</div>
                                <div class="info-value">R$ 6.000</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Máximo</div>
                                <div class="info-value">R$ 10.000</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Amostras</div>
                                <div class="info-value">73</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <p class="text-muted mb-3">Não encontrou o cargo que procura?</p>
                <a href="<?php echo BASE_DIR; ?>/app/views/vagas.php" class="btn btn-outline-primary">
                    <i class="fas fa-search me-2"></i>Buscar Vagas
                </a>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_DIR; ?>/public/js/main.js"></script>
</body>
</html>
