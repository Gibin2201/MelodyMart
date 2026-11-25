<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("../dboperation.php");
$obj = new dboperation();

if (isset($_POST['submit']) || (isset($_POST['productid']) && $_SERVER['REQUEST_METHOD'] === 'POST')) {
    if (!isset($_SESSION['customer_id'])) {
        header("Location: ../Guest/login.php");
        exit();
    }

    $pid = (int)($_POST['productid'] ?? 0);
    $customerid = (int)$_SESSION['customer_id'];
    $tamount = (float)($_POST['totalamount'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);
    $cstatus = 'cart';
    $cdate = date('Y-m-d');

    if ($pid <= 0) {
        header("Location: catview.php");
        exit();
    }

    // Check if the item is already in the cart for this customer
    $checkSql = "SELECT * FROM tbl_cart WHERE product_id = '$pid' AND customer_id = '$customerid' AND status = 'cart'";
    $checkRes = $obj->executequery($checkSql);

    if ($checkRes && mysqli_num_rows($checkRes) > 0) {
        echo "<script>alert('Item is already in cart'); window.location='cartview.php';</script>";
        exit();
    }

    $sqlquery1 = "INSERT INTO tbl_cart (product_id, quantity, totalamount, customer_id, cart_date, status) 
                  VALUES ('$pid', '$quantity', '$tamount', '$customerid', '$cdate', '$cstatus')";
    $result1 = $obj->executequery($sqlquery1);

    if ($result1) {
        header("Location: cartview.php");
        exit();
    } else {
        echo "<script>alert('Failed to add item to cart. Please try again.'); window.location='ptview.php?productid=" . $pid . "';</script>";
        exit();
    }
} else {
    header("Location: catview.php");
    exit();
}
?>
