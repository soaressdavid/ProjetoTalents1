<?php
// Script para testar se a API do Google Maps está configurada corretamente

// Incluir configuração
require_once 'config/google_maps.php';

// Função para testar a API
function testarApiGoogleMaps($apiKey) {
    if ($apiKey === 'YOUR_API_KEY' || $apiKey === 'SUA_CHAVE_AQUI' || empty($apiKey)) {
        return [
            'status' => 'error',
            'mensagem' => 'API key não configurada',
            'detalhes' => 'Configure a chave da API em config/google_maps.php'
        ];
    }
    
    // Testar com uma requisição simples de geocodificação
    $endereco = 'Avenida Paulista, 1000, São Paulo';
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($endereco) . "&key=" . $apiKey;
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'method' => 'GET'
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        return [
            'status' => 'error',
            'mensagem' => 'Erro na requisição',
            'detalhes' => 'Verifique sua conexão com a internet'
        ];
    }
    
    $data = json_decode($response, true);
    
    if (isset($data['status'])) {
        switch ($data['status']) {
            case 'OK':
                return [
                    'status' => 'success',
                    'mensagem' => 'API funcionando corretamente',
                    'detalhes' => 'Chave válida e APIs habilitadas',
                    'exemplo' => $data['results'][0]['formatted_address'] ?? 'Endereço encontrado'
                ];
            case 'REQUEST_DENIED':
                return [
                    'status' => 'error',
                    'mensagem' => 'API key inválida ou restrita',
                    'detalhes' => 'Verifique se a chave está correta e se as APIs estão habilitadas'
                ];
            case 'OVER_QUERY_LIMIT':
                return [
                    'status' => 'error',
                    'mensagem' => 'Limite de cota excedido',
                    'detalhes' => 'Aguarde ou verifique sua cota no Google Cloud Console'
                ];
            default:
                return [
                    'status' => 'error',
                    'mensagem' => 'Erro na API: ' . $data['status'],
                    'detalhes' => $data['error_message'] ?? 'Erro desconhecido'
                ];
        }
    }
    
    return [
        'status' => 'error',
        'mensagem' => 'Resposta inválida da API',
        'detalhes' => 'Verifique a configuração'
    ];
}

// Obter chave da API
$apiKey = getGoogleMapsApiKey();

// Testar API
$resultado = testarApiGoogleMaps($apiKey);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste da API do Google Maps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .status-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 2px solid #28a745;
        }
        .status-error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border: 2px solid #dc3545;
        }
        .status-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 2px solid #ffc107;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card <?php echo $resultado['status'] === 'success' ? 'status-success' : 'status-error'; ?>">
                    <div class="card-header <?php echo $resultado['status'] === 'success' ? 'bg-success text-white' : 'bg-danger text-white'; ?>">
                        <h4 class="mb-0">
                            <i class="fas fa-<?php echo $resultado['status'] === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                            Teste da API do Google Maps
                        </h4>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">
                            <?php echo $resultado['mensagem']; ?>
                        </h5>
                        <p class="card-text">
                            <?php echo $resultado['detalhes']; ?>
                        </p>
                        
                        <?php if (isset($resultado['exemplo'])): ?>
                        <div class="alert alert-info">
                            <strong>Exemplo de funcionamento:</strong><br>
                            <code><?php echo htmlspecialchars($resultado['exemplo']); ?></code>
                        </div>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <h6>Informações da Configuração:</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong>Chave da API:</strong> 
                                <?php if ($apiKey === 'YOUR_API_KEY' || $apiKey === 'SUA_CHAVE_AQUI'): ?>
                                    <span class="text-danger">Não configurada</span>
                                <?php else: ?>
                                    <span class="text-success">Configurada</span>
                                    <br><small class="text-muted"><?php echo substr($apiKey, 0, 10) . '...'; ?></small>
                                <?php endif; ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Status:</strong> 
                                <span class="badge bg-<?php echo $resultado['status'] === 'success' ? 'success' : 'danger'; ?>">
                                    <?php echo strtoupper($resultado['status']); ?>
                                </span>
                            </li>
                        </ul>
                        
                        <?php if ($resultado['status'] !== 'success'): ?>
                        <div class="mt-4">
                            <h6>Como corrigir:</h6>
                            <ol>
                                <li>Acesse <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></li>
                                <li>Habilite as APIs: Directions API, Places API, Geocoding API</li>
                                <li>Gere uma chave de API</li>
                                <li>Configure em <code>config/google_maps.php</code></li>
                                <li>Execute este teste novamente</li>
                            </ol>
                        </div>
                        <?php else: ?>
                        <div class="mt-4">
                            <div class="alert alert-success">
                                <h6><i class="fas fa-check-circle me-2"></i>API Funcionando!</h6>
                                <p class="mb-0">Agora você pode usar a calculadora de deslocamento com dados reais do Google Maps.</p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mt-3">
                            <a href="app/views/calculadora_deslocamento.php" class="btn btn-primary">
                                <i class="fas fa-route me-2"></i>Testar Calculadora
                            </a>
                            <a href="CONFIGURAR_API_GOOGLE_MAPS.md" class="btn btn-outline-secondary">
                                <i class="fas fa-book me-2"></i>Guia de Configuração
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
