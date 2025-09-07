<?php
require_once __DIR__ . '/../utils/init.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Cadastro - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Faça login ou cadastre-se no TalentsHUB para acessar as melhores oportunidades de trabalho.">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_DIR; ?>/public/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_DIR; ?>/public/images/favicon.ico">
    
    <style>
        .auth-page {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        
        .auth-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            margin: 0 1rem;
        }
        
        .auth-tabs {
            border-bottom: 1px solid var(--border-color);
            background: var(--light-color);
        }
        
        .auth-tab {
            padding: 1rem 2rem;
            border: none;
            background: transparent;
            color: var(--secondary-color);
            font-weight: 500;
            transition: var(--transition);
            cursor: pointer;
            flex: 1;
        }
        
        .auth-tab.active {
            background: white;
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
        }
        
        .auth-tab:hover {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-color);
        }
        
        .auth-content {
            padding: 3rem;
        }
        
        .auth-form {
            max-width: 400px;
            margin: 0 auto;
        }
        
        .form-floating {
            margin-bottom: 1.5rem;
        }
        
        .form-floating input, .form-floating select, .form-floating textarea {
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            transition: var(--transition);
        }
        
        .form-floating input:focus, .form-floating select:focus, .form-floating textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }
        
        .btn-auth {
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: var(--border-radius);
            transition: var(--transition);
            width: 100%;
        }
        
        .btn-auth:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
        }
        
        .conditional-fields {
            margin-top: 1rem;
            padding: 1rem;
            background: var(--light-color);
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
        }
        
        .conditional-fields.show {
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .auth-divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
        }
        
        .auth-divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--border-color);
        }
        
        .auth-divider span {
            background: white;
            padding: 0 1rem;
            color: var(--secondary-color);
            font-size: 0.875rem;
        }
        
        @media (max-width: 768px) {
            .auth-content {
                padding: 2rem 1.5rem;
            }
            
            .auth-tab {
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="<?php echo BASE_DIR; ?>">
                <i class="fas fa-users me-2"></i><?php echo SITE_NAME; ?>
            </a>
            
            <div class="d-flex">
                <a href="<?php echo BASE_DIR; ?>" class="btn btn-outline-primary">
                    <i class="fas fa-home me-1"></i>Voltar ao Início
                </a>
            </div>
        </div>
    </header>

    <!-- Página de Autenticação -->
    <div class="auth-page">
        <div class="auth-container">
            <!-- Alertas -->
            <?php if (isset($_SESSION['cadastro_sucesso'])): ?>
                <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['cadastro_sucesso']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['cadastro_sucesso']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['cadastro_erro'])): ?>
                <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_SESSION['cadastro_erro']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['cadastro_erro']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['login_sucesso'])): ?>
                <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['login_sucesso']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['login_sucesso']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['login_erro'])): ?>
                <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_SESSION['login_erro']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['login_erro']); ?>
            <?php endif; ?>

            <!-- Tabs de Navegação -->
            <div class="auth-tabs d-flex">
                <button class="auth-tab active" id="loginTab">
                    <i class="fas fa-sign-in-alt me-2"></i>Entrar
                </button>
                <button class="auth-tab" id="registerTab">
                    <i class="fas fa-user-plus me-2"></i>Cadastrar
                </button>
            </div>

            <!-- Conteúdo das Tabs -->
            <div class="auth-content">
                <!-- Formulário de Login -->
                <div id="loginForm" class="auth-form">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary">Bem-vindo de volta!</h2>
                        <p class="text-muted">Entre com suas credenciais para acessar sua conta</p>
                    </div>

                    <form action="<?php echo BASE_DIR; ?>/app/controllers/LoginController.php" method="POST">
                        <div class="form-floating">
                            <input type="email" class="form-control" id="loginEmail" name="email" placeholder="Email" required>
                            <label for="loginEmail"><i class="fas fa-envelope me-2"></i>Email</label>
                        </div>

                        <div class="form-floating">
                            <input type="password" class="form-control" id="loginPassword" name="senha" placeholder="Senha" required>
                            <label for="loginPassword"><i class="fas fa-lock me-2"></i>Senha</label>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">
                                    Lembrar de mim
                                </label>
                            </div>
                            <a href="#" class="text-primary text-decoration-none">Esqueceu a senha?</a>
                        </div>

                        <button type="submit" class="btn btn-primary btn-auth">
                            <i class="fas fa-sign-in-alt me-2"></i>Entrar
                        </button>
                    </form>
                </div>

                <!-- Formulário de Cadastro -->
                <div id="registerForm" class="auth-form" style="display: none;">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary">Crie sua conta</h2>
                        <p class="text-muted">Junte-se ao TalentsHUB e encontre as melhores oportunidades</p>
                    </div>

                    <form action="<?php echo BASE_DIR; ?>/app/controllers/CadastroController.php" method="POST" id="cadastroForm">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="registerName" name="nome" placeholder="Nome completo" required>
                            <label for="registerName"><i class="fas fa-user me-2"></i>Nome completo</label>
                        </div>

                        <div class="form-floating">
                            <input type="email" class="form-control" id="registerEmail" name="email" placeholder="Email" required>
                            <label for="registerEmail"><i class="fas fa-envelope me-2"></i>Email</label>
                        </div>

                        <div class="form-floating">
                            <input type="password" class="form-control" id="registerPassword" name="senha" placeholder="Senha" required>
                            <label for="registerPassword"><i class="fas fa-lock me-2"></i>Senha (mín. 8 caracteres)</label>
                        </div>

                        <div class="form-floating">
                            <select class="form-select" id="tipoUsuario" name="tipo_usuario" required>
                                <option value="">Selecione o tipo de usuário</option>
                                <option value="candidato">Candidato</option>
                                <option value="empresa">Empresa</option>
                                <option value="admin">Administrador</option>
                            </select>
                            <label for="tipoUsuario"><i class="fas fa-users me-2"></i>Tipo de usuário</label>
                        </div>

                        <!-- Campos específicos para candidatos -->
                        <div id="camposCandidato" class="conditional-fields" style="display: none;">
                            <h6 class="text-primary mb-3"><i class="fas fa-user-graduate me-2"></i>Informações do Candidato</h6>
                            
                            <div class="form-floating">
                                <input type="tel" class="form-control" id="candidatoTelefone" name="telefone_candidato" placeholder="Telefone">
                                <label for="candidatoTelefone"><i class="fas fa-phone me-2"></i>Telefone (apenas números)</label>
                            </div>

                            <div class="form-floating">
                                <input type="date" class="form-control" id="candidatoNascimento" name="data_nascimento" placeholder="Data de nascimento">
                                <label for="candidatoNascimento"><i class="fas fa-calendar me-2"></i>Data de nascimento</label>
                            </div>
                        </div>

                        <!-- Campos específicos para empresas -->
                        <div id="camposEmpresa" class="conditional-fields" style="display: none;">
                            <h6 class="text-primary mb-3"><i class="fas fa-building me-2"></i>Informações da Empresa</h6>
                            
                            <div class="form-floating">
                                <input type="text" class="form-control" id="empresaCnpj" name="cnpj" placeholder="CNPJ">
                                <label for="empresaCnpj"><i class="fas fa-id-card me-2"></i>CNPJ (apenas números)</label>
                            </div>

                            <div class="form-floating">
                                <input type="tel" class="form-control" id="empresaTelefone" name="telefone" placeholder="Telefone">
                                <label for="empresaTelefone"><i class="fas fa-phone me-2"></i>Telefone (apenas números)</label>
                            </div>

                            <div class="form-floating">
                                <textarea class="form-control" id="empresaDescricao" name="descricao_empresa" placeholder="Descrição da empresa" style="height: 100px"></textarea>
                                <label for="empresaDescricao"><i class="fas fa-info-circle me-2"></i>Descrição da empresa</label>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="acceptTerms" required>
                            <label class="form-check-label" for="acceptTerms">
                                Aceito os <a href="#" class="text-primary">Termos de Uso</a> e <a href="#" class="text-primary">Política de Privacidade</a>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-auth">
                            <i class="fas fa-user-plus me-2"></i>Criar Conta
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript para o novo design -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM carregado - configurando nova interface...');
            
            // Elementos das tabs
            const loginTab = document.getElementById('loginTab');
            const registerTab = document.getElementById('registerTab');
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            
            // Função para alternar entre tabs
            function switchTab(activeTab, activeForm, inactiveTab, inactiveForm) {
                // Remover classe active de todas as tabs
                loginTab.classList.remove('active');
                registerTab.classList.remove('active');
                
                // Ocultar todos os formulários
                loginForm.style.display = 'none';
                registerForm.style.display = 'none';
                
                // Ativar tab e formulário selecionados
                activeTab.classList.add('active');
                activeForm.style.display = 'block';
                
                // Adicionar animação
                activeForm.style.opacity = '0';
                activeForm.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    activeForm.style.transition = 'all 0.3s ease';
                    activeForm.style.opacity = '1';
                    activeForm.style.transform = 'translateY(0)';
                }, 10);
            }
            
            // Event listeners para as tabs
            if (loginTab) {
                loginTab.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Tab Login clicada');
                    switchTab(loginTab, loginForm, registerTab, registerForm);
                });
            }
            
            if (registerTab) {
                registerTab.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Tab Cadastro clicada');
                    switchTab(registerTab, registerForm, loginTab, loginForm);
                });
            }
            
            // Campos condicionais para cadastro
            const tipoUsuarioSelect = document.getElementById('tipoUsuario');
            const camposCandidato = document.getElementById('camposCandidato');
            const camposEmpresa = document.getElementById('camposEmpresa');

            if (tipoUsuarioSelect && camposCandidato && camposEmpresa) {
                tipoUsuarioSelect.addEventListener('change', function(e) {
                    const tipo = e.target.value;
                    console.log('Tipo de usuário selecionado:', tipo);
                    
                    // Ocultar todos os campos específicos
                    camposCandidato.style.display = 'none';
                    camposCandidato.classList.remove('show');
                    camposEmpresa.style.display = 'none';
                    camposEmpresa.classList.remove('show');
                    
                    // Mostrar campos específicos baseado na seleção
                    if (tipo === 'candidato') {
                        camposCandidato.style.display = 'block';
                        setTimeout(() => {
                            camposCandidato.classList.add('show');
                        }, 10);
                        console.log('Mostrando campos de candidato');
                    } else if (tipo === 'empresa') {
                        camposEmpresa.style.display = 'block';
                        setTimeout(() => {
                            camposEmpresa.classList.add('show');
                        }, 10);
                        console.log('Mostrando campos de empresa');
                    }
                });
            }
            
            // Validação do formulário de cadastro
            const cadastroForm = document.getElementById('cadastroForm');
            if (cadastroForm) {
                cadastroForm.addEventListener('submit', function(e) {
                    const tipoUsuario = document.getElementById('tipoUsuario').value;
                    const nome = document.getElementById('registerName').value.trim();
                    const email = document.getElementById('registerEmail').value.trim();
                    const senha = document.getElementById('registerPassword').value;
                    const acceptTerms = document.getElementById('acceptTerms').checked;
                    
                    console.log('Validando formulário de cadastro...');
                    
                    // Validações básicas
                    if (!nome || !email || !senha || !tipoUsuario) {
                        e.preventDefault();
                        alert('Por favor, preencha todos os campos obrigatórios.');
                        return false;
                    }
                    
                    if (!acceptTerms) {
                        e.preventDefault();
                        alert('Você deve aceitar os Termos de Uso e Política de Privacidade.');
                        return false;
                    }
                    
                    // Validação de senha
                    if (senha.length < 8) {
                        e.preventDefault();
                        alert('A senha deve ter pelo menos 8 caracteres.');
                        return false;
                    }
                    
                    // Validações específicas para empresas
                    if (tipoUsuario === 'empresa') {
                        const cnpj = document.getElementById('empresaCnpj').value.trim();
                        const telefone = document.getElementById('empresaTelefone').value.trim();
                        
                        if (!cnpj || !telefone) {
                            e.preventDefault();
                            alert('Para empresas, CNPJ e telefone são obrigatórios.');
                            return false;
                        }
                        
                        if (cnpj.length !== 14) {
                            e.preventDefault();
                            alert('CNPJ deve ter 14 dígitos.');
                            return false;
                        }
                    }
                    
                    // Validações específicas para candidatos
                    if (tipoUsuario === 'candidato') {
                        const telefoneCandidato = document.getElementById('candidatoTelefone').value.trim();
                        
                        if (!telefoneCandidato) {
                            e.preventDefault();
                            alert('Para candidatos, telefone é obrigatório.');
                            return false;
                        }
                    }
                    
                    console.log('Formulário de cadastro validado e enviado');
                });
            }
            
            // Melhorar UX com animações
            const formInputs = document.querySelectorAll('.form-floating input, .form-floating select, .form-floating textarea');
            formInputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.parentElement.classList.remove('focused');
                    }
                });
            });
            
            console.log('Interface configurada com sucesso!');
        });
    </script>
</body>
</html>