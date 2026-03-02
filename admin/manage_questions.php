<?php
session_start();
require_once 'config.php';
requireAdminAuth();

$pageTitle = 'Manage Survey Questions';
$additionalCss = ['./assets/questions.css'];
$additionalScripts = ['./assets/questions.js'];

$contentFile = './pages/questions-content.php';

require_once './layouts/base.php';
?>
