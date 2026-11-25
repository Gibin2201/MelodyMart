<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("../dboperation.php");
$obj = new dboperation();

// Handle Quick Stock Update
if (isset($_POST['action']) && $_POST['action'] === 'quick_stock') {
    $pid     = (int)$_POST['product_id'];
    $stock   = max(0, (int)$_POST['stock']);
    $pstatus = isset($_POST['pstatus']) ? trim($_POST['pstatus']) : ($stock > 0 ? 'available' : 'Unavailable');

    // Auto-align status if stock is 0
    if ($stock <= 0) {
        $pstatus = 'Unavailable';
    }

    $sql = "UPDATE tbl_product 
            SET stock = '$stock', pstatus = '$pstatus' 
            WHERE product_id = '$pid'";
    $res = $obj->executequery($sql);

    if ($res) {
        echo "<script>alert('Stock updated successfully!'); window.location='productview.php';</script>";
    } else {
        echo "<script>alert('Failed to update stock.'); window.location='productview.php';</script>";
    }
    exit();
}

// Handle Full Product Details Update
if (isset($_POST['submit_edit']) || (isset($_POST['action']) && $_POST['action'] === 'edit_product')) {
    $pid         = (int)$_POST['product_id'];
    $productname = mysqli_real_escape_string($obj->con, trim($_POST['product_name']));
    $subcatid    = (int)$_POST['subcategory_id'];
    $amount      = (float)$_POST['amount'];
    $stock       = max(0, (int)$_POST['stock']);
    $brand       = !empty($_POST['brand']) ? mysqli_real_escape_string($obj->con, trim($_POST['brand'])) : 'MelodyMart';
    $description = !empty($_POST['description']) ? mysqli_real_escape_string($obj->con, trim($_POST['description'])) : '';
    $pstatus     = isset($_POST['pstatus']) ? trim($_POST['pstatus']) : ($stock > 0 ? 'available' : 'Unavailable');

    if ($stock <= 0) {
        $pstatus = 'Unavailable';
    }

    // Check for optional new image upload
    $imageUpdateClause = "";
    if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
        $filename = time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($_FILES['image']['name']));
        $target = "../uploads/" . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $imageUpdateClause = ", image = '$filename'";
        }
    }

    $sql = "UPDATE tbl_product 
            SET product_name = '$productname',
                subcat = '$subcatid',
                amount = '$amount',
                stock = '$stock',
                pstatus = '$pstatus',
                brand = '$brand',
                description = '$description'
                $imageUpdateClause 
            WHERE product_id = '$pid'";

    $res = $obj->executequery($sql);

    if ($res) {
        echo "<script>alert('Product details updated successfully!'); window.location='productview.php';</script>";
    } else {
        echo "<script>alert('Failed to update product details.'); window.location='productview.php';</script>";
    }
    exit();
}

header("Location: productview.php");
exit();
?>
