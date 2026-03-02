<?php
session_start();
require_once 'config.php';
requireAdminAuth();

$pageTitle = 'Survey Responses';
$additionalCss = [];
$additionalScripts = ['./assets/responses.js'];

$contentFile = './pages/responses-content.php';

require_once './layouts/base.php';
?>
