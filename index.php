<?php
require_once __DIR__ . '/config/config.php';

if (isset($_SESSION['user_id'])) {
    redirect('pages/dashboard.php');
} else {
    redirect('auth/login.php');
}