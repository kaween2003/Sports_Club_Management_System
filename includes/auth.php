<?php
// SCMS Version 1.0 - Basic authentication helper - N.G.Kaween Newmal

function check_login()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }
}

function check_admin()
{
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        header("Location: member_dashboard.php");
        exit();
    }
}

function check_member()
{
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'member') {
        header("Location: admin_dashboard.php");
        exit();
    }
}
?>
