<?php
// Arquivo: app/controllers/CadastroController.php
// TalentsHUB - Plataforma de Recrutamento e Seleção

require_once __DIR__ . '/../utils/init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getDbConnection();

    // Sanitização e validação dos dados de entrada
    $nome = trim(isset($_POST['nome']) ? $_POST['nome'] : '');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
    $tipo_usuario = isset($_POST['tipo_usuario']) ? $_POST['tipo_usuario'] : '';
    
    // Campos específicos para empresas
    $cnpj = isset($_POST['cnpj']) ? preg_replace('/[^0-9]/', '', $_POST['cnpj']) : '';
    $telefone = isset($_POST['telefone']) ? preg_replace('/[^0-9]/', '', $_POST['telefone']) : '';
    $descricao_empresa = isset($_POST['descricao_empresa']) ? trim($_POST['descricao_empresa']) : '';
    
    // Campos específicos para candidatos
    $telefone_candidato = isset($_POST['telefone_candidato']) ? preg_replace('/[^0-9]/', '', $_POST['telefone_candidato']) : '';
    $data_nascimento = isset($_POST['data_nascimento']) ? $_POST['data_nascimento'] : '';

    // Validação básica de campos obrigatórios
    if (empty($nome) || empty($email) || empty($senha) || empty($tipo_usuario)) {
        $_SESSION['cadastro_erro'] = "Por favor, preencha todos os campos obrigatórios.";
        header("Location: " . BASE_DIR . "/app/views/auth.php");
        exit();
    }

    // Validação de formato de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['cadastro_erro'] = "Por favor, insira um endereço de email válido.";
        header("Location: " . BASE_DIR . "/app/views/auth.php");
        exit();
    }

    // Validação de força da senha (mínimo 8 caracteres, pelo menos 1 número e 1 letra)
    if (strlen($senha) < 8) {
        $_SESSION['cadastro_erro'] = "A senha deve ter pelo menos 8 caracteres.";
        header("Location: " . BASE_DIR . "/app/views/auth.php");
        exit();
    }
    
    // Verificar se a senha tem pelo menos uma letra
    if (!preg_match('/[A-Za-z]/', $senha)) {
        $_SESSION['cadastro_erro'] = "A senha deve conter pelo menos uma letra.";
        header("Location: " . BASE_DIR . "/app/views/auth.php");
        exit();
    }
    
    // Verificar se a senha tem pelo menos um número
    if (!preg_match('/[0-9]/', $senha)) {
        $_SESSION['cadastro_erro'] = "A senha deve conter pelo menos um número.";
        header("Location: " . BASE_DIR . "/app/views/auth.php");
        exit();
    }

    // Validação de tipo de usuário
    if (!in_array($tipo_usuario, ['candidato', 'empresa', 'admin'])) {
        $_SESSION['cadastro_erro'] = "Tipo de usuário inválido.";
        header("Location: " . BASE_DIR . "/app/views/auth.php");
        exit();
    }

    // Validações específicas para empresas
    if ($tipo_usuario === 'empresa') {
        if (empty($cnpj)) {
            $_SESSION['cadastro_erro'] = "CNPJ é obrigatório para empresas.";
            header("Location: " . BASE_DIR . "/app/views/auth.php");
            exit();
        }
        
        if (strlen($cnpj) !== 14) {
            $_SESSION['cadastro_erro'] = "CNPJ deve ter 14 dígitos.";
            header("Location: " . BASE_DIR . "/app/views/auth.php");
            exit();
        }
        
        if (empty($telefone)) {
            $_SESSION['cadastro_erro'] = "Telefone é obrigatório para empresas.";
            header("Location: " . BASE_DIR . "/app/views/auth.php");
            exit();
        }
    }

    // Validações específicas para candidatos
    if ($tipo_usuario === 'candidato') {
        if (empty($telefone_candidato)) {
            $_SESSION['cadastro_erro'] = "Telefone é obrigatório para candidatos.";
            header("Location: " . BASE_DIR . "/app/views/auth.php");
            exit();
        }
        
        if (!empty($data_nascimento)) {
            $idade = date_diff(date_create($data_nascimento), date_create('today'))->y;
            if ($idade < 16) {
                $_SESSION['cadastro_erro'] = "Você deve ter pelo menos 16 anos para se cadastrar.";
                header("Location: " . BASE_DIR . "/app/views/auth.php");
                exit();
            }
        }
    }

    // Verificar se o email já existe
    $userModel = new User();
    if ($userModel->findByEmail($email)) {
        $_SESSION['cadastro_erro'] = "Este email já está cadastrado em nossa plataforma. Use outro email ou faça login.";
        header("Location: " . BASE_DIR . "/app/views/auth.php");
        exit();
    }
    
    // Verificar se o CNPJ já existe (para empresas)
    if ($tipo_usuario === 'empresa' && !empty($cnpj)) {
        $stmt = $conn->prepare("SELECT id FROM empresas WHERE cnpj = ?");
        $stmt->execute([$cnpj]);
        if ($stmt->fetch()) {
            $_SESSION['cadastro_erro'] = "Este CNPJ já está cadastrado em nossa plataforma.";
            header("Location: " . BASE_DIR . "/app/views/auth.php");
            exit();
        }
    }

    $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

    try {
        $conn->beginTransaction();

        // Inserir usuário principal
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo_usuario, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$nome, $email, $senhaCriptografada, $tipo_usuario]);
        $usuario_id = $conn->lastInsertId();
        
        // Criar registro específico baseado no tipo de usuário
        if ($tipo_usuario === 'candidato') {
            $stmt = $conn->prepare("INSERT INTO candidatos (usuario_id, telefone, data_nascimento, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$usuario_id, $telefone_candidato, $data_nascimento]);
        } elseif ($tipo_usuario === 'empresa') {
            $stmt = $conn->prepare("INSERT INTO empresas (usuario_id, cnpj, telefone, descricao, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$usuario_id, $cnpj, $telefone, $descricao_empresa]);
        }
        // Para admin, não criamos registro adicional nas tabelas candidatos/empresas
        
        $conn->commit();
        
        // Mensagens de sucesso personalizadas
        $mensagem_sucesso = "";
        switch ($tipo_usuario) {
            case 'candidato':
                $mensagem_sucesso = "Bem-vindo ao TalentsHUB! Sua conta de candidato foi criada com sucesso. Agora você pode buscar vagas e se candidatar.";
                break;
            case 'empresa':
                $mensagem_sucesso = "Bem-vindo ao TalentsHUB! Sua conta empresarial foi criada com sucesso. Agora você pode publicar vagas e encontrar talentos.";
                break;
            case 'admin':
                $mensagem_sucesso = "Conta administrativa criada com sucesso no TalentsHUB.";
                break;
        }
        
        $_SESSION['cadastro_sucesso'] = $mensagem_sucesso;
        header("Location: " . BASE_DIR . "/app/views/auth.php");
        exit();
    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION['cadastro_erro'] = "Ops! Ocorreu um erro ao criar sua conta. Nossa equipe foi notificada. Tente novamente em alguns minutos.";
        error_log("Erro no cadastro TalentsHUB: " . $e->getMessage());
        header("Location: " . BASE_DIR . "/app/views/auth.php");
        exit();
    }
} else {
    header("Location: " . BASE_DIR . "/app/views/auth.php");
    exit();
}
?>
