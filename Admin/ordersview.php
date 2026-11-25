<?php 
include("header.php"); 
include_once("../dboperation.php");
$obj = new dboperation(); 

// Fetch summary metrics
$totalRevenue = 0;
$totalOrders = 0;
$totalItemsSold = 0;

$statRes = $obj->executequery("SELECT 
    COUNT(DISTINCT cm.cartmaster_id) as total_orders,
    COALESCE(SUM(cm.grandtotal), 0) as total_revenue
    FROM tbl_cartmaster cm 
    WHERE cm.cmstatus = 'Paid' OR cm.cmstatus = 'paid'");
if ($statRes && $row = mysqli_fetch_assoc($statRes)) {
    $totalOrders = (int)$row['total_orders'];
    $totalRevenue = (float)$row['total_revenue'];
}

$itemsRes = $obj->executequery("SELECT COALESCE(SUM(quantity), 0) as total_qty FROM tbl_cart WHERE status = 'paid'");
if ($itemsRes && $row = mysqli_fetch_assoc($itemsRes)) {
    $totalItemsSold = (int)$row['total_qty'];
}

$avgOrderVal = ($totalOrders > 0) ? ($totalRevenue / $totalOrders) : 0;

// Main Orders Query
$sql = "SELECT 
    cm.cartmaster_id,
    cm.grandtotal,
    cm.cmstatus,
    MAX(p.payment_date) as payment_date,
    MAX(p.pamount) as pamount,
    MAX(p.pstatus) as pstatus,
    MAX(cus.customer_name) as customer_name,
    MAX(cus.phone_no) as phone_no,
    MAX(cus.email) as email,
    MAX(da.personname) as shipping_recipient,
    MAX(da.mobno) as shipping_phone,
    MAX(da.address) as shipping_address,
    MAX(da.pincode) as shipping_pincode,
    COUNT(c.cart_id) as total_items,
    COALESCE(SUM(c.quantity), 0) as total_quantity
FROM tbl_cartmaster cm
LEFT JOIN tbl_payment p ON p.cartmaster_id = cm.cartmaster_id
LEFT JOIN tbl_customer cus ON cus.customer_id = cm.customer_id
LEFT JOIN tbl_deliveryaddress da ON da.cartmasterid = cm.cartmaster_id
LEFT JOIN tbl_cart c ON c.cartmaster_id = cm.cartmaster_id
GROUP BY cm.cartmaster_id, cm.grandtotal, cm.cmstatus
ORDER BY cm.cartmaster_id DESC";

$ordersResult = $obj->executequery($sql);
?> 

<div class="main-panel"> 
    <div class="content"> 
        <div class="container-fluid"> 
            
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <div>
                    <h4 class="page-title mb-1">Customer Orders &amp; Shipments</h4>
                    <p class="text-muted small mb-0">Track completed customer purchases, delivery addresses, and fulfillment</p>
                </div>
                <div class="mt-2 mt-sm-0">
                    <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 13px;">
                        <i class="fa-solid fa-circle-check mr-1"></i> Store Gateway Active
                    </span>
                </div>
            </div>

            <!-- Key Metric Stat Cards -->
            <div class="row mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-content">
                            <h3>₹<?php echo number_format($totalRevenue, 2); ?></h3>
                            <p>Total Revenue</p>
                        </div>
                        <div class="stat-icon">
                            <i class="fa-solid fa-indian-rupee-sign text-success"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-content">
                            <h3><?php echo $totalOrders; ?></h3>
                            <p>Orders Placed</p>
                        </div>
                        <div class="stat-icon">
                            <i class="fa-solid fa-bag-shopping text-warning"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-content">
                            <h3><?php echo $totalItemsSold; ?></h3>
                            <p>Instruments Sold</p>
                        </div>
                        <div class="stat-icon">
                            <i class="fa-solid fa-guitar text-primary"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-content">
                            <h3>₹<?php echo number_format($avgOrderVal, 2); ?></h3>
                            <p>Avg. Order Value</p>
                        </div>
                        <div class="stat-icon">
                            <i class="fa-solid fa-chart-line text-info"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table Card -->
            <div class="card"> 
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap"> 
                    <div>
                        <h4 class="card-title">Completed Purchases</h4> 
                        <p class="card-subtitle">Verified online customer checkouts and delivery details</p> 
                    </div>
                    <!-- Live Search Box -->
                    <div class="mt-2 mt-md-0" style="min-width: 250px;">
                        <input type="text" id="orderSearch" class="form-control form-control-sm" placeholder="🔍 Search Order #, Name, Phone..." onkeyup="filterOrdersTable()">
                    </div>
                </div> 

                <div class="card-body p-0"> 
                    <div class="table-responsive"> 
                        <table class="table table-hover mb-0" id="ordersTable"> 
                            <thead class="bg-light text-muted" style="font-size: 12px; text-transform: uppercase;"> 
                                <tr> 
                                    <th class="py-3 px-4">Order ID</th> 
                                    <th class="py-3">Customer Info</th> 
                                    <th class="py-3">Delivery Destination</th> 
                                    <th class="py-3">Order Date</th> 
                                    <th class="py-3 text-center">Items</th> 
                                    <th class="py-3 text-right">Grand Total</th> 
                                    <th class="py-3 text-center">Status</th> 
                                    <th class="py-3 text-center px-4">Action</th> 
                                </tr> 
                            </thead> 
                            <tbody> 
                                <?php if ($ordersResult && mysqli_num_rows($ordersResult) > 0): ?>
                                    <?php while ($r = mysqli_fetch_assoc($ordersResult)): 
                                        $orderRef = "#MM-ORD-" . str_pad($r['cartmaster_id'], 5, '0', STR_PAD_LEFT);
                                        $customerDisplay = !empty($r['customer_name']) ? $r['customer_name'] : "Registered Customer";
                                        $recipient = !empty($r['shipping_recipient']) ? $r['shipping_recipient'] : $customerDisplay;
                                        $address = !empty($r['shipping_address']) ? $r['shipping_address'] : "Primary Address on file";
                                        $pincode = !empty($r['shipping_pincode']) ? " - " . $r['shipping_pincode'] : "";
                                        $date = !empty($r['payment_date']) ? $r['payment_date'] : "Recorded";
                                    ?> 
                                        <tr class="order-table-row"> 
                                            <!-- Order ID -->
                                            <td class="py-3 px-4 align-middle"> 
                                                <strong class="text-dark font-weight-bold order-ref-text"><?php echo $orderRef; ?></strong>
                                                <div class="small text-muted">ID: #<?php echo (int)$r['cartmaster_id']; ?></div>
                                            </td> 

                                            <!-- Customer Info -->
                                            <td class="py-3 align-middle"> 
                                                <div class="font-weight-bold text-dark customer-name-text"><?php echo htmlspecialchars($customerDisplay); ?></div> 
                                                <?php if (!empty($r['phone_no'])): ?>
                                                    <span class="small text-muted d-block"><i class="fa-solid fa-phone mr-1" style="font-size: 10px;"></i><?php echo htmlspecialchars($r['phone_no']); ?></span>
                                                <?php endif; ?>
                                                <?php if (!empty($r['email'])): ?>
                                                    <span class="small text-muted d-block"><i class="fa-regular fa-envelope mr-1" style="font-size: 10px;"></i><?php echo htmlspecialchars($r['email']); ?></span>
                                                <?php endif; ?>
                                            </td> 

                                            <!-- Delivery Destination -->
                                            <td class="py-3 align-middle" style="max-width: 260px;"> 
                                                <div class="small font-weight-bold text-dark">
                                                    <i class="fa-solid fa-location-dot text-danger mr-1"></i><?php echo htmlspecialchars($recipient); ?>
                                                </div>
                                                <div class="small text-muted text-truncate" title="<?php echo htmlspecialchars($address . $pincode); ?>">
                                                    <?php echo htmlspecialchars($address . $pincode); ?>
                                                </div>
                                            </td> 

                                            <!-- Order Date -->
                                            <td class="py-3 align-middle text-muted small" style="white-space: nowrap;"> 
                                                <i class="fa-regular fa-calendar-check mr-1 text-success"></i>
                                                <?php echo htmlspecialchars($date); ?>
                                            </td> 

                                            <!-- Total Quantity / Items -->
                                            <td class="py-3 text-center align-middle"> 
                                                <span class="badge badge-light border font-weight-bold px-2 py-1">
                                                    <?php echo (int)$r['total_quantity']; ?> instrument(s)
                                                </span>
                                            </td> 

                                            <!-- Grand Total -->
                                            <td class="py-3 text-right align-middle"> 
                                                <strong class="text-success font-weight-bold" style="font-size: 15px;">
                                                    ₹<?php echo number_format((float)$r['grandtotal'], 2); ?>
                                                </strong>
                                            </td> 

                                            <!-- Payment Status -->
                                            <td class="py-3 text-center align-middle"> 
                                                <span class="badge badge-success px-2 py-1 font-weight-bold" style="background: #2e7d32;">
                                                    <i class="fa-solid fa-check mr-1"></i> <?php echo htmlspecialchars(ucfirst($r['cmstatus'] ?: 'Paid')); ?>
                                                </span>
                                            </td> 

                                            <!-- Action -->
                                            <td class="py-3 text-center px-4 align-middle" style="white-space: nowrap;"> 
                                                <a href="orderdetail.php?order_id=<?php echo (int)$r['cartmaster_id']; ?>" class="btn btn-sm btn-outline-dark font-weight-bold" style="border-radius: 6px;">
                                                    <i class="fa-solid fa-eye mr-1 text-warning"></i> Details
                                                </a> 
                                            </td> 
                                        </tr> 
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr> 
                                        <td colspan="8" class="text-center py-5 text-muted"> 
                                            <div class="mb-2" style="font-size: 28px; color: #ccc;"><i class="fa-solid fa-box-open"></i></div>
                                            No completed customer orders recorded yet.
                                        </td> 
                                    </tr> 
                                <?php endif; ?>
                            </tbody> 
                        </table> 
                    </div> 
                </div> 
            </div> 

        </div> 
    </div> 
</div> 

<script>
function filterOrdersTable() {
    var input = document.getElementById("orderSearch").value.toLowerCase();
    var rows = document.querySelectorAll("#ordersTable tbody tr.order-table-row");
    
    rows.forEach(function(row) {
        var text = row.innerText.toLowerCase();
        if (text.indexOf(input) > -1) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}
</script>

<?php include("footer.php"); ?>
