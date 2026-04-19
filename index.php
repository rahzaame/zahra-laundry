<?php
session_start();
if (isset($_SESSION["id_user"])) {
    header("Location: dashboard/dashboard.php");
} else {
    header("Location: login.php");
}
exit();