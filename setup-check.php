<?php
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'Oracle 10g Connection Check';
$connected = false;
$error = null;
$serverVersion = '';
$schemaUser = '';
$tableCount = 0;
$tables = [];
$required = ['ADMINISTRATOR','CAREER_FAIR','STUDENT','SKILL','STUDENT_SKILL','FAIR_REGISTRATION','COMPANY','COMPANY_PARTICIPATION','COMPANY_REPRESENTATIVE','BOOTH','BOOTH_ASSIGNMENT','JOB_VACANCY','JOB_REQUIRED_SKILL','APPLICATION','INTERVIEW','FAIR_SESSION','SESSION_ATTENDANCE','FEEDBACK','APP_USER'];
try {
    $db = db();
    $connected = true;
    $serverVersion = $db->serverVersion();
    $schemaUser = (string)$db->query('SELECT USER FROM dual')->fetchColumn();
    $rows = $db->query('SELECT table_name FROM user_tables ORDER BY table_name')->fetchAll();
    $tables = array_map(function ($r) { return strtoupper((string)$r['table_name']); }, $rows);
    $tableCount = count($tables);
} catch (Throwable $e) {
    $error = oracle_error_message($e);
}
$missing = array_values(array_diff($required, $tables));
include __DIR__ . '/includes/header.php';
?>
<div class="card form-card">
    <h2 style="margin-top:0">PHP -> OCI8 -> Oracle 10g XE</h2>
    <p>This page is designed as direct proof that the PHP interface is connected to the same Oracle schema used by the Career Fair database project.</p>
    <?php if ($connected): ?>
        <p class="connection-ok">✓ Oracle connection successful</p>
        <div class="grid grid-3">
            <div class="card"><div class="stat-label">Oracle Schema</div><strong><?= e($schemaUser) ?></strong></div>
            <div class="card"><div class="stat-label">Oracle Server</div><strong><?= e($serverVersion) ?></strong></div>
            <div class="card"><div class="stat-label">Tables Found</div><strong><?= e((string)$tableCount) ?></strong></div>
        </div>
        <h3>Detected CF_OWNER tables</h3>
        <div class="code"><?= e(implode("\n", $tables)) ?></div>
        <?php if ($missing): ?>
            <div class="alert danger" style="margin-top:14px"><strong>Missing required tables:</strong> <?= e(implode(', ', $missing)) ?><br>For APP_USER, run <code>database/01_web_support_setup.sql</code>. If core academic tables are missing, use your existing Oracle project scripts.</div>
        <?php else: ?>
            <div class="alert success" style="margin-top:14px">All core academic tables plus APP_USER were detected. This is a good page to include in the final documentation and video.</div>
        <?php endif; ?>
    <?php else: ?>
        <p class="connection-bad">✗ Oracle connection failed</p>
        <div class="alert danger"><?= e($error) ?></div>
        <ol>
            <li>Confirm Oracle XE services are running.</li>
            <li>Confirm PHP OCI8 is enabled in XAMPP and restart Apache.</li>
            <li>Set your CF_OWNER password in <code>config/database.local.php</code> (or via environment variables).</li>
            <li>Confirm the connection string is <code>127.0.0.1:1521/XE</code> or your actual XE listener address.</li>
        </ol>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
