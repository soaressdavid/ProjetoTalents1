<?php
// Arquivo: app/models/Notification.php
// TalentsHUB - Modelo de Notificações

class Notification {
    private $conn;

    public function __construct() {
        $this->conn = getDbConnection();
    }

    public function create($usuario_id, $tipo, $titulo, $mensagem, $dados_extra = null) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO notifications (usuario_id, tipo, titulo, mensagem, dados_extra, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $dados_json = $dados_extra ? json_encode($dados_extra) : null;
            return $stmt->execute([$usuario_id, $tipo, $titulo, $mensagem, $dados_json]);
        } catch (PDOException $e) {
            error_log("Erro ao criar notificação: " . $e->getMessage());
            return false;
        }
    }

    public function getByUsuario($usuario_id, $limit = 20, $offset = 0) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM notifications 
                WHERE usuario_id = ? 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$usuario_id, $limit, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar notificações: " . $e->getMessage());
            return false;
        }
    }

    public function getUnreadCount($usuario_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) FROM notifications 
                WHERE usuario_id = ? AND lida = 0
            ");
            $stmt->execute([$usuario_id]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erro ao contar notificações não lidas: " . $e->getMessage());
            return 0;
        }
    }

    public function markAsRead($id, $usuario_id) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE notifications 
                SET lida = 1, lida_em = NOW() 
                WHERE id = ? AND usuario_id = ?
            ");
            return $stmt->execute([$id, $usuario_id]);
        } catch (PDOException $e) {
            error_log("Erro ao marcar notificação como lida: " . $e->getMessage());
            return false;
        }
    }

    public function markAllAsRead($usuario_id) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE notifications 
                SET lida = 1, lida_em = NOW() 
                WHERE usuario_id = ? AND lida = 0
            ");
            return $stmt->execute([$usuario_id]);
        } catch (PDOException $e) {
            error_log("Erro ao marcar todas as notificações como lidas: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id, $usuario_id) {
        try {
            $stmt = $this->conn->prepare("
                DELETE FROM notifications 
                WHERE id = ? AND usuario_id = ?
            ");
            return $stmt->execute([$id, $usuario_id]);
        } catch (PDOException $e) {
            error_log("Erro ao deletar notificação: " . $e->getMessage());
            return false;
        }
    }

    public function deleteOld($days = 30) {
        try {
            $stmt = $this->conn->prepare("
                DELETE FROM notifications 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
            ");
            return $stmt->execute([$days]);
        } catch (PDOException $e) {
            error_log("Erro ao deletar notificações antigas: " . $e->getMessage());
            return false;
        }
    }

    // Métodos específicos para diferentes tipos de notificação
    public function notifyNewCandidatura($vaga_id, $candidato_id) {
        $vagaModel = new Vaga();
        $candidatoModel = new Candidato();
        $empresaModel = new Empresa();
        
        $vaga = $vagaModel->findById($vaga_id);
        $candidato = $candidatoModel->findById($candidato_id);
        
        if ($vaga && $candidato) {
            $empresa = $empresaModel->findById($vaga['empresa_id']);
            if ($empresa) {
                $titulo = "Nova candidatura recebida";
                $mensagem = "Você recebeu uma nova candidatura para a vaga '{$vaga['titulo']}' de {$candidato['nome']}";
                $dados_extra = [
                    'vaga_id' => $vaga_id,
                    'candidato_id' => $candidato_id,
                    'candidatura_id' => null // Será preenchido depois
                ];
                
                return $this->create($empresa['usuario_id'], 'candidatura', $titulo, $mensagem, $dados_extra);
            }
        }
        
        return false;
    }

    public function notifyStatusChange($candidatura_id, $novo_status) {
        $candidaturaModel = new Candidatura();
        $candidatura = $candidaturaModel->findById($candidatura_id);
        
        if ($candidatura) {
            $titulo = "Status da candidatura atualizado";
            $mensagem = "O status da sua candidatura para '{$candidatura['vaga_titulo']}' foi alterado para: " . ucfirst($novo_status);
            $dados_extra = [
                'candidatura_id' => $candidatura_id,
                'vaga_id' => $candidatura['vaga_id'],
                'status' => $novo_status
            ];
            
            return $this->create($candidatura['candidato_id'], 'status', $titulo, $mensagem, $dados_extra);
        }
        
        return false;
    }

    public function notifyNewVaga($vaga_id) {
        $vagaModel = new Vaga();
        $candidatoModel = new Candidato();
        
        $vaga = $vagaModel->findById($vaga_id);
        if ($vaga) {
            // Notificar candidatos que podem ter interesse na vaga
            $stmt = $this->conn->prepare("
                SELECT c.usuario_id 
                FROM candidatos c
                WHERE c.area_interesse = ? OR c.area_interesse IS NULL
                LIMIT 100
            ");
            $stmt->execute([$vaga['area']]);
            $candidatos = $stmt->fetchAll();
            
            $titulo = "Nova vaga disponível";
            $mensagem = "Uma nova vaga foi publicada: '{$vaga['titulo']}' em {$vaga['localizacao']}";
            $dados_extra = [
                'vaga_id' => $vaga_id,
                'tipo' => 'nova_vaga'
            ];
            
            $notificados = 0;
            foreach ($candidatos as $candidato) {
                if ($this->create($candidato['usuario_id'], 'vaga', $titulo, $mensagem, $dados_extra)) {
                    $notificados++;
                }
            }
            
            return $notificados;
        }
        
        return false;
    }

    public function notifySystemMessage($usuario_id, $titulo, $mensagem) {
        return $this->create($usuario_id, 'sistema', $titulo, $mensagem);
    }

    public function notifyBulk($usuario_ids, $tipo, $titulo, $mensagem, $dados_extra = null) {
        $notificados = 0;
        foreach ($usuario_ids as $usuario_id) {
            if ($this->create($usuario_id, $tipo, $titulo, $mensagem, $dados_extra)) {
                $notificados++;
            }
        }
        return $notificados;
    }
}
?>

