<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'PHP / OCI8 Diagnostics';
$oci = function_exists('oci_connect');
$phpVersion = PHP_VERSION;
$arch = PHP_INT_SIZE === 8 ? '64-bit (x64)' : '32-bit (x86)';
$sapi = PHP_SAPI;
include __DIR__ . '/includes/header.php';
?>
<div class="card form-card">
    <h2 style="margin-top:0">PHP / OCI8 Diagnostics</h2>
    <div class="grid grid-3">
        <div class="card"><div class="stat-label">PHP Version</div><strong><?= e($phpVersion) ?></strong></div>
        <div class="card"><div class="stat-label">PHP Architecture</div><strong><?= e($arch) ?></strong></div>
        <div class="card"><div class="stat-label">Server API</div><strong><?= e($sapi) ?></strong></div>
    </div>
    <?php if ($oci): ?>
        <p class="connection-ok">✓ OCI8 is loaded: oci_connect() is available.</p>
        <p>Next: configure <code>config/database.php</code>, then open <a href="<?= e(url('setup-check.php')) ?>">Oracle Connection Check</a>.</p>
    <?php else: ?>
        <p class="connection-bad">✗ OCI8 is not loaded.</p>
        <p>Enable/install the PHP OCI8 extension, make sure its Oracle client DLLs match PHP x86/x64, restart Apache, and reload this page.</p>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
