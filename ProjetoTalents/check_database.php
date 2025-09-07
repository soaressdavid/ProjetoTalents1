<?php
// Script para verificar o banco de dados
require_once __DIR__ . '/app/utils/init.php';

echo "<h1>Verificação do Banco de Dados</h1>";

try {
    $conn = getDbConnection();
    echo "<p style='color: green;'>✅ Conexão com banco estabelecida</p>";
    
    // Verificar se as tabelas existem
    $tables = ['usuarios', 'empresas', 'vagas', 'candidaturas'];
    
    foreach ($tables as $table) {
        $stmt = $conn->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $exists = $stmt->fetch();
        
        if ($exists) {
            echo "<p style='color: green;'>✅ Tabela '$table' existe</p>";
            
            // Contar registros
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM $table");
            $stmt->execute();
            $count = $stmt->fetch()['total'];
            echo "<p style='color: blue;'>📊 Registros em '$table': $count</p>";
            
            // Se for a tabela vagas, mostrar algumas vagas
            if ($table === 'vagas' && $count > 0) {
                $stmt = $conn->prepare("SELECT id, titulo, empresa_id, status, created_at FROM vagas ORDER BY created_at DESC LIMIT 5");
                $stmt->execute();
                $vagas = $stmt->fetchAll();
                
                echo "<h3>Últimas 5 vagas:</h3>";
                echo "<ul>";
                foreach ($vagas as $vaga) {
                    echo "<li>ID: {$vaga['id']} - {$vaga['titulo']} (Empresa: {$vaga['empresa_id']}, Status: {$vaga['status']}) - {$vaga['created_at']}</li>";
                }
                echo "</ul>";
            }
        } else {
            echo "<p style='color: red;'>❌ Tabela '$table' não existe</p>";
        }
    }
    
    // Verificar usuários logados
    if (isset($_SESSION['usuario_id'])) {
        echo "<h3>Usuário Logado:</h3>";
        echo "<p>ID: " . $_SESSION['usuario_id'] . "</p>";
        echo "<p>Tipo: " . $_SESSION['usuario_tipo'] . "</p>";
        
        // Buscar empresa do usuário
        $stmt = $conn->prepare("SELECT * FROM empresas WHERE usuario_id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        $empresa = $stmt->fetch();
        
        if ($empresa) {
            echo "<p style='color: green;'>✅ Empresa encontrada: {$empresa['razao_social']} (ID: {$empresa['id']})</p>";
            
            // Buscar vagas da empresa
            $stmt = $conn->prepare("SELECT * FROM vagas WHERE empresa_id = ?");
            $stmt->execute([$empresa['id']]);
            $vagas_empresa = $stmt->fetchAll();
            
            echo "<p style='color: blue;'>📊 Vagas da empresa: " . count($vagas_empresa) . "</p>";
            
            if (count($vagas_empresa) > 0) {
                echo "<h3>Vagas da empresa:</h3>";
                echo "<ul>";
                foreach ($vagas_empresa as $vaga) {
                    echo "<li>ID: {$vaga['id']} - {$vaga['titulo']} (Status: {$vaga['status']}) - {$vaga['created_at']}</li>";
                }
                echo "</ul>";
            }
        } else {
            echo "<p style='color: red;'>❌ Nenhuma empresa encontrada para o usuário</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Nenhum usuário logado</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='app/views/gerenciar_vagas.php'>← Voltar para Gerenciar Vagas</a></p>";
?>
