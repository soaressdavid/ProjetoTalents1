<?php
// EXEMPLO de configuração da API do Google Maps
// COPIE este arquivo para google_maps.php e configure sua chave

// Chave da API do Google Maps
// IMPORTANTE: Substitua 'SUA_CHAVE_AQUI' pela sua chave real da API do Google Maps
define('GOOGLE_MAPS_API_KEY', 'SUA_CHAVE_AQUI');

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

// Exemplo de chave válida (formato):
// define('GOOGLE_MAPS_API_KEY', 'AIzaSyBvOkBwvQwQwQwQwQwQwQwQwQwQwQwQwQwQ');
?>
