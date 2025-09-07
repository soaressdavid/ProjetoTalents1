<?php
// Arquivo: app/views/criar_vaga.php
// TalentsHUB - Criar Nova Vaga

require_once __DIR__ . '/../utils/init.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'empresa') {
    header("Location: " . BASE_DIR . "/app/views/auth.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Nova Vaga - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Publique uma nova oportunidade de trabalho no TalentsHUB.">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_DIR; ?>/public/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_DIR; ?>/public/images/favicon.ico">
    
    <style>
        .form-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 0.75rem;
            color: var(--primary-color);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            transition: var(--transition);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25);
        }
        
        .form-text {
            font-size: 0.75rem;
            color: var(--secondary-color);
            margin-top: 0.25rem;
        }
        
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 2rem;
            font-weight: 500;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .btn-outline-secondary {
            border-color: var(--border-color);
            color: var(--secondary-color);
            padding: 0.75rem 2rem;
            font-weight: 500;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }
        
        .btn-outline-secondary:hover {
            background: var(--light-color);
            border-color: var(--secondary-color);
            color: var(--dark-color);
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
        
        .required {
            color: #dc3545;
        }
        
        .form-floating {
            margin-bottom: 1rem;
        }
        
        .form-floating > .form-control {
            height: calc(3.5rem + 2px);
            padding: 1rem 0.75rem;
        }
        
        .form-floating > label {
            padding: 1rem 0.75rem;
        }
        
        /* Estilos do sidebar - igual ao gerenciar vagas */
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
            .form-container {
                padding: 1.5rem;
            }
            
            .section-title {
                font-size: 1.125rem;
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
                            <a href="#" class="nav-link active">
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
                            <li class="breadcrumb-item active">Criar Nova Vaga</li>
                        </ol>
                    </nav>
                    
                    <!-- Header -->
                    <div class="dashboard-header">
                        <h1 class="fw-bold text-primary mb-2">
                            <i class="fas fa-plus-circle me-2"></i>Criar Nova Vaga
                        </h1>
                        <p class="welcome-text">Publique uma nova oportunidade de trabalho e encontre os melhores talentos.</p>
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
                    
                    <!-- Formulário -->
                    <div class="form-container">
                        <form action="<?php echo BASE_DIR; ?>/app/controllers/VagaController.php" method="POST" id="criarVagaForm">
            <input type="hidden" name="action" value="create">
            
                            <!-- Informações Básicas -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-info-circle"></i>Informações Básicas
                                </h3>
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="titulo" name="titulo" 
                                                   placeholder="Título da vaga" required>
                                            <label for="titulo">Título da Vaga <span class="required">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" id="area" name="area" required>
                                                <option value="">Selecione a área</option>
                                                <option value="tecnologia">Tecnologia</option>
                                                <option value="marketing">Marketing</option>
                                                <option value="vendas">Vendas</option>
                                                <option value="rh">Recursos Humanos</option>
                                                <option value="financeiro">Financeiro</option>
                                                <option value="operacoes">Operações</option>
                                                <option value="juridico">Jurídico</option>
                                                <option value="comercial">Comercial</option>
                                                <option value="administrativo">Administrativo</option>
                                                <option value="outros">Outros</option>
                                            </select>
                                            <label for="area">Área <span class="required">*</span></label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" id="descricao" name="descricao" 
                                              placeholder="Descrição da vaga" style="height: 120px" required></textarea>
                                    <label for="descricao">Descrição da Vaga <span class="required">*</span></label>
                                    <div class="form-text">Descreva as responsabilidades e atividades da vaga.</div>
                                </div>
                                
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" id="requisitos" name="requisitos" 
                                              placeholder="Requisitos" style="height: 120px" required></textarea>
                                    <label for="requisitos">Requisitos <span class="required">*</span></label>
                                    <div class="form-text">Liste os requisitos necessários para a vaga.</div>
                                </div>
                                
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" id="beneficios" name="beneficios" 
                                              placeholder="Benefícios" style="height: 100px"></textarea>
                                    <label for="beneficios">Benefícios</label>
                                    <div class="form-text">Liste os benefícios oferecidos (opcional).</div>
                                </div>
                            </div>
                            
                            <!-- Detalhes da Vaga -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-cogs"></i>Detalhes da Vaga
                                </h3>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" id="nivel_experiencia" name="nivel_experiencia" required>
                                                <option value="">Nível de experiência</option>
                                                <option value="estagiario">Estagiário</option>
                                                <option value="junior">Júnior</option>
                                                <option value="pleno">Pleno</option>
                                                <option value="senior">Sênior</option>
                                                <option value="especialista">Especialista</option>
                                            </select>
                                            <label for="nivel_experiencia">Nível de Experiência <span class="required">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" id="tipo_contrato" name="tipo_contrato" required>
                                                <option value="">Tipo de contrato</option>
                                                <option value="clt">CLT</option>
                                                <option value="pj">PJ</option>
                                                <option value="estagio">Estágio</option>
                                                <option value="trainee">Trainee</option>
                                                <option value="freelancer">Freelancer</option>
                                            </select>
                                            <label for="tipo_contrato">Tipo de Contrato <span class="required">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" id="modalidade" name="modalidade" required>
                                                <option value="">Modalidade</option>
                                                <option value="presencial">Presencial</option>
                                                <option value="remoto">Remoto</option>
                                                <option value="hibrido">Híbrido</option>
                                            </select>
                                            <label for="modalidade">Modalidade <span class="required">*</span></label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="localizacao" name="localizacao" 
                                                   placeholder="Localização" required>
                                            <label for="localizacao">Localização <span class="required">*</span></label>
                                            <div class="form-text">Cidade, estado ou "Remoto" para trabalho remoto.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="date" class="form-control" id="data_limite" name="data_limite">
                                            <label for="data_limite">Data Limite para Candidaturas</label>
                                            <div class="form-text">Deixe em branco para não definir limite (opcional).</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Remuneração -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-dollar-sign"></i>Remuneração
                                </h3>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="number" class="form-control" id="salario_min" name="salario_min" 
                                                   placeholder="Salário mínimo" min="0" step="0.01">
                                            <label for="salario_min">Salário Mínimo (R$)</label>
                                            <div class="form-text">Valor mínimo oferecido (opcional).</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="number" class="form-control" id="salario_max" name="salario_max" 
                                                   placeholder="Salário máximo" min="0" step="0.01">
                                            <label for="salario_max">Salário Máximo (R$)</label>
                                            <div class="form-text">Valor máximo oferecido (opcional).</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Botões -->
                            <div class="d-flex justify-content-between">
                                <a href="<?php echo BASE_DIR; ?>/app/views/painel_empresa.php" 
                                   class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Publicar Vaga
                                </button>
                            </div>
        </form>
    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_DIR; ?>/public/js/main.js"></script>
    
    <script>
        // Validação do formulário
        document.getElementById('criarVagaForm').addEventListener('submit', function(e) {
            const salarioMin = parseFloat(document.getElementById('salario_min').value) || 0;
            const salarioMax = parseFloat(document.getElementById('salario_max').value) || 0;
            
            if (salarioMin > 0 && salarioMax > 0 && salarioMin > salarioMax) {
                e.preventDefault();
                alert('O salário mínimo não pode ser maior que o salário máximo.');
                return false;
            }
            
            // Definir data limite padrão se não informada
            const dataLimite = document.getElementById('data_limite').value;
            if (!dataLimite) {
                const hoje = new Date();
                const proximoMes = new Date(hoje.getFullYear(), hoje.getMonth() + 1, hoje.getDate());
                document.getElementById('data_limite').value = proximoMes.toISOString().split('T')[0];
            }
        });
        
        // Auto-completar localização baseada na modalidade
        document.getElementById('modalidade').addEventListener('change', function() {
            const localizacao = document.getElementById('localizacao');
            if (this.value === 'remoto') {
                localizacao.value = 'Remoto';
                localizacao.readOnly = true;
            } else {
                localizacao.readOnly = false;
                if (localizacao.value === 'Remoto') {
                    localizacao.value = '';
                }
            }
        });
    </script>
</body>
</html>
