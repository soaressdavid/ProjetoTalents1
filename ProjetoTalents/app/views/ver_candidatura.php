<?php
// Arquivo: app/views/ver_candidatura.php

require_once __DIR__ . '/../utils/init.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'empresa') {
    header("Location: " . BASE_DIR . "/app/views/auth.php");
    exit();
}

if (!isset($_GET['vaga_id']) || empty($_GET['vaga_id']) || !is_numeric($_GET['vaga_id'])) {
    header("Location: " . BASE_DIR . "/app/views/gerenciar_vagas.php");
    exit();
}

$vaga_id = $_GET['vaga_id'];

$vagaModel = new Vaga();
$empresaModel = new Empresa();

// Buscar empresa primeiro
$empresa = $empresaModel->findByUsuarioId($_SESSION['usuario_id']);

if (!$empresa) {
    $_SESSION['vaga_erro'] = "Empresa não encontrada. Complete seu cadastro de empresa.";
    header("Location: " . BASE_DIR . "/app/views/editar_perfil_empresa.php");
    exit();
}

$vaga = $vagaModel->findById($vaga_id);

if (!$vaga || $vaga['empresa_id'] !== $empresa['id']) {
    $_SESSION['vaga_erro'] = "Acesso negado: Vaga não encontrada ou não pertence a você.";
    header("Location: " . BASE_DIR . "/app/views/gerenciar_vagas.php");
    exit();
}

$candidaturaModel = new Candidatura();
$candidaturas = $candidaturaModel->findByVagaId($vaga_id);

