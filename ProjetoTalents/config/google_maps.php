<?php
// Configuração da API do Google Maps
// IMPORTANTE: Substitua 'YOUR_API_KEY' pela sua chave real da API do Google Maps

// Chave da API do Google Maps
// Para obter uma chave:
// 1. Acesse: https://console.cloud.google.com/
// 2. Crie um novo projeto ou selecione um existente
// 3. Ative as APIs: Maps JavaScript API, Directions API, Places API
// 4. Crie credenciais (API Key)
// 5. Configure restrições de domínio para segurança

define('GOOGLE_MAPS_API_KEY', 'FREE_MODE');

// Configurações adicionais
define('GOOGLE_MAPS_DEFAULT_CENTER_LAT', -23.5505); // São Paulo
define('GOOGLE_MAPS_DEFAULT_CENTER_LNG', -46.6333); // São Paulo
define('GOOGLE_MAPS_DEFAULT_ZOOM', 10);

// Configurações de segurança
define('GOOGLE_MAPS_ALLOWED_DOMAINS', [
    'localhost',
    '127.0.0.1',
    'seu-dominio.com'
]);

// Função para verificar se o domínio é permitido
function isDomainAllowed($domain) {
    $allowedDomains = GOOGLE_MAPS_ALLOWED_DOMAINS;
    return in_array($domain, $allowedDomains);
}

// Função para obter a chave da API
function getGoogleMapsApiKey() {
    return GOOGLE_MAPS_API_KEY;
}
?>
