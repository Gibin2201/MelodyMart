<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("../dboperation.php");
$obj = new dboperation();

if (isset($_POST['submit'])) {
    if (!isset($_SESSION['customer_id'])) {
        header("Location: ../Guest/login.php");
        exit();
    }

    $customerid = (int)$_SESSION['customer_id'];
    $cartid = (int)$_POST['cartid'];
    $applydate = date('Y-m-d');
    $issue = mysqli_real_escape_string($obj->con, trim($_POST['issue']));
    $wstatus = "requested";

    // Upload warranty card with sanitized name
    $wimg = "";
    if (isset($_FILES["wimg"]) && !empty($_FILES["wimg"]["name"])) {
        $wimg = time() . "_wcard_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", $_FILES["wimg"]["name"]);
        $wimg_tmp = $_FILES["wimg"]["tmp_name"];
        move_uploaded_file($wimg_tmp, "../uploads/" . $wimg);
    }

    // Upload product defect image with sanitized name
    $proimg = "";
    if (isset($_FILES["proimg"]) && !empty($_FILES["proimg"]["name"])) {
        $proimg = time() . "_defect_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", $_FILES["proimg"]["name"]);
        $proimg_tmp = $_FILES["proimg"]["tmp_name"];
        move_uploaded_file($proimg_tmp, "../uploads/" . $proimg);
    }

    $sqlquery1 = "INSERT INTO tbl_warranty 
                  (customer_id, cart_id, applydate, wimg, issuedesc, prod_img, wstatus) 
                  VALUES 
                  ('$customerid', '$cartid', '$applydate', '$wimg', '$issue', '$proimg', '$wstatus')";

    $result1 = $obj->executequery($sqlquery1);

    if ($result1) {
        echo "<script>alert('Warranty claim submitted successfully! MelodyMart team will review your claim.'); window.location='warrantyview.php';</script>";
    } else {
        echo "<script>alert('Failed to submit warranty claim. Please try again.'); window.location='warranty.php?cartid=" . $cartid . "';</script>";
    }
} else {
    header("Location: vieworders.php");
    exit();
}
?>