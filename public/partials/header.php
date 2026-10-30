<?php
// Page title defaults
$pageTitle = isset($pageTitle) ? $pageTitle : 'OsunBiz';
$appName   = isset($appName) ? $appName : 'OsunBiz';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> | <?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="/" class="logo"><span class="logo-mark">OB</span><span class="logo-txt"><strong><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></strong><small>Business Directory</small></span></a>
            <nav class="nav">
                <a href="/">Home</a>
                <a href="/directory">Directory</a>
                <a href="/categories">Categories</a>
                <a href="/locations">Locations</a>
                <a href="/about">About</a>
                <a href="/contact">Contact</a>
            </nav>
            <a href="/login" class="btn btn-primary">Sign In</a>
        </div>
    </header>

    <main class="container">
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type'], ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($_SESSION['flash']['message'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>