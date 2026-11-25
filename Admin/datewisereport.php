<?php
include("../dboperation.php");
include("header.php");

$obj = new dboperation();

$orders = [];
$from = '';
$to = '';
$totalFilteredRevenue = 0;

if (isset($_POST['filter'])) {
    $from = $_POST['fromdate'];
    $to   = $_POST['todate'];

    // Query: filter customer orders between selected dates
    $sql = "SELECT cm.*, 
                   p.payment_date, p.pamount, p.pstatus,
                   cus.customer_name, cus.phone_no, cus.email,
                   da.personname, da.address, da.pincode
            FROM tbl_cartmaster cm
            INNER JOIN tbl_payment p ON p.cartmaster_id = cm.cartmaster_id
            INNER JOIN tbl_customer cus ON cus.customer_id = cm.customer_id
            LEFT JOIN tbl_deliveryaddress da ON da.cartmasterid = cm.cartmaster_id
            WHERE (cm.cmstatus = 'Paid' OR cm.cmstatus = 'paid')
              AND p.payment_date BETWEEN '$from' AND '$to'
            ORDER BY p.payment_date DESC, cm.cartmaster_id DESC";

    $res = $obj->executequery($sql);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $orders[] = $row;
            $totalFilteredRevenue += (float)$row['grandtotal'];
        }
    }
}
?>

<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Reports &amp; Analytics</h4>
            
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Customer Sales &amp; Orders by Date</h4>
                    <p class="card-subtitle">Filter verified customer purchases by payment date range and export reports to Excel</p>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="POST" class="row align-items-end mb-4">
                        <div class="col-md-4 col-lg-3 form-group mb-md-0">
                            <label class="font-weight-bold">From Date</label>
                            <input type="date" name="fromdate" class="form-control" value="<?php echo htmlspecialchars($from); ?>" required>
                        </div>
                        <div class="col-md-4 col-lg-3 form-group mb-md-0">
                            <label class="font-weight-bold">To Date</label>
                            <input type="date" name="todate" class="form-control" value="<?php echo htmlspecialchars($to); ?>" required>
                        </div>
                        <div class="col-md-4 col-lg-3 form-group mb-md-0">
                            <button type="submit" name="filter" class="btn btn-success w-100">
                                <i class="fa-solid fa-filter mr-1"></i> Filter Orders
                            </button>
                        </div>
                        <?php if (!empty($orders)): ?>
                            <div class="col-md-12 col-lg-3 mt-3 mt-lg-0 text-lg-right">
                                <form method="POST" action="exportexcel.php" class="d-inline">
                                    <input type="hidden" name="fromdate" value="<?php echo htmlspecialchars($from); ?>">
                                    <input type="hidden" name="todate" value="<?php echo htmlspecialchars($to); ?>">
                                    <button type="submit" class="btn btn-outline-secondary">
                                        <i class="fa-solid fa-file-excel mr-1 text-success"></i> Export to Excel
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </form>

                    <!-- Results Table -->
                    <?php if (!empty($orders)): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                            <span class="text-muted font-weight-bold">
                                Showing <strong><?php echo count($orders); ?></strong> verified order(s) between <strong><?php echo htmlspecialchars($from); ?></strong> and <strong><?php echo htmlspecialchars($to); ?></strong>
                            </span>
                            <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 13px;">
                                Total Period Revenue: ₹<?php echo number_format($totalFilteredRevenue, 2); ?>
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Contact</th>
                                        <th>Delivery Address</th>
                                        <th>Payment Date</th>
                                        <th class="text-right">Grand Total</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $ord): 
                                        $orderRef = "#MM-ORD-" . str_pad($ord['cartmaster_id'], 5, '0', STR_PAD_LEFT);
                                        $addr = !empty($ord['address']) ? $ord['address'] : "Primary Address";
                                    ?>
                                        <tr>
                                            <td>
                                                <strong class="text-dark d-block"><?php echo $orderRef; ?></strong>
                                                <small class="text-muted">Master #<?php echo (int)$ord['cartmaster_id']; ?></small>
                                            </td>
                                            <td>
                                                <strong class="text-dark d-block"><?php echo htmlspecialchars($ord['customer_name']); ?></strong>
                                                <?php if (!empty($ord['email'])): ?>
                                                    <small class="text-muted"><?php echo htmlspecialchars($ord['email']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($ord['phone_no']); ?></td>
                                            <td style="max-width: 220px;" class="text-truncate" title="<?php echo htmlspecialchars($addr); ?>">
                                                <?php echo htmlspecialchars($addr); ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-light border text-muted">
                                                    <i class="fa-regular fa-calendar mr-1 text-success"></i> <?php echo htmlspecialchars($ord['payment_date']); ?>
                                                </span>
                                            </td>
                                            <td class="text-right font-weight-bold text-success">
                                                ₹<?php echo number_format((float)$ord['grandtotal'], 2); ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-success px-2 py-1 font-weight-bold" style="background: #2e7d32;">
                                                    <i class="fa-solid fa-check mr-1"></i> Paid
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="orderdetail.php?order_id=<?php echo (int)$ord['cartmaster_id']; ?>" class="btn btn-sm btn-outline-dark font-weight-bold">
                                                    <i class="fa-solid fa-eye text-warning mr-1"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php elseif (isset($_POST['filter'])): ?>
                        <div class="alert alert-warning text-center mt-4">
                            <i class="fa-solid fa-circle-exclamation mr-1"></i> No customer orders found between <strong><?php echo htmlspecialchars($from); ?></strong> and <strong><?php echo htmlspecialchars($to); ?></strong>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>
