<?php
session_start();
require_once 'config.php';
requireAdminAuth();

$pageTitle = 'BASC LSMS | Dashboard';
$additionalCss = [];
$additionalScripts = [];

$contentFile = './pages/dashboard-content.php';

require_once './layouts/base.php';
?>
    