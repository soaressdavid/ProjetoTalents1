<?php
// Arquivo: app/views/editar_perfil_empresa.php
// TalentsHUB - Editar Perfil da Empresa

require_once __DIR__ . '/../utils/init.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'empresa') {
    header("Location: " . BASE_DIR . "/app/views/auth.php");
    exit();
}

$usuarioModel = new User();
$empresaModel = new Empresa();

$usuario = $usuarioModel->findById($_SESSION['usuario_id']);
$empresa = $empresaModel->findByUsuarioId($_SESSION['usuario_id']);

if (!$usuario) {
    $_SESSION['perfil_erro'] = "Usuário não encontrado.";
    header("Location: " . BASE_DIR . "/app/views/painel_empresa.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil da Empresa - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Edite as informações do perfil da sua empresa no TalentsHUB.">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_DIR; ?>/public/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_DIR; ?>/public/images/favicon.ico">
    
    <style>
        .profile-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .profile-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .btn-save {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-save:hover {
            background: var(--primary-dark);
            color: white;
        }
        
        .btn-cancel {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            padding: 0.75rem 2rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
            margin-right: 1rem;
        }
        
        .btn-cancel:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
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
        
        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
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
            .profile-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
            
            .profile-card {
                padding: 1.5rem;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn-cancel {
                margin-right: 0;
                margin-bottom: 1rem;
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
                            <a href="<?php echo BASE_DIR; ?>/app/views/buscar_candidatos.php" class="nav-link">
                                <i class="fas fa-search"></i>Buscar Candidatos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
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
                            <li class="breadcrumb-item active">Editar Perfil</li>
                        </ol>
                    </nav>
                    
                    <!-- Header -->
                    <div class="dashboard-header">
                        <h1 class="fw-bold text-primary mb-2">
                            <i class="fas fa-building me-2"></i>Editar Perfil da Empresa
                        </h1>
                        <p class="welcome-text">Atualize as informações do perfil da sua empresa.</p>
                    </div>
                    
                    <!-- Alertas -->
                    <?php if (isset($_SESSION['perfil_sucesso'])): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['perfil_sucesso']); ?>
                        </div>
                        <?php unset($_SESSION['perfil_sucesso']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['perfil_erro'])): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_SESSION['perfil_erro']); ?>
                        </div>
                        <?php unset($_SESSION['perfil_erro']); ?>
                    <?php endif; ?>
                    
                    <!-- Formulário -->
                    <div class="profile-card">
                        <form action="<?php echo BASE_DIR; ?>/app/controllers/PerfilController.php" method="POST">
                            <input type="hidden" name="action" value="update_perfil">
                            
                            <!-- Informações Básicas -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-info-circle"></i>Informações Básicas
                                </h3>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nome" class="form-label">Nome da Empresa *</label>
                                            <input type="text" id="nome" name="nome" class="form-control" 
                                                   value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email" class="form-label">Email *</label>
                                            <input type="email" id="email" name="email" class="form-control" 
                                                   value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="senha" class="form-label">Nova Senha</label>
                                    <input type="password" id="senha" name="senha" class="form-control">
                                    <small class="form-text">Deixe em branco se não quiser alterar a senha.</small>
                                </div>
                            </div>
                            
                            <!-- Informações da Empresa -->
                            <?php if ($empresa): ?>
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-building"></i>Dados da Empresa
                                </h3>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cnpj" class="form-label">CNPJ</label>
                                            <input type="text" id="cnpj" name="cnpj" class="form-control" 
                                                   value="<?php echo htmlspecialchars($empresa['cnpj'] ?? ''); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="razao_social" class="form-label">Razão Social</label>
                                            <input type="text" id="razao_social" name="razao_social" class="form-control" 
                                                   value="<?php echo htmlspecialchars($empresa['razao_social'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telefone" class="form-label">Telefone</label>
                                            <input type="text" id="telefone" name="telefone" class="form-control" 
                                                   value="<?php echo htmlspecialchars($empresa['telefone'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="site" class="form-label">Site</label>
                                            <input type="url" id="site" name="site" class="form-control" 
                                                   value="<?php echo htmlspecialchars($empresa['site'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="descricao" class="form-label">Descrição da Empresa</label>
                                    <textarea id="descricao" name="descricao" class="form-control" rows="4"><?php echo htmlspecialchars($empresa['descricao'] ?? ''); ?></textarea>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="form-actions">
                                <a href="<?php echo BASE_DIR; ?>/app/views/painel_empresa.php" class="btn-cancel">
                                    <i class="fas fa-arrow-left me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn-save">
                                    <i class="fas fa-save me-2"></i>Salvar Alterações
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
</body>
</html>