// Verificar se a consulta retornou dados válidos
if ($candidaturas === false) {
    $candidaturas = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatos para a Vaga - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Visualizar candidatos para a vaga: <?php echo htmlspecialchars($vaga['titulo']); ?>">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_DIR; ?>/public/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_DIR; ?>/public/images/favicon.ico">
    
    <style>
        .candidates-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .candidates-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .candidate-item {
            background: #f8f9fa;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: var(--transition);
        }
        
        .candidate-item:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }
        
        .candidate-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .candidate-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }
        
        .candidate-status {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-enviada { background: #dbeafe; color: #1e40af; }
        .status-visualizada { background: #fef3c7; color: #92400e; }
        .status-em_analise { background: #e0e7ff; color: #3730a3; }
        .status-aprovada { background: #d1fae5; color: #065f46; }
        .status-rejeitada { background: #fee2e2; color: #991b1b; }
        .status-entrevista_agendada { background: #f3e8ff; color: #7c3aed; }
        
        .candidate-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
        
        .candidate-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .btn-candidate {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }
        
        .btn-view-profile {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-view-profile:hover {
            background: var(--primary-dark);
            color: white;
        }
        
        .btn-download-cv {
            background: #10b981;
            color: white;
        }
        
        .btn-download-cv:hover {
            background: #059669;
            color: white;
        }
        
        .btn-approve {
            background: #10b981;
            color: white;
        }
        
        .btn-approve:hover {
            background: #059669;
            color: white;
        }
        
        .btn-reject {
            background: #ef4444;
            color: white;
        }
        
        .btn-reject:hover {
            background: #dc2626;
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
        
        .vaga-info {
            background: var(--light-color);
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 2rem;
        }
        
        .vaga-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .vaga-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            color: var(--secondary-color);
            font-size: 0.875rem;
        }
        
        @media (max-width: 768px) {
            .candidates-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
            
            .candidates-card {
                padding: 1.5rem;
            }
            
            .candidate-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .candidate-status {
                margin-top: 0.5rem;
            }
            
            .candidate-info {
                grid-template-columns: 1fr;
            }
            
            .candidate-actions {
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="candidates-container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_DIR; ?>/app/views/gerenciar_vagas.php">Gerenciar Vagas</a></li>
                    <li class="breadcrumb-item active">Candidatos</li>
                </ol>
            </nav>
            
            <!-- Informações da Vaga -->
            <div class="vaga-info">
                <h2 class="vaga-title"><?php echo htmlspecialchars($vaga['titulo']); ?></h2>
                <div class="vaga-meta">
                    <span><i class="fas fa-building me-1"></i><?php echo htmlspecialchars($vaga['razao_social'] ?? $vaga['empresa_nome'] ?? 'Empresa'); ?></span>
                    <span><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($vaga['localizacao']); ?></span>
                    <span><i class="fas fa-briefcase me-1"></i><?php echo ucfirst($vaga['nivel_experiencia']); ?></span>
                    <span><i class="fas fa-calendar me-1"></i>Publicada em <?php echo date('d/m/Y', strtotime($vaga['created_at'])); ?></span>
                </div>
            </div>
            
            <!-- Card de Candidatos -->
            <div class="candidates-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="mb-0">
                        <i class="fas fa-users me-2"></i>Candidatos
                        <span class="badge bg-primary ms-2"><?php echo count($candidaturas); ?></span>
                    </h1>
                    <a href="<?php echo BASE_DIR; ?>/app/views/gerenciar_vagas.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </a>
                </div>
                
                <?php if (empty($candidaturas)): ?>
                    <div class="empty-state">
                        <i class="fas fa-user-slash"></i>
                        <h3>Nenhum candidato ainda</h3>
                        <p>Nenhum candidato se inscreveu para esta vaga. Compartilhe a vaga para atrair mais candidatos!</p>
                        <a href="<?php echo BASE_DIR; ?>/app/views/editar_vaga.php?id=<?php echo $vaga['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Editar Vaga
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($candidaturas as $candidatura): ?>
                        <div class="candidate-item">
                            <div class="candidate-header">
                                <h3 class="candidate-name"><?php echo htmlspecialchars($candidatura['candidato_nome'] ?? 'Nome não disponível'); ?></h3>
                                <span class="candidate-status status-<?php echo $candidatura['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $candidatura['status'])); ?>
                                </span>
                            </div>
                            
                            <div class="candidate-info">
                                <div class="info-item">
                                    <i class="fas fa-envelope"></i>
                                    <span><?php echo htmlspecialchars($candidatura['candidato_email'] ?? 'Email não disponível'); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>Candidatou-se em <?php echo date('d/m/Y H:i', strtotime($candidatura['data_candidatura'])); ?></span>
                                </div>
                                <?php if (!empty($candidatura['data_visualizacao'])): ?>
                                <div class="info-item">
                                    <i class="fas fa-eye"></i>
                                    <span>Visualizada em <?php echo date('d/m/Y H:i', strtotime($candidatura['data_visualizacao'])); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($candidatura['carta_apresentacao'])): ?>
                            <div class="mb-3">
                                <strong>Carta de Apresentação:</strong>
                                <p class="mt-2 text-muted"><?php echo nl2br(htmlspecialchars($candidatura['carta_apresentacao'])); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <div class="candidate-actions">
                                <a href="<?php echo BASE_DIR; ?>/app/views/perfil_candidato.php?candidato_id=<?php echo htmlspecialchars($candidatura['candidato_id']); ?>" 
                                   class="btn-candidate btn-view-profile">
                                    <i class="fas fa-user me-1"></i>Ver Perfil
                                </a>
                                
                                <?php if (!empty($candidatura['curriculo_path'])): ?>
                                <a href="<?php echo BASE_DIR; ?>/uploads/curriculos/<?php echo htmlspecialchars($candidatura['curriculo_path']); ?>" 
                                   class="btn-candidate btn-download-cv" target="_blank">
                                    <i class="fas fa-download me-1"></i>Baixar CV
                                </a>
                                <?php endif; ?>
                                
                                <?php if ($candidatura['status'] === 'enviada' || $candidatura['status'] === 'visualizada'): ?>
                                <form action="<?php echo BASE_DIR; ?>/app/controllers/CandidaturaController.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="candidatura_id" value="<?php echo $candidatura['id']; ?>">
                                    <input type="hidden" name="status" value="aprovada">
                                    <button type="submit" class="btn-candidate btn-approve">
                                        <i class="fas fa-check me-1"></i>Aprovar
                                    </button>
                                </form>
                                
                                <form action="<?php echo BASE_DIR; ?>/app/controllers/CandidaturaController.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="candidatura_id" value="<?php echo $candidatura['id']; ?>">
                                    <input type="hidden" name="status" value="rejeitada">
                                    <button type="submit" class="btn-candidate btn-reject" 
                                            onclick="return confirm('Tem certeza que deseja rejeitar este candidato?');">
                                        <i class="fas fa-times me-1"></i>Rejeitar
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_DIR; ?>/public/js/main.js"></script>
</body>
</html>