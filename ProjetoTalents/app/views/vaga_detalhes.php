<?php
require_once __DIR__ . '/../utils/init.php';

// Verifica se o ID da vaga foi passado na URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redireciona para a página de vagas se o ID for inválido
    header('Location: vagas.php');
    exit();
}

$vagaModel = new Vaga();
$vaga = $vagaModel->findById($_GET['id']);

// Verifica se a vaga foi encontrada
if (!$vaga) {
    // Exibe uma mensagem de erro se a vaga não existir
    echo "<!DOCTYPE html><html lang='pt-br'><head><meta charset='UTF-8'><title>Vaga não encontrada</title><link rel='stylesheet' href='../../public/css/style.css'></head><body><main class='container'><h2 class='main-title' style='text-align:center;'>Vaga não encontrada</h2><p style='text-align:center;'>A vaga que você está procurando não existe ou foi removida.</p><p style='text-align:center;'><a href='vagas.php' class='button button-secondary'>Voltar para as vagas</a></p></main></body></html>";
    exit();
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($vaga['titulo']); ?> - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Detalhes da vaga: <?php echo htmlspecialchars($vaga['titulo']); ?>">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_DIR; ?>/public/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_DIR; ?>/public/images/favicon.ico">
    
    <style>
        .vaga-detail-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .vaga-detail-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .vaga-header {
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .vaga-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 1rem;
        }
        
        .vaga-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            color: var(--secondary-color);
            font-size: 0.875rem;
        }
        
        .meta-item i {
            margin-right: 0.5rem;
            color: var(--primary-color);
            width: 16px;
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-left: auto;
        }
        
        .status-ativa { background: #d1fae5; color: #065f46; }
        .status-pausada { background: #fef3c7; color: #92400e; }
        .status-finalizada { background: #fee2e2; color: #991b1b; }
        
        .vaga-section {
            margin-bottom: 2rem;
        }
        
        .vaga-section h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .vaga-section p {
            color: var(--secondary-color);
            line-height: 1.6;
            white-space: pre-line;
        }
        
        .vaga-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
        }
        
        .btn-vaga {
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }
        
        .btn-primary-vaga {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary-vaga:hover {
            background: var(--primary-dark);
            color: white;
        }
        
        .btn-secondary-vaga {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }
        
        .btn-secondary-vaga:hover {
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
        
        @media (max-width: 768px) {
            .vaga-detail-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
            
            .vaga-detail-card {
                padding: 1.5rem;
            }
            
            .vaga-title {
                font-size: 1.5rem;
            }
            
            .vaga-meta {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .status-badge {
                margin-left: 0;
                align-self: flex-start;
            }
            
            .vaga-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="vaga-detail-container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_DIR; ?>/app/views/gerenciar_vagas.php">Gerenciar Vagas</a></li>
                    <li class="breadcrumb-item active">Detalhes da Vaga</li>
                </ol>
            </nav>
            
            <!-- Card da Vaga -->
            <div class="vaga-detail-card">
                <div class="vaga-header">
                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                        <div>
                            <h1 class="vaga-title"><?php echo htmlspecialchars($vaga['titulo']); ?></h1>
                            <div class="vaga-meta">
                                <div class="meta-item">
                                    <i class="fas fa-building"></i>
                                    <span><?php echo htmlspecialchars($vaga['razao_social'] ?? $vaga['empresa_nome'] ?? 'Empresa'); ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($vaga['localizacao']); ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-briefcase"></i>
                                    <span><?php echo ucfirst($vaga['nivel_experiencia']); ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>Publicada em <?php echo date('d/m/Y', strtotime($vaga['created_at'])); ?></span>
                                </div>
                            </div>
                        </div>
                        <span class="status-badge status-<?php echo $vaga['status']; ?>">
                            <?php echo ucfirst($vaga['status']); ?>
                        </span>
                    </div>
                </div>

                <div class="vaga-section">
                    <h3><i class="fas fa-file-alt me-2"></i>Descrição da Vaga</h3>
                    <p><?php echo htmlspecialchars($vaga['descricao']); ?></p>
                </div>

                <div class="vaga-section">
                    <h3><i class="fas fa-list-check me-2"></i>Requisitos</h3>
                    <p><?php echo htmlspecialchars($vaga['requisitos']); ?></p>
                </div>

                <?php if (!empty($vaga['beneficios'])): ?>
                <div class="vaga-section">
                    <h3><i class="fas fa-gift me-2"></i>Benefícios</h3>
                    <p><?php echo htmlspecialchars($vaga['beneficios']); ?></p>
                </div>
                <?php endif; ?>

                <div class="vaga-actions">
                    <a href="<?php echo BASE_DIR; ?>/app/views/editar_vaga.php?id=<?php echo $vaga['id']; ?>" 
                       class="btn-vaga btn-primary-vaga">
                        <i class="fas fa-edit me-2"></i>Editar Vaga
                    </a>
                    <a href="<?php echo BASE_DIR; ?>/app/views/ver_candidatura.php?vaga_id=<?php echo $vaga['id']; ?>" 
                       class="btn-vaga btn-secondary-vaga">
                        <i class="fas fa-users me-2"></i>Ver Candidatos
                    </a>
                    <a href="<?php echo BASE_DIR; ?>/app/views/calculadora_deslocamento.php?empresa=<?php echo urlencode($vaga['razao_social']); ?>&endereco=<?php echo urlencode($vaga['endereco'] ?? ''); ?>" 
                       class="btn-vaga btn-secondary-vaga">
                        <i class="fas fa-route me-2"></i>Calcular Deslocamento
                    </a>
                    <a href="<?php echo BASE_DIR; ?>/app/views/gerenciar_vagas.php" 
                       class="btn-vaga btn-secondary-vaga">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_DIR; ?>/public/js/main.js"></script>
</body>
</html>