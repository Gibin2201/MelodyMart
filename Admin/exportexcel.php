<?php
include("../dboperation.php");
$obj = new dboperation();

if (isset($_POST['fromdate']) && isset($_POST['todate'])) {
    $from = $_POST['fromdate'];
    $to   = $_POST['todate'];

    $sql = "SELECT cm.cartmaster_id, cm.grandtotal, cm.cmstatus,
                   p.payment_date,
                   cus.customer_name, cus.phone_no, cus.email,
                   da.address, da.pincode
            FROM tbl_cartmaster cm
            INNER JOIN tbl_payment p ON p.cartmaster_id = cm.cartmaster_id
            INNER JOIN tbl_customer cus ON cus.customer_id = cm.customer_id
            LEFT JOIN tbl_deliveryaddress da ON da.cartmasterid = cm.cartmaster_id
            WHERE (cm.cmstatus = 'Paid' OR cm.cmstatus = 'paid')
              AND p.payment_date BETWEEN '$from' AND '$to'
            ORDER BY p.payment_date DESC, cm.cartmaster_id DESC";

    $res = $obj->executequery($sql);

    // Force download as Excel
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=MelodyMart_Sales_Report_" . date('Y-m-d') . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "<table border='1'>";
    echo "<tr>
            <th>Order Ref</th>
            <th>Customer Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Payment Date</th>
            <th>Total Amount (INR)</th>
            <th>Delivery Address</th>
            <th>Status</th>
          </tr>";

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $orderRef = "#MM-ORD-" . str_pad($row['cartmaster_id'], 5, '0', STR_PAD_LEFT);
            $fullAddr = !empty($row['address']) ? $row['address'] . ' - ' . $row['pincode'] : 'On File';
            echo "<tr>
                    <td>{$orderRef}</td>
                    <td>" . htmlspecialchars($row['customer_name']) . "</td>
                    <td>" . htmlspecialchars($row['phone_no']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td>{$row['payment_date']}</td>
                    <td>{$row['grandtotal']}</td>
                    <td>" . htmlspecialchars($fullAddr) . "</td>
                    <td>Paid</td>
                  </tr>";
        }
    }
    echo "</table>";
    exit;
}
?>