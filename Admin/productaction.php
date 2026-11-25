<?php
include_once("../dboperation.php");
$obj = new dboperation();

if (isset($_POST['submit'])) {
    $productname = addslashes(trim($_POST['productname']));
    $subcatid    = (int)$_POST['subcategory_id'];
    $amount      = (float)$_POST['amount'];
    $stock       = (int)$_POST['stock'];
    $brand       = !empty($_POST['brand']) ? addslashes(trim($_POST['brand'])) : 'MelodyMart';
    $description = !empty($_POST['description']) ? addslashes(trim($_POST['description'])) : '';
    $pstatus     = 'available';

    $photo = '';
    if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
        $photo = basename($_FILES['image']['name']);
        move_uploaded_file($_FILES["image"]["tmp_name"], "../uploads/" . $photo);
    }

    // Check duplicate product
    $sqlquery = "SELECT * FROM tbl_product WHERE product_name = '$productname' AND subcat = '$subcatid'";
    $result   = $obj->executequery($sqlquery);
    $rows     = ($result) ? mysqli_num_rows($result) : 0;

    if ($rows > 0) {
        echo "<script>alert('Product already exists in this subcategory!'); window.location='product.php';</script>";
    } else {
        $sqlquery1 = "INSERT INTO tbl_product (product_name, subcat, amount, image, stock, pstatus, brand, description) 
                      VALUES ('$productname', '$subcatid', '$amount', '$photo', '$stock', '$pstatus', '$brand', '$description')";
        $result1 = $obj->executequery($sqlquery1);
        if ($result1) {
            echo "<script>alert('Product Added Successfully!'); window.location='product.php';</script>";
        } else {
            echo "<script>alert('Product Registration Failed!'); window.location='product.php';</script>";
        }
    }
} else {
    header("Location: product.php");
    exit();
}
?>