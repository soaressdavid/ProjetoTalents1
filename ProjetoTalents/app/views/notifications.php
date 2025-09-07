<?php
// Arquivo: app/views/notifications.php

require_once __DIR__ . '/../utils/init.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: " . BASE_DIR . "/app/views/auth.php");
    exit();
}

$notificationModel = new Notification();

// Buscar notificações do usuário
$notifications = $notificationModel->findByUsuarioId($_SESSION['usuario_id']);

// Verificar se as consultas retornaram dados válidos
if ($notifications === false) {
    $notifications = [];
}

// Marcar notificações como lidas
if (!empty($notifications)) {
    $notificationModel->markAsRead($_SESSION['usuario_id']);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificações - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Suas notificações no TalentsHUB.">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_DIR; ?>/public/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_DIR; ?>/public/images/favicon.ico">
    
    <style>
        .notifications-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .notification-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: var(--transition);
        }
        
        .notification-card:hover {
            box-shadow: var(--shadow);
        }
        
        .notification-card.unread {
            border-left: 4px solid var(--primary-color);
            background: #f8fafc;
        }
        
        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }
        
        .notification-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }
        
        .notification-time {
            color: var(--secondary-color);
            font-size: 0.75rem;
        }
        
        .notification-content {
            color: var(--secondary-color);
            font-size: 0.875rem;
            line-height: 1.5;
        }
        
        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        
        .icon-success {
            background: #d1fae5;
            color: #065f46;
        }
        
        .icon-info {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .icon-warning {
            background: #fef3c7;
            color: #92400e;
        }
        
        .icon-error {
            background: #fee2e2;
            color: #991b1b;
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
        
        .page-header {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }
        
        .page-subtitle {
            color: var(--secondary-color);
            margin: 0.5rem 0 0;
        }
        
        @media (max-width: 768px) {
            .notifications-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
            
            .notification-card {
                padding: 1rem;
            }
            
            .notification-header {
                flex-direction: column;
                align-items: flex-start;
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
                <?php if ($_SESSION['usuario_tipo'] === 'candidato'): ?>
                    <a href="<?php echo BASE_DIR; ?>/app/views/painel_candidato.php">Meu Painel</a>
                    <a href="<?php echo BASE_DIR; ?>/app/views/minhas_candidaturas.php">Minhas Candidaturas</a>
                <?php elseif ($_SESSION['usuario_tipo'] === 'empresa'): ?>
                    <a href="<?php echo BASE_DIR; ?>/app/views/painel_empresa.php">Meu Painel</a>
                    <a href="<?php echo BASE_DIR; ?>/app/views/gerenciar_vagas.php">Gerenciar Vagas</a>
                <?php endif; ?>
                <a href="<?php echo BASE_DIR; ?>/app/controllers/LogoutController.php">Sair</a>
            </nav>
        </div>
    </header>

    <div class="notifications-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-bell me-2"></i>Notificações
            </h1>
            <p class="page-subtitle">Acompanhe as últimas atualizações da sua conta</p>
        </div>
        
        <?php if (empty($notifications)): ?>
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <h3>Nenhuma notificação</h3>
                <p>Você não possui notificações no momento. Quando houver atualizações importantes, elas aparecerão aqui.</p>
                <a href="<?php echo BASE_DIR; ?>/app/views/vagas.php" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Buscar Vagas
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-card <?php echo $notification['lida'] ? '' : 'unread'; ?>">
                    <div class="d-flex">
                        <div class="notification-icon icon-<?php echo $notification['tipo'] ?? 'info'; ?>">
                            <?php
                            $icon = 'info-circle';
                            switch ($notification['tipo'] ?? 'info') {
                                case 'success':
                                    $icon = 'check-circle';
                                    break;
                                case 'warning':
                                    $icon = 'exclamation-triangle';
                                    break;
                                case 'error':
                                    $icon = 'times-circle';
                                    break;
                                default:
                                    $icon = 'info-circle';
                            }
                            ?>
                            <i class="fas fa-<?php echo $icon; ?>"></i>
                        </div>
                        
                        <div class="flex-grow-1">
                            <div class="notification-header">
                                <h3 class="notification-title"><?php echo htmlspecialchars($notification['titulo']); ?></h3>
                                <span class="notification-time">
                                    <?php echo date('d/m/Y H:i', strtotime($notification['created_at'])); ?>
                                </span>
                            </div>
                            
                            <div class="notification-content">
                                <?php echo nl2br(htmlspecialchars($notification['mensagem'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_DIR; ?>/public/js/main.js"></script>
</body>
</html>
