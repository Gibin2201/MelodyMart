<?php
include_once("../dboperation.php");
$obj = new dboperation();

if (isset($_GET["cartid"])) {
    $lid = (int)$_GET["cartid"];
    $sql = "DELETE FROM tbl_cart WHERE cart_id = $lid";
    $obj->executequery($sql);
}

header("Location: cartview.php");
exit();
?>