<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("../dboperation.php");
$obj = new dboperation();

// Handle Accept Claim
if (isset($_POST['action']) && $_POST['action'] === 'accept') {
    $warrantyId = (int)$_POST['warranty_id'];
    $responseDate = !empty($_POST['responsedate']) ? mysqli_real_escape_string($obj->con, $_POST['responsedate']) : date('Y-m-d', strtotime('+7 days'));
    
    $sql = "UPDATE tbl_warranty 
            SET wstatus = 'accepted', responsedate = '$responseDate' 
            WHERE warranty_id = '$warrantyId'";
    $res = $obj->executequery($sql);

    if ($res) {
        echo "<script>alert('Warranty claim #$warrantyId has been APPROVED and scheduled for $responseDate.'); window.location='warrantyview.php';</script>";
    } else {
        echo "<script>alert('Failed to update warranty claim.'); window.location='warrantyview.php';</script>";
    }
    exit();
}

// Handle Reject Claim
if (isset($_GET['action']) && $_GET['action'] === 'reject') {
    $warrantyId = (int)$_GET['warranty_id'];
    
    $sql = "UPDATE tbl_warranty 
            SET wstatus = 'rejected' 
            WHERE warranty_id = '$warrantyId'";
    $res = $obj->executequery($sql);

    if ($res) {
        echo "<script>alert('Warranty claim #$warrantyId has been DECLINED.'); window.location='warrantyview.php';</script>";
    } else {
        echo "<script>alert('Failed to update warranty claim.'); window.location='warrantyview.php';</script>";
    }
    exit();
}

header("Location: warrantyview.php");
exit();
?>
