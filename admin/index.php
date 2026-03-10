<?php
/**
 * Admin Panel Home/Index - Redirect to unified dashboard
 */
session_start();
require_once 'config.php';
requireAdminAuth();

header('Location: ./dashboard.php');
exit;
