<?php
require_once __DIR__ . '/auth.php';
$user = current_user();
$page_title = $page_title ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title) ?> | <?= APP_NAME ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background: #f4f6f9; }
    .sidebar { min-height: 100vh; background: #1e293b; color: #cbd5e1; position: sticky; top: 0; }
    .sidebar a { color: #cbd5e1; text-decoration: none; padding: 10px 15px; display: block; border-radius: 6px; margin-bottom: 3px; }
    .sidebar a:hover, .sidebar a.active { background: #334155; color: #fff; }
    .sidebar .brand { color: #fff; font-weight: 700; padding: 20px 15px; border-bottom: 1px solid #334155; }
    .content { padding: 20px 30px; min-height: 100vh; }
    .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.08); border-radius: 8px; }
    .stat-card { color: #fff; border-radius: 10px; }
</style>
</head>
<body>
<div class="container-fluid">
<div class="row">