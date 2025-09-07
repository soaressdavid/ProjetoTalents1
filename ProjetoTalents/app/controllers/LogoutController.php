<?php
// Arquivo: app/controllers/LogoutController.php

require_once __DIR__ . '/../utils/init.php';

session_unset();
session_destroy();
header("Location: " . BASE_DIR . "/app/views/auth.php");
exit();
?>