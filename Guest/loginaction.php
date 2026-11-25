<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("../dboperation.php");
$obj = new dboperation();

$username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
$password = isset($_POST["password"]) ? trim($_POST["password"]) : "";

if (empty($username) || empty($password)) {
    header("Location: login.php");
    exit();
}

// 1. Check Admin Login
$sqlAdmin = "SELECT * FROM tbl_adminlogin WHERE username='$username' AND Password='$password'";
$resAdmin = $obj->executequery($sqlAdmin);
if ($resAdmin && mysqli_num_rows($resAdmin) === 1) {
    $row = mysqli_fetch_array($resAdmin);
    $_SESSION["username"] = $username;
    $_SESSION["Login_id"] = $row["Login_id"];
    header("Location: ../Admin/index.php");
    exit();
}

// 2. Check Customer Login
$sqlCustomer = "SELECT * FROM tbl_customer WHERE username='$username' AND password='$password'";
$resCustomer = $obj->executequery($sqlCustomer);
if ($resCustomer && mysqli_num_rows($resCustomer) === 1) {
    $row = mysqli_fetch_array($resCustomer);
    $_SESSION["username"] = $username;
    $_SESSION["customer_id"] = $row["customer_id"];

    // Default customer destination or requested product page
    $redirect = "../Customer/index.php";
    if (isset($_POST["redirect"]) && !empty($_POST["redirect"])) {
        $candidate = trim($_POST["redirect"]);
        if (strpos($candidate, '://') === false && strpos($candidate, '//') !== 0) {
            $redirect = $candidate;
        }
    }
    header("Location: " . $redirect);
    exit();
}

// Invalid Credentials - display clean error or redirect back
echo "<script>alert('Invalid Username or Password'); window.location='login.php';</script>";
exit();
?>