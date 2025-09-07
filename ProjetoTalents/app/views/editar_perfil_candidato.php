<?php
// Arquivo: app/views/editar_perfil_candidato.php

require_once __DIR__ . '/../utils/init.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'candidato') {
    header("Location: " . BASE_DIR . "/app/views/auth.php");
    exit();
}

$usuarioModel = new User();
$usuario = $usuarioModel->findById($_SESSION['usuario_id']);

if (!$usuario) {
    echo "<h1>Erro: Usuário não encontrado.</h1>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Edite suas informações pessoais no TalentsHUB.">
    
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
        
        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 2rem;
        }
        
        .profile-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }
        
        .profile-subtitle {
            color: var(--secondary-color);
            margin: 0.5rem 0 0;
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
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
        
        .btn-save {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .btn-save:hover {
            background: var(--primary-dark);
        }
        
        .btn-cancel {
            background: transparent;
            color: var(--secondary-color);
            border: 2px solid var(--border-color);
            padding: 0.75rem 2rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
            margin-left: 1rem;
        }
        
        .btn-cancel:hover {
            background: var(--border-color);
            color: var(--dark-color);
        }
        
        .alert {
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
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
            .profile-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
            
            .profile-card {
                padding: 1.5rem;
            }
            
            .btn-cancel {
                margin-left: 0;
                margin-top: 1rem;
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

    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h2 class="profile-title">Editar Perfil</h2>
                <p class="profile-subtitle">Atualize suas informações pessoais</p>
            </div>

            <?php
            if (isset($_SESSION['perfil_sucesso'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['perfil_sucesso']) . '</div>';
                unset($_SESSION['perfil_sucesso']);
            }
            if (isset($_SESSION['perfil_erro'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['perfil_erro']) . '</div>';
                unset($_SESSION['perfil_erro']);
            }
            ?>

            <form action="<?php echo BASE_DIR; ?>/app/controllers/PerfilController.php" method="POST">
                <input type="hidden" name="action" value="update_perfil">

                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-user me-2"></i>Informações Básicas
                    </h3>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo *</label>
                                <input type="text" id="nome" name="nome" class="form-control" 
                                       value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" id="email" name="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="senha" class="form-label">Nova Senha</label>
                                <input type="password" id="senha" name="senha" class="form-control" 
                                       placeholder="Deixe em branco para manter a senha atual">
                                <div class="form-text">Deixe em branco se não quiser alterar a senha</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save me-2"></i>Salvar Alterações
                    </button>
                    <a href="<?php echo BASE_DIR; ?>/app/views/painel_candidato.php" class="btn-cancel">
                        <i class="fas fa-arrow-left me-2"></i>Voltar ao Painel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_DIR; ?>/public/js/main.js"></script>
</body>
</html>