<?php
// Arquivo: app/controllers/NotificationController.php
// TalentsHUB - Controller de Notificações

require_once __DIR__ . '/../utils/init.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_DIR . '/app/views/auth.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'mark_read':
            markAsRead();
            break;
        case 'mark_all_read':
            markAllAsRead();
            break;
        case 'delete':
            deleteNotification();
            break;
        default:
            header('Location: ' . BASE_DIR . '/app/views/notifications.php');
            exit();
    }
} else {
    // GET requests
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'list':
            listNotifications();
            break;
        case 'count':
            getUnreadCount();
            break;
        case 'view':
            viewNotification();
            break;
        default:
            listNotifications();
    }
}

function listNotifications() {
    $notificationModel = new Notification();
    
    // Paginação
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = 20;
    $offset = ($page - 1) * $limit;
    
    // Buscar notificações
    $notifications = $notificationModel->getByUsuario($_SESSION['usuario_id'], $limit, $offset);
    $unread_count = $notificationModel->getUnreadCount($_SESSION['usuario_id']);
    
    // Preparar dados para a view
    $data = [
        'notifications' => $notifications,
        'unread_count' => $unread_count,
        'pagination' => [
            'current_page' => $page,
            'limit' => $limit
        ]
    ];
    
    // Incluir a view
    include __DIR__ . '/../views/notifications.php';
}

function getUnreadCount() {
    $notificationModel = new Notification();
    $count = $notificationModel->getUnreadCount($_SESSION['usuario_id']);
    
    header('Content-Type: application/json');
    echo json_encode(['count' => $count]);
    exit();
}

function markAsRead() {
    $notification_id = intval($_POST['notification_id'] ?? 0);
    
    if (!$notification_id) {
        header('Location: ' . BASE_DIR . '/app/views/notifications.php');
        exit();
    }
    
    $notificationModel = new Notification();
    
    if ($notificationModel->markAsRead($notification_id, $_SESSION['usuario_id'])) {
        $_SESSION['notification_sucesso'] = "Notificação marcada como lida.";
    } else {
        $_SESSION['notification_erro'] = "Erro ao marcar notificação como lida.";
    }
    
    header('Location: ' . BASE_DIR . '/app/views/notifications.php');
    exit();
}

function markAllAsRead() {
    $notificationModel = new Notification();
    
    if ($notificationModel->markAllAsRead($_SESSION['usuario_id'])) {
        $_SESSION['notification_sucesso'] = "Todas as notificações foram marcadas como lidas.";
    } else {
        $_SESSION['notification_erro'] = "Erro ao marcar notificações como lidas.";
    }
    
    header('Location: ' . BASE_DIR . '/app/views/notifications.php');
    exit();
}

function deleteNotification() {
    $notification_id = intval($_POST['notification_id'] ?? 0);
    
    if (!$notification_id) {
        header('Location: ' . BASE_DIR . '/app/views/notifications.php');
        exit();
    }
    
    $notificationModel = new Notification();
    
    if ($notificationModel->delete($notification_id, $_SESSION['usuario_id'])) {
        $_SESSION['notification_sucesso'] = "Notificação removida.";
    } else {
        $_SESSION['notification_erro'] = "Erro ao remover notificação.";
    }
    
    header('Location: ' . BASE_DIR . '/app/views/notifications.php');
    exit();
}

function viewNotification() {
    $notification_id = intval($_GET['id'] ?? 0);
    
    if (!$notification_id) {
        header('Location: ' . BASE_DIR . '/app/views/notifications.php');
        exit();
    }
    
    $notificationModel = new Notification();
    
    // Marcar como lida
    $notificationModel->markAsRead($notification_id, $_SESSION['usuario_id']);
    
    // Buscar notificação
    $stmt = $notificationModel->conn->prepare("
        SELECT * FROM notifications 
        WHERE id = ? AND usuario_id = ?
    ");
    $stmt->execute([$notification_id, $_SESSION['usuario_id']]);
    $notification = $stmt->fetch();
    
    if (!$notification) {
        $_SESSION['notification_erro'] = "Notificação não encontrada.";
        header('Location: ' . BASE_DIR . '/app/views/notifications.php');
        exit();
    }
    
    // Processar dados extras se existirem
    $dados_extra = null;
    if ($notification['dados_extra']) {
        $dados_extra = json_decode($notification['dados_extra'], true);
    }
    
    // Redirecionar baseado no tipo de notificação
    switch ($notification['tipo']) {
        case 'candidatura':
            if ($dados_extra && isset($dados_extra['candidatura_id'])) {
                header('Location: ' . BASE_DIR . '/app/views/ver_candidatura.php?id=' . $dados_extra['candidatura_id']);
            } else {
                header('Location: ' . BASE_DIR . '/app/views/gerenciar_candidaturas.php');
            }
            break;
            
        case 'vaga':
            if ($dados_extra && isset($dados_extra['vaga_id'])) {
                header('Location: ' . BASE_DIR . '/app/views/vaga_detalhes.php?id=' . $dados_extra['vaga_id']);
            } else {
                header('Location: ' . BASE_DIR . '/app/views/vagas.php');
            }
            break;
            
        case 'status':
            if ($dados_extra && isset($dados_extra['candidatura_id'])) {
                header('Location: ' . BASE_DIR . '/app/views/ver_candidatura.php?id=' . $dados_extra['candidatura_id']);
            } else {
                header('Location: ' . BASE_DIR . '/app/views/minhas_candidaturas.php');
            }
            break;
            
        default:
            header('Location: ' . BASE_DIR . '/app/views/notifications.php');
    }
    
    exit();
}
?>

