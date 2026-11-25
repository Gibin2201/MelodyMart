<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("../dboperation.php");
$obj = new dboperation();

if (isset($_POST['submit'])) {
    $cid = (int)$_POST['Customer_id'];
    $name = mysqli_real_escape_string($obj->con, trim($_POST['customername']));
    $email = mysqli_real_escape_string($obj->con, trim($_POST['email']));
    $phno = mysqli_real_escape_string($obj->con, trim($_POST['phonenumber']));
    $user = mysqli_real_escape_string($obj->con, trim($_POST['username']));
    $pass = mysqli_real_escape_string($obj->con, trim($_POST['password']));
    $locid = isset($_POST['locationid']) && !empty($_POST['locationid']) ? (int)$_POST['locationid'] : 0;

    // Verify customer owns this record or is the logged-in customer
    if (!isset($_SESSION['customer_id']) || (int)$_SESSION['customer_id'] !== $cid) {
        header("Location: ../Guest/login.php");
        exit();
    }

    // Check if new username is already taken by another customer
    $sqlCheckUser = "SELECT customer_id FROM tbl_customer WHERE username='$user' AND customer_id != $cid";
    $resCheckUser = $obj->executequery($sqlCheckUser);
    if ($resCheckUser && mysqli_num_rows($resCheckUser) > 0) {
        echo "<script>alert('Username is already in use by another musician! Please choose a different username.'); window.location='profileedit.php?cid=" . $cid . "';</script>";
        exit();
    }

    // Check if new email is already taken by another customer
    $sqlCheckEmail = "SELECT customer_id FROM tbl_customer WHERE email='$email' AND customer_id != $cid";
    $resCheckEmail = $obj->executequery($sqlCheckEmail);
    if ($resCheckEmail && mysqli_num_rows($resCheckEmail) > 0) {
        echo "<script>alert('Email is already in use by another account! Please enter a different email.'); window.location='profileedit.php?cid=" . $cid . "';</script>";
        exit();
    }

    if ($locid > 0) {
        $sql = "UPDATE tbl_customer 
                SET customer_name='$name', phone_no='$phno', email='$email', username='$user', password='$pass', location_id='$locid' 
                WHERE customer_id=$cid";
    } else {
        $sql = "UPDATE tbl_customer 
                SET customer_name='$name', phone_no='$phno', email='$email', username='$user', password='$pass' 
                WHERE customer_id=$cid";
    }

    $result = $obj->executequery($sql);

    if ($result) {
        // Sync active session username so navbar updates instantly
        $_SESSION['username'] = $user;
        header("Location: viewprofile.php?updated=1");
        exit();
    } else {
        header("Location: profileedit.php?cid=" . $cid . "&error=1");
        exit();
    }
} else {
    header("Location: viewprofile.php");
    exit();
}
?>