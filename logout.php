<?php
// SCMS Version 1.0 - Logout script - N.G.Kaween Newmal
require_once "config/config.php";

session_unset();
session_destroy();

header("Location: index.php");
exit();
