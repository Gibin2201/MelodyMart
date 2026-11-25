<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("../dboperation.php");
$obj = new dboperation();

if (!isset($_SESSION['customer_id'])) {
    header("Location: ../Guest/login.php");
    exit();
}

if (isset($_GET['cartid']) && isset($_GET['action'])) {
    $cartid = (int)$_GET['cartid'];
    $action = $_GET['action'];
    $customerid = (int)$_SESSION['customer_id'];

    $q = "SELECT c.*, p.amount, p.stock FROM tbl_cart c 
          INNER JOIN tbl_product p ON p.product_id = c.product_id 
          WHERE c.cart_id = $cartid AND c.customer_id = $customerid AND c.status = 'cart'";
    $res = $obj->executequery($q);
    if ($res && $r = mysqli_fetch_assoc($res)) {
        $curQty = (int)$r['quantity'];
        $unitPrice = (float)$r['amount'];
        $maxStock = (int)$r['stock'];

        if ($action === 'inc' && $curQty < $maxStock) {
            $newQty = $curQty + 1;
        } elseif ($action === 'dec' && $curQty > 1) {
            $newQty = $curQty - 1;
        } else {
            $newQty = $curQty;
        }

        $newTotal = $newQty * $unitPrice;
        $obj->executequery("UPDATE tbl_cart SET quantity = $newQty, totalamount = $newTotal WHERE cart_id = $cartid");
    }
}

header("Location: cartview.php");
exit();
?>
