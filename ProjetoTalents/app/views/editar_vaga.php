<?php
// Arquivo: app/views/editar_vaga.php

require_once __DIR__ . '/../utils/init.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'empresa') {
    header("Location: " . BASE_DIR . "/app/views/auth.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: " . BASE_DIR . "/app/views/gerenciar_vagas.php");
    exit();
}

$vagaModel = new Vaga();
$empresaModel = new Empresa();

// Buscar empresa primeiro
$empresa = $empresaModel->findByUsuarioId($_SESSION['usuario_id']);

if (!$empresa) {
    $_SESSION['vaga_erro'] = "Empresa não encontrada. Complete seu cadastro de empresa.";
    header("Location: " . BASE_DIR . "/app/views/editar_perfil_empresa.php");
    exit();
}

$vaga_id = $_GET['id'];
$vaga = $vagaModel->findById($vaga_id);

if (!$vaga || $vaga['empresa_id'] !== $empresa['id']) {
    $_SESSION['vaga_erro'] = "Vaga não encontrada ou você não tem permissão para editá-la.";
    header("Location: " . BASE_DIR . "/app/views/gerenciar_vagas.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Vaga - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Editar vaga: <?php echo htmlspecialchars($vaga['titulo']); ?>">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_DIR; ?>/public/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_DIR; ?>/public/images/favicon.ico">
    
    <style>
        .edit-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .edit-card {
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
        
        .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            background-color: white;
            transition: var(--transition);
        }
        
        .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .btn-edit {
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
        
        .btn-edit:hover {
            background: var(--primary-dark);
            color: white;
        }
        
        .btn-secondary-edit {
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
        
        .btn-secondary-edit:hover {
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
        
        @media (max-width: 768px) {
            .edit-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
            
            .edit-card {
                padding: 1.5rem;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn-secondary-edit {
                margin-right: 0;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="edit-container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_DIR; ?>/app/views/gerenciar_vagas.php">Gerenciar Vagas</a></li>
                    <li class="breadcrumb-item active">Editar Vaga</li>
                </ol>
            </nav>
            
            <!-- Card de Edição -->
            <div class="edit-card">
                <h1 class="mb-4">
                    <i class="fas fa-edit me-2"></i>Editar Vaga
                </h1>
                
                <!-- Alertas -->
                <?php if (isset($_SESSION['vaga_sucesso'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['vaga_sucesso']); ?>
                    </div>
                    <?php unset($_SESSION['vaga_sucesso']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['vaga_erro'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_SESSION['vaga_erro']); ?>
                    </div>
                    <?php unset($_SESSION['vaga_erro']); ?>
                <?php endif; ?>

                <form action="<?php echo BASE_DIR; ?>/app/controllers/VagaController.php" method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($vaga['id']); ?>">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="titulo" class="form-label">Título da Vaga *</label>
                                <input type="text" id="titulo" name="titulo" class="form-control" 
                                       value="<?php echo htmlspecialchars($vaga['titulo']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status" class="form-label">Status *</label>
                                <select id="status" name="status" class="form-select" required>
                                    <option value="ativa" <?php echo $vaga['status'] === 'ativa' ? 'selected' : ''; ?>>Ativa</option>
                                    <option value="pausada" <?php echo $vaga['status'] === 'pausada' ? 'selected' : ''; ?>>Pausada</option>
                                    <option value="finalizada" <?php echo $vaga['status'] === 'finalizada' ? 'selected' : ''; ?>>Finalizada</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="descricao" class="form-label">Descrição da Vaga *</label>
                        <textarea id="descricao" name="descricao" class="form-control" rows="5" required><?php echo htmlspecialchars($vaga['descricao']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="requisitos" class="form-label">Requisitos *</label>
                        <textarea id="requisitos" name="requisitos" class="form-control" rows="5" required><?php echo htmlspecialchars($vaga['requisitos']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="beneficios" class="form-label">Benefícios</label>
                        <textarea id="beneficios" name="beneficios" class="form-control" rows="3"><?php echo htmlspecialchars($vaga['beneficios'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="localizacao" class="form-label">Localização *</label>
                                <input type="text" id="localizacao" name="localizacao" class="form-control" 
                                       value="<?php echo htmlspecialchars($vaga['localizacao']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="area" class="form-label">Área</label>
                                <input type="text" id="area" name="area" class="form-control" 
                                       value="<?php echo htmlspecialchars($vaga['area'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nivel_experiencia" class="form-label">Nível de Experiência *</label>
                                <select id="nivel_experiencia" name="nivel_experiencia" class="form-select" required>
                                    <option value="estagiario" <?php echo $vaga['nivel_experiencia'] === 'estagiario' ? 'selected' : ''; ?>>Estagiário</option>
                                    <option value="junior" <?php echo $vaga['nivel_experiencia'] === 'junior' ? 'selected' : ''; ?>>Júnior</option>
                                    <option value="pleno" <?php echo $vaga['nivel_experiencia'] === 'pleno' ? 'selected' : ''; ?>>Pleno</option>
                                    <option value="senior" <?php echo $vaga['nivel_experiencia'] === 'senior' ? 'selected' : ''; ?>>Sênior</option>
                                    <option value="especialista" <?php echo $vaga['nivel_experiencia'] === 'especialista' ? 'selected' : ''; ?>>Especialista</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tipo_contrato" class="form-label">Tipo de Contrato *</label>
                                <select id="tipo_contrato" name="tipo_contrato" class="form-select" required>
                                    <option value="clt" <?php echo $vaga['tipo_contrato'] === 'clt' ? 'selected' : ''; ?>>CLT</option>
                                    <option value="pj" <?php echo $vaga['tipo_contrato'] === 'pj' ? 'selected' : ''; ?>>PJ</option>
                                    <option value="estagio" <?php echo $vaga['tipo_contrato'] === 'estagio' ? 'selected' : ''; ?>>Estágio</option>
                                    <option value="trainee" <?php echo $vaga['tipo_contrato'] === 'trainee' ? 'selected' : ''; ?>>Trainee</option>
                                    <option value="freelancer" <?php echo $vaga['tipo_contrato'] === 'freelancer' ? 'selected' : ''; ?>>Freelancer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="modalidade" class="form-label">Modalidade *</label>
                                <select id="modalidade" name="modalidade" class="form-select" required>
                                    <option value="presencial" <?php echo $vaga['modalidade'] === 'presencial' ? 'selected' : ''; ?>>Presencial</option>
                                    <option value="remoto" <?php echo $vaga['modalidade'] === 'remoto' ? 'selected' : ''; ?>>Remoto</option>
                                    <option value="hibrido" <?php echo $vaga['modalidade'] === 'hibrido' ? 'selected' : ''; ?>>Híbrido</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="salario_min" class="form-label">Salário Mínimo (R$)</label>
                                <input type="number" id="salario_min" name="salario_min" class="form-control" 
                                       value="<?php echo htmlspecialchars($vaga['salario_min'] ?? ''); ?>" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="salario_max" class="form-label">Salário Máximo (R$)</label>
                                <input type="number" id="salario_max" name="salario_max" class="form-control" 
                                       value="<?php echo htmlspecialchars($vaga['salario_max'] ?? ''); ?>" step="0.01">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="data_limite" class="form-label">Data Limite para Candidaturas</label>
                        <input type="date" id="data_limite" name="data_limite" class="form-control" 
                               value="<?php echo htmlspecialchars($vaga['data_limite'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-actions">
                        <a href="<?php echo BASE_DIR; ?>/app/views/gerenciar_vagas.php" class="btn-secondary-edit">
                            <i class="fas fa-arrow-left me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn-edit">
                            <i class="fas fa-save me-2"></i>Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_DIR; ?>/public/js/main.js"></script>
</body>
</html>