<?php
// Teste simples de criação de vaga
require_once __DIR__ . '/app/utils/init.php';

echo "<h1>Teste Simples - Criação de Vaga</h1>";

// Verificar se está logado
if (!isset($_SESSION['usuario_id'])) {
    echo "<p style='color: red;'>❌ Faça login primeiro</p>";
    echo "<p><a href='app/views/auth.php'>Fazer Login</a></p>";
    exit();
}

echo "<p style='color: green;'>✅ Usuário logado: " . $_SESSION['usuario_id'] . " (Tipo: " . $_SESSION['usuario_tipo'] . ")</p>";

// Verificar se é empresa
if ($_SESSION['usuario_tipo'] !== 'empresa') {
    echo "<p style='color: red;'>❌ Usuário não é empresa</p>";
    exit();
}

// Buscar empresa
$empresaModel = new Empresa();
$empresa = $empresaModel->findByUsuarioId($_SESSION['usuario_id']);

if (!$empresa) {
    echo "<p style='color: red;'>❌ Empresa não encontrada</p>";
    echo "<p>Você precisa completar o cadastro da empresa primeiro.</p>";
    echo "<p><a href='app/views/editar_perfil_empresa.php'>Completar Cadastro da Empresa</a></p>";
    exit();
}

echo "<p style='color: green;'>✅ Empresa: " . $empresa['razao_social'] . " (ID: " . $empresa['id'] . ")</p>";

// Testar criação de vaga
$vagaModel = new Vaga();

$dados_vaga = [
    'empresa_id' => $empresa['id'],
    'titulo' => 'Vaga de Teste - ' . date('H:i:s'),
    'descricao' => 'Descrição da vaga de teste',
    'requisitos' => 'Requisitos da vaga de teste',
    'beneficios' => 'Benefícios da vaga de teste',
    'salario_min' => 1000,
    'salario_max' => 2000,
    'tipo_contrato' => 'clt',
    'modalidade' => 'presencial',
    'nivel_experiencia' => 'junior',
    'area' => 'tecnologia',
    'localizacao' => 'São Paulo',
    'data_limite' => date('Y-m-d', strtotime('+30 days'))
];

echo "<h3>Criando vaga de teste...</h3>";

$resultado = $vagaModel->create(
    $dados_vaga['empresa_id'],
    $dados_vaga['titulo'],
    $dados_vaga['descricao'],
    $dados_vaga['requisitos'],
    $dados_vaga['beneficios'],
    $dados_vaga['salario_min'],
    $dados_vaga['salario_max'],
    $dados_vaga['tipo_contrato'],
    $dados_vaga['modalidade'],
    $dados_vaga['nivel_experiencia'],
    $dados_vaga['area'],
    $dados_vaga['localizacao'],
    $dados_vaga['data_limite']
);

if ($resultado) {
    echo "<p style='color: green;'>✅ Vaga criada com sucesso!</p>";
    
    // Buscar vagas da empresa
    $vagas = $vagaModel->getByEmpresa($empresa['id']);
    echo "<p style='color: blue;'>📊 Total de vagas da empresa: " . count($vagas) . "</p>";
    
    if (count($vagas) > 0) {
        echo "<h3>Vagas da empresa:</h3>";
        echo "<ul>";
        foreach ($vagas as $vaga) {
            echo "<li>ID: {$vaga['id']} - {$vaga['titulo']} (Status: {$vaga['status']}) - {$vaga['created_at']}</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p style='color: red;'>❌ Erro ao criar vaga</p>";
}

echo "<hr>";
echo "<p><a href='app/views/gerenciar_vagas.php'>← Ver Gerenciar Vagas</a></p>";
echo "<p><a href='app/views/criar_vaga.php'>← Criar Nova Vaga</a></p>";
echo "<p><a href='check_database.php'>← Verificar Banco de Dados</a></p>";
?>
