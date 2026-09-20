<?php
require_once dirname(__DIR__) . '/config/app.php';

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return BASE_URL . ($path !== '' ? '/' . $path : '');
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $value;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['_csrf'] ?? '', $token)) {
        http_response_code(419);
        exit('Invalid CSRF token. Please go back and try again.');
    }
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('error', 'Please sign in first.');
        redirect('login.php');
    }
}

function require_role(string ...$roles): void
{
    require_login();
    $role = current_user()['role'] ?? '';
    if (!in_array($role, $roles, true)) {
        http_response_code(403);
        exit('403 - You do not have permission to access this page.');
    }
}

function role_home(string $role): string
{
    switch ($role) {
        case 'ADMIN': return 'admin/dashboard.php';
        case 'STUDENT': return 'student/dashboard.php';
        case 'COMPANY_REP': return 'company/dashboard.php';
        default: return 'login.php';
    }
}

function badge_class(string $status): string
{
    $s = strtoupper($status);
    if (in_array($s, ['APPROVED', 'OPEN', 'ACTIVE', 'ATTENDED', 'SELECTED', 'COMPLETED'], true)) return 'success';
    if (in_array($s, ['PENDING', 'UNDER_REVIEW', 'REGISTERED', 'SCHEDULED', 'SUBMITTED'], true)) return 'warning';
    if (in_array($s, ['REJECTED', 'CLOSED', 'CANCELLED', 'FAILED'], true)) return 'danger';
    if (in_array($s, ['SHORTLISTED', 'ASSIGNED'], true)) return 'info';
    return 'secondary';
}

function normalize_date(?string $value): ?string
{
    $value = trim((string)$value);
    return $value === '' ? null : $value;
}

function post(string $key, string $default = ''): string
{
    return trim((string)($_POST[$key] ?? $default));
}

function upload_resume(string $field = 'resume'): ?string
{
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Resume upload failed.');
    }
    if ($_FILES[$field]['size'] > 2 * 1024 * 1024) {
        throw new RuntimeException('Resume must be 2 MB or smaller.');
    }
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['pdf', 'doc', 'docx'], true)) {
        throw new RuntimeException('Resume must be PDF, DOC, or DOCX.');
    }
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0775, true);
    }
    $name = 'resume_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $target = UPLOAD_DIR . '/' . $name;
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $target)) {
        throw new RuntimeException('Could not save uploaded resume.');
    }
    return $name;
}

function input_date(?string $value): string
{
    $value = trim((string)$value);
    return $value === '' ? '' : substr($value, 0, 10);
}

function input_datetime(?string $value): string
{
    $value = trim((string)$value);
    if ($value === '') return '';
    $ts = strtotime($value);
    return $ts ? date('Y-m-d\TH:i', $ts) : '';
}

function oracle_error_message(Throwable $e): string
{
    $msg = trim($e->getMessage());
    // Keep the useful first ORA message and avoid overwhelming the UI.
    return preg_replace('/\s+/', ' ', $msg);
}

function display_date(?string $value): string
{
    $value = trim((string)$value);
    if ($value === '') return '';
    $ts = strtotime($value);
    return $ts ? date('d-M-Y', $ts) : $value;
}

function display_time(?string $value): string
{
    $value = trim((string)$value);
    if ($value === '') return '';
    // interview_time is stored as VARCHAR2 like 10:00; DATE values include a date.
    if (preg_match('/^\d{1,2}:\d{2}/', $value, $m)) return $m[0];
    $ts = strtotime($value);
    return $ts ? date('H:i', $ts) : $value;
}
