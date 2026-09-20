<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? APP_NAME;
$user = current_user();
$role = $user['role'] ?? null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
</head>
<body>
<div class="app-shell">
    <?php if ($user): ?>
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="<?= e(url(role_home($role))) ?>">
            <span class="brand-mark">CF</span>
            <span><strong>Career Fair</strong><small>Management</small></span>
        </a>
        <nav>
            <?php if ($role === 'ADMIN'): ?>
                <a href="<?= e(url('admin/dashboard.php')) ?>">Dashboard</a>
                <a href="<?= e(url('admin/fairs.php')) ?>">Career Fairs</a>
                <a href="<?= e(url('admin/students.php')) ?>">Students</a>
                <a href="<?= e(url('admin/companies.php')) ?>">Companies</a>
                <a href="<?= e(url('admin/booths.php')) ?>">Booths</a>
                <a href="<?= e(url('admin/jobs.php')) ?>">Job Vacancies</a>
                <a href="<?= e(url('admin/applications.php')) ?>">Applications</a>
                <a href="<?= e(url('admin/interviews.php')) ?>">Interviews</a>
                <a href="<?= e(url('admin/sessions.php')) ?>">Sessions</a>
                <a href="<?= e(url('admin/feedback.php')) ?>">Feedback</a>
            <?php elseif ($role === 'STUDENT'): ?>
                <a href="<?= e(url('student/dashboard.php')) ?>">Dashboard</a>
                <a href="<?= e(url('student/fairs.php')) ?>">Career Fairs</a>
                <a href="<?= e(url('student/jobs.php')) ?>">Jobs</a>
                <a href="<?= e(url('student/applications.php')) ?>">My Applications</a>
                <a href="<?= e(url('student/sessions.php')) ?>">Sessions</a>
                <a href="<?= e(url('student/feedback.php')) ?>">Feedback</a>
            <?php elseif ($role === 'COMPANY_REP'): ?>
                <a href="<?= e(url('company/dashboard.php')) ?>">Dashboard</a>
                <a href="<?= e(url('company/participation.php')) ?>">Fair Participation</a>
                <a href="<?= e(url('company/jobs.php')) ?>">Job Vacancies</a>
                <a href="<?= e(url('company/applications.php')) ?>">Applications</a>
                <a href="<?= e(url('company/interviews.php')) ?>">Interviews</a>
            <?php endif; ?>
            <a href="<?= e(url('setup-check.php')) ?>">Connection Check</a>
        </nav>
        <div class="sidebar-user">
            <strong><?= e($user['display_name'] ?? $user['username']) ?></strong>
            <small><?= e(str_replace('_', ' ', $role)) ?></small>
            <a class="logout-link" href="<?= e(url('logout.php')) ?>">Sign out</a>
        </div>
    </aside>
    <?php endif; ?>

    <main class="main <?= $user ? '' : 'main-public' ?>">
        <?php if ($user): ?>
        <header class="topbar">
            <button class="menu-button" type="button" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
            <div>
                <div class="eyebrow">Career Fair Management System</div>
                <h1><?= e($pageTitle) ?></h1>
            </div>
            <div class="topbar-user"><?= e($user['display_name'] ?? '') ?></div>
        </header>
        <?php endif; ?>
        <div class="content <?= $user ? '' : 'public-content' ?>">
            <?php if ($msg = flash('success')): ?><div class="alert success"><?= e($msg) ?></div><?php endif; ?>
            <?php if ($msg = flash('error')): ?><div class="alert danger"><?= e($msg) ?></div><?php endif; ?>
