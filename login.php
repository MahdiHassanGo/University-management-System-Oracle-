<?php
require_once __DIR__ . '/includes/auth.php';
if (is_logged_in()) {
    redirect(role_home(current_user()['role']));
}
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = post('username');
    $password = $_POST['password'] ?? '';
    try {
        $stmt = db()->prepare('SELECT * FROM app_user WHERE username = ? AND is_active = 1 AND ROWNUM = 1');
        $stmt->execute([$username]);
        $account = $stmt->fetch();
        if ($account && password_verify($password, $account['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'user_id' => (int)$account['user_id'],
                'username' => $account['username'],
                'role' => $account['role'],
                'related_id' => (int)$account['related_id'],
                'display_name' => $account['display_name'],
            ];
            redirect(role_home($account['role']));
        }
        $error = 'Invalid username or password.';
    } catch (Throwable $e) {
        $error = 'Oracle connection/login setup problem: ' . oracle_error_message($e);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sign In - Career Fair Management System</title>
<link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
</head>
<body>
<section class="hero">
    <div class="hero-copy">
        <div class="eyebrow" style="color:#b9cbe1">AIUB ADBMS Final Project</div>
        <h1>Career Fair Management System</h1>
        <p>A PHP user interface connected directly to the Oracle 10g XE Career Fair database. The website reads and writes the same CF_OWNER tables used in the database project.</p>
        <div class="feature-list">
            <div class="feature">Oracle 10g XE / CF_OWNER</div>
            <div class="feature">PHP OCI8 connection</div>
            <div class="feature">Role-based dashboards</div>
            <div class="feature">Live Oracle CRUD operations</div>
        </div>
    </div>
    <div class="login-card">
        <h2>Sign in</h2>
        <div class="muted">Run <code>database/01_web_support_setup.sql</code> once before using these accounts.</div>
        <?php if ($error): ?><div class="alert danger"><?= e($error) ?></div><?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <div class="field"><label>Username / Email</label><input type="text" name="username" required autocomplete="username"></div>
            <div class="field"><label>Password</label><input type="password" name="password" required autocomplete="current-password"></div>
            <button class="btn primary" type="submit">Sign In</button>
        </form>
        <p style="margin:18px 0 0;text-align:center"><a href="<?= e(url('setup-check.php')) ?>">Run Oracle connection check</a></p>
    </div>
</section>
</body></html>
