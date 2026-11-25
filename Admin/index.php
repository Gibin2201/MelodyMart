<?php
include("../dboperation.php");
include("header.php");

$obj = new dboperation();

// Fetch summary metrics
$countCategories = 0;
$countProducts = 0;
$countCustomers = 0;

try {
    $r1 = $obj->executequery("SELECT COUNT(*) FROM tbl_category");
    if ($r1 && $row = mysqli_fetch_row($r1)) $countCategories = (int)$row[0];
    
    $r2 = $obj->executequery("SELECT COUNT(*) FROM tbl_product");
    if ($r2 && $row = mysqli_fetch_row($r2)) $countProducts = (int)$row[0];
    
    $r4 = $obj->executequery("SELECT COUNT(*) FROM tbl_customer");
    if ($r4 && $row = mysqli_fetch_row($r4)) $countCustomers = (int)$row[0];

    $rOrders = $obj->executequery("SELECT COUNT(*), COALESCE(SUM(grandtotal), 0) FROM tbl_cartmaster WHERE cmstatus='Paid' OR cmstatus='paid'");
    if ($rOrders && $row = mysqli_fetch_row($rOrders)) {
        $countOrders = (int)$row[0];
        $totalRevenue = (float)$row[1];
    }

    $rWar = $obj->executequery("SELECT COUNT(*) FROM tbl_warranty WHERE wstatus='requested'");
    if ($rWar && $row = mysqli_fetch_row($rWar)) {
        $countWarranty = (int)$row[0];
    }
} catch (\Throwable $e) {
    // Graceful fallback
}

// Query: Monthly Payment Total
$sql = "SELECT 
            MONTH(payment_date) AS month_no,
            DATE_FORMAT(payment_date, '%M') AS month_name,
            SUM(commission) AS total_payment
        FROM tbl_payment
        WHERE pstatus = 'Paid'
        GROUP BY month_no, month_name
        ORDER BY month_no";

$res = $obj->executequery($sql);

$months = [];
$payments = [];

if ($res) {
    while ($row = mysqli_fetch_array($res)) {
        $months[] = $row['month_name'];
        $payments[] = (float)$row['total_payment'];
    }
}
?>

<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <!-- Welcome Hero Banner -->
            <div class="admin-welcome-hero">
                <h2>Welcome to <span>MelodyMart</span> Control Center</h2>
                <p>
                    Manage your premium musical instrument catalog, fulfill customer orders, review analytics, and monitor warranties seamlessly.
                </p>
            </div>

            <!-- Key Metric Stat Cards -->
            <div class="row">
                <div class="col-sm-6 col-lg-3">
                    <a href="ordersview.php" class="text-decoration-none">
                        <div class="stat-card">
                            <div class="stat-content">
                                <h3>₹<?php echo number_format($totalRevenue, 0); ?></h3>
                                <p>Total Revenue</p>
                            </div>
                            <div class="stat-icon">
                                <i class="fa-solid fa-indian-rupee-sign text-success"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a href="ordersview.php" class="text-decoration-none">
                        <div class="stat-card">
                            <div class="stat-content">
                                <h3><?php echo $countOrders; ?></h3>
                                <p>Customer Orders</p>
                            </div>
                            <div class="stat-icon">
                                <i class="fa-solid fa-bag-shopping text-warning"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a href="productview.php" class="text-decoration-none">
                        <div class="stat-card">
                            <div class="stat-content">
                                <h3><?php echo $countProducts; ?></h3>
                                <p>Instruments</p>
                            </div>
                            <div class="stat-icon">
                                <i class="fa-solid fa-guitar text-primary"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a href="warrantyview.php" class="text-decoration-none">
                        <div class="stat-card">
                            <div class="stat-content">
                                <h3><?php echo $countWarranty; ?></h3>
                                <p>Warranty Pending</p>
                            </div>
                            <div class="stat-icon">
                                <i class="fa-solid fa-shield-halved text-danger"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Revenue Chart and Quick Actions Row -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Monthly Commission Revenue</h4>
                            <p class="card-subtitle">Commission earnings from paid orders across all months</p>
                        </div>
                        <div class="card-body">
                            <div style="position: relative; height: 320px;">
                                <canvas id="paymentChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Quick Actions</h4>
                            <p class="card-subtitle">Frequently used administrative tasks</p>
                        </div>
                        <div class="card-body p-3">
                            <div class="list-group list-group-flush">
                                <a href="ordersview.php" class="list-group-item list-group-item-action d-flex align-items-center py-3 border-bottom">
                                    <div class="mr-3 text-warning" style="font-size: 20px; width: 28px;">
                                        <i class="fa-solid fa-box-archive"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-dark">Customer Orders (<?php echo $countOrders; ?>)</strong>
                                        <small class="text-muted">Inspect shipping addresses &amp; dispatch items</small>
                                    </div>
                                </a>
                                <a href="warrantyview.php" class="list-group-item list-group-item-action d-flex align-items-center py-3 border-bottom">
                                    <div class="mr-3 text-danger" style="font-size: 20px; width: 28px;">
                                        <i class="fa-solid fa-shield-halved"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-dark">Warranty Claims <?php if ($countWarranty > 0): ?><span class="badge badge-warning text-dark ml-1"><?php echo $countWarranty; ?> new</span><?php endif; ?></strong>
                                        <small class="text-muted">Verify defect photos and schedule service</small>
                                    </div>
                                </a>
                                <a href="product.php" class="list-group-item list-group-item-action d-flex align-items-center py-3 border-bottom">
                                    <div class="mr-3 text-primary" style="font-size: 20px; width: 28px;">
                                        <i class="fa-solid fa-plus-circle"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-dark">Add New Instrument</strong>
                                        <small class="text-muted">Register instrument with specifications</small>
                                    </div>
                                </a>
                                <a href="datewisereport.php" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                                    <div class="mr-3 text-info" style="font-size: 20px; width: 28px;">
                                        <i class="fa-solid fa-chart-column"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-dark">Sales &amp; Analytics Reports</strong>
                                        <small class="text-muted">View category distribution and monthly trends</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('paymentChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Commission (INR / $)',
                data: <?php echo json_encode($payments); ?>,
                backgroundColor: 'rgba(215, 173, 105, 0.75)',
                borderColor: '#b88f4b',
                borderWidth: 2,
                borderRadius: 6,
                hoverBackgroundColor: 'rgba(215, 173, 105, 1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: { family: "'DM Sans', sans-serif" }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: "'DM Sans', sans-serif" }
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        font: { family: "'DM Sans', sans-serif", weight: '600' }
                    }
                }
            }
        }
    });
</script>

<?php include("footer.php"); ?>
