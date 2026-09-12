<?php
session_set_cookie_params(['path' => '/']);
session_start();

// Bypass admin login
$_SESSION['admin_id'] = 1;
$_SESSION['admin_name'] = 'Admin';

header("Location: admin/reports.php");
exit();
?>