<?php
// Application configuration.
// If you rename the project folder, update BASE_URL below.
define('APP_NAME', 'Career Fair Management System');
define('BASE_URL', '/career_fair_management');
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/resumes');
define('UPLOAD_URL', BASE_URL . '/uploads/resumes');

date_default_timezone_set('Asia/Dhaka');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
