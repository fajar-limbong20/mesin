<?php
if (session_status() === PHP_SESSION_NONE) session_start();

define('BASE_URL', 'http://localhost/Mesin/');
define('APP_NAME', 'Prediksi Downtime Canon C10000');

date_default_timezone_set('Asia/Jakarta');

function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit;
}

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function set_flash($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function get_flash() {
    if (!isset($_SESSION['flash'])) return null;
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

function tanggal_indo($tgl) {
    if (!$tgl) return '-';
    $bulan = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $d = date('Y-m-d', strtotime($tgl));
    [$y, $m, $day] = explode('-', $d);
    return (int)$day . ' ' . $bulan[(int)$m] . ' ' . $y;
}