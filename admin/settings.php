<?php
session_start();
require_once 'config.php';
requireAdminAuth();

$pageTitle = 'Settings';
$additionalCss = [];
$additionalScripts = ['./assets/settings.js'];

$contentFile = './pages/settings-content.php';

require_once './layouts/base.php';
?>
