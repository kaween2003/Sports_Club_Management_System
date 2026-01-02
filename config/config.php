<?php
// Sports Club Management System (SCMS) - Version 1.0
// Configuration file for database connection - N.G.Kaween Newmal

$host = "fdb1032.awardspace.net";
$db   = "4715453_spotssystem";
$user = "4715453_spotssystem";
$pass = "Kaween&2003";

// Simple MySQLi connection
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Start session for login system
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
