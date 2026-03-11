<?php
session_start();
require_once 'config.php';
requireAdminAuth();

$pageTitle = 'Generate Link';
$additionalCss = [];
$additionalScripts = [];

$contentFile = './pages/generate-link.php';

require_once './layouts/base.php';
?>