<?php
// Arquivo: app/utils/init.php
// TalentsHUB - Inicialização do Sistema

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurações do TalentsHUB
define('BASE_DIR', '/ProjetoTalents');
define('SITE_NAME', 'TalentsHUB');
define('SITE_DESCRIPTION', 'Plataforma de Recrutamento e Seleção');
define('SITE_VERSION', '1.0.0');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Vaga.php';
require_once __DIR__ . '/../models/Candidatura.php';
require_once __DIR__ . '/../models/PerfilCandidato.php';
require_once __DIR__ . '/../models/Empresa.php';
require_once __DIR__ . '/../models/Candidato.php';
require_once __DIR__ . '/../models/Notification.php';

// A conexão não é mais global, os modelos a obtêm através de `getDbConnection()`
$conn = getDbConnection();
?>