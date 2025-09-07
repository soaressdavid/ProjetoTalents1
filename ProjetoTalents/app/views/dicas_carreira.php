<?php
require_once __DIR__ . '/../utils/init.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dicas de Carreira - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Dicas e orientações para o desenvolvimento da sua carreira profissional.">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_DIR; ?>/public/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_DIR; ?>/public/images/favicon.ico">
    
    <style>
        .career-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .tip-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            transition: var(--transition);
        }
        
        .tip-card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }
        
        .tip-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .icon-interview {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .icon-resume {
            background: #d1fae5;
            color: #065f46;
        }
        
        .icon-networking {
            background: #fef3c7;
            color: #92400e;
        }
        
        .icon-skills {
            background: #fee2e2;
            color: #991b1b;
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
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 4rem 0;
            margin-bottom: 3rem;
            border-radius: var(--border-radius);
        }
        
        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        
        @media (max-width: 768px) {
            .career-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
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

    <div class="career-container">
        <!-- Hero Section -->
        <div class="hero-section text-center">
            <div class="container">
                <h1 class="hero-title">Dicas de Carreira</h1>
                <p class="hero-subtitle">Orientações profissionais para impulsionar sua carreira</p>
            </div>
        </div>
        
        <!-- Dicas -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="tip-card">
                    <div class="tip-icon icon-interview">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3>Dicas de Entrevista</h3>
                    <p class="text-muted mb-3">Como se preparar e se destacar em entrevistas de emprego.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Pesquise sobre a empresa</li>
                        <li><i class="fas fa-check text-success me-2"></i>Prepare perguntas inteligentes</li>
                        <li><i class="fas fa-check text-success me-2"></i>Pratique respostas comuns</li>
                        <li><i class="fas fa-check text-success me-2"></i>Seja pontual e profissional</li>
                    </ul>
                    <a href="<?php echo BASE_DIR; ?>/app/views/entrevistas.php" class="btn btn-primary">
                        Ver Mais Dicas
                    </a>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="tip-card">
                    <div class="tip-icon icon-resume">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3>Como Fazer um Currículo</h3>
                    <p class="text-muted mb-3">Dicas para criar um currículo que chama atenção dos recrutadores.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Seja claro e objetivo</li>
                        <li><i class="fas fa-check text-success me-2"></i>Destaque suas conquistas</li>
                        <li><i class="fas fa-check text-success me-2"></i>Use palavras-chave relevantes</li>
                        <li><i class="fas fa-check text-success me-2"></i>Mantenha o design limpo</li>
                    </ul>
                    <a href="<?php echo BASE_DIR; ?>/app/views/curriculo.php" class="btn btn-primary">
                        Ver Mais Dicas
                    </a>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="tip-card">
                    <div class="tip-icon icon-networking">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Networking Profissional</h3>
                    <p class="text-muted mb-3">Como construir e manter uma rede de contatos profissionais.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Participe de eventos da área</li>
                        <li><i class="fas fa-check text-success me-2"></i>Use o LinkedIn estrategicamente</li>
                        <li><i class="fas fa-check text-success me-2"></i>Mantenha contato regular</li>
                        <li><i class="fas fa-check text-success me-2"></i>Ofereça ajuda antes de pedir</li>
                    </ul>
                    <a href="#" class="btn btn-primary">
                        Ver Mais Dicas
                    </a>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="tip-card">
                    <div class="tip-icon icon-skills">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h3>Desenvolvimento de Habilidades</h3>
                    <p class="text-muted mb-3">Como identificar e desenvolver as habilidades mais valorizadas.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Identifique gaps de conhecimento</li>
                        <li><i class="fas fa-check text-success me-2"></i>Invista em cursos e certificações</li>
                        <li><i class="fas fa-check text-success me-2"></i>Pratique habilidades técnicas</li>
                        <li><i class="fas fa-check text-success me-2"></i>Desenvolva soft skills</li>
                    </ul>
                    <a href="<?php echo BASE_DIR; ?>/app/views/cursos.php" class="btn btn-primary">
                        Ver Cursos
                    </a>
                </div>
            </div>
        </div>
        
        <!-- CTA Section -->
        <div class="text-center mt-5">
            <h2 class="mb-3">Pronto para aplicar essas dicas?</h2>
            <p class="text-muted mb-4">Encontre oportunidades que combinam com seu perfil profissional.</p>
            <a href="<?php echo BASE_DIR; ?>/app/views/vagas.php" class="btn btn-primary btn-lg">
                <i class="fas fa-search me-2"></i>Buscar Vagas
            </a>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_DIR; ?>/public/js/main.js"></script>
</body>
</html>
