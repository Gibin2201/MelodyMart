<?php
session_start();
include_once("../dboperation.php");

$obj = new dboperation();
$customerid = $_SESSION['customer_id'];

if (isset($_POST["submit"])) {

    // --- Step 1: Collect form data ---
    $personname = $_POST['fullname'];
    $mobno = $_POST['phone'];
    $address = $_POST['address'];
    $pincode = $_POST['pincode'];

    $totalamount = $_POST["total_amount"];
    $paiddate = date("Y-m-d");
    $status = "Paid";
    $commission = $totalamount * 0.10;

    // --- Step 2: Insert into tbl_cartmaster ---
    $cartSql = "INSERT INTO tbl_cartmaster(grandtotal, customer_id, cmstatus)
                VALUES ('$totalamount', '$customerid', '$status')";
    $obj->executequery($cartSql);

    // Get last inserted cartmaster id
    $cartId = mysqli_insert_id($obj->con);

    // --- Step 3: Insert into tbl_payment ---
    $paymentSql = "INSERT INTO tbl_payment(cartmaster_id, payment_date, pamount, customer_id, pstatus, commission)
                   VALUES ('$cartId', '$paiddate', '$totalamount', '$customerid', '$status', '$commission')";
    $obj->executequery($paymentSql);

    // --- Step 4: Insert into tbl_deliveryaddress (with cartmasterid) ---
    $addressSql = "INSERT INTO tbl_deliveryaddress(personname, mobno, address, pincode, cartmasterid)
                   VALUES ('$personname', '$mobno', '$address', '$pincode', '$cartId')";
    $obj->executequery($addressSql);

    // --- Step 5: Update cart items for this customer ---
    $updatecartSql = "UPDATE tbl_cart 
                      SET cartmaster_id = '$cartId', status = 'paid' 
                      WHERE (cartmaster_id IS NULL OR status = 'cart') AND customer_id = '$customerid'";
    $obj->executequery($updatecartSql);

    // --- Step 6: Decrease product stock ---
    $cartItems = $obj->executequery("SELECT product_id, quantity FROM tbl_cart 
                                     WHERE cartmaster_id = '$cartId' AND customer_id = '$customerid'");
    while ($row = mysqli_fetch_array($cartItems)) {
        $productId = $row['product_id'];
        $qty = $row['quantity'];

        // reduce stock
        $obj->executequery("UPDATE tbl_product SET stock = stock - $qty WHERE product_id = '$productId'");

        // check stock
        $stockRes = $obj->executequery("SELECT stock FROM tbl_product WHERE product_id = '$productId'");
        $stockRow = mysqli_fetch_array($stockRes);
        if ($stockRow['stock'] <= 0) {
            $obj->executequery("UPDATE tbl_product SET pstatus='Unavailable', stock=0 WHERE product_id='$productId'");
        }
    }

    // Redirect to Order Confirmation & Warranty receipt page
    header("Location: ordersuccess.php?order_id=" . $cartId);
    exit();
}
?>
