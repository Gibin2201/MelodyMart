<?php
include("../dboperation.php");
$obj = new dboperation();

if (isset($_GET["pid"])) {
    $pid = (int)$_GET["pid"];
    $sql = "DELETE FROM tbl_product WHERE product_id = $pid";
    $res = $obj->executequery($sql);
    echo "<script>alert('Product Deleted Successfully!'); window.location='productview.php';</script>";
} else {
    header("Location: productview.php");
    exit();
}
?>
