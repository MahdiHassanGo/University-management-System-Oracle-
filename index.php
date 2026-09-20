<?php
require_once __DIR__ . '/includes/functions.php';
if (is_logged_in()) {
    redirect(role_home(current_user()['role']));
}
redirect('login.php');
