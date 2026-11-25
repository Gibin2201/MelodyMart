<?php
if (!headers_sent()) {
    header('Content-Type: application/json');
}
require_once(__DIR__ . "/../dboperation.php");
$obj = new dboperation();

$username = isset($_POST['username']) ? mysqli_real_escape_string($obj->con, trim($_POST['username'])) : '';

if (empty($username)) {
    echo json_encode(['status' => 'empty', 'message' => '']);
    exit();
}

// Check in tbl_customer
$query = "SELECT customer_id FROM tbl_customer WHERE username='$username'";
$result = $obj->executequery($query);

if ($result && mysqli_num_rows($result) > 0) {
    echo json_encode(['status' => 'taken', 'message' => 'Username is already taken']);
} else {
    echo json_encode(['status' => 'available', 'message' => 'Username is available']);
}
?>
