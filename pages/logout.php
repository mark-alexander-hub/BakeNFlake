<?php
require_once __DIR__ . '/../config/init.php';
session_destroy();
header('Location: ' . SITE_URL . '/pages/home.php');
exit;
