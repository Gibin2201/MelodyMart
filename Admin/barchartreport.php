<?php
include("../dboperation.php");
include("header.php");

$obj = new dboperation();

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
$totalAllMonths = 0;

if ($res) {
    while ($row = mysqli_fetch_array($res)) {
        $months[] = $row['month_name'];
        $val = (float)$row['total_payment'];
        $payments[] = $val;
        $totalAllMonths += $val;
    }
}
?>

<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Reports &amp; Analytics</h4>
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="card-title">Monthly Commission Revenue Report</h4>
                        <p class="card-subtitle">Visual breakdown of platform commission earnings by month</p>
                    </div>
                    <div class="badge badge-warning p-2 text-dark font-weight-bold" style="background: var(--admin-gold); font-size: 13px;">
                        Total: ₹<?php echo number_format($totalAllMonths, 2); ?>
                    </div>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 350px;" class="mb-4">
                        <canvas id="paymentChart"></canvas>
                    </div>

                    <!-- Monthly Breakdown Table -->
                    <h5 class="font-weight-bold mt-4 mb-3 text-dark" style="font-family: var(--admin-font-heading);">
                        <i class="fa-solid fa-table-list mr-1 text-warning"></i> Month-by-Month Summary
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Commission Earned</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($months)): ?>
                                    <?php for ($k = 0; $k < count($months); $k++): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($months[$k]); ?></strong></td>
                                            <td class="font-weight-bold text-dark">₹<?php echo number_format($payments[$k], 2); ?></td>
                                            <td><span class="badge badge-success px-2 py-1"><i class="fa-solid fa-check mr-1"></i> Paid Orders</span></td>
                                        </tr>
                                    <?php endfor; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No payment records found.</td>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('paymentChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Commission (INR)',
                data: <?php echo json_encode($payments); ?>,
                backgroundColor: 'rgba(215, 173, 105, 0.8)',
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
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    ticks: { font: { family: "'DM Sans', sans-serif" } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: "'DM Sans', sans-serif" } }
                }
            },
            plugins: {
                legend: {
                    labels: { font: { family: "'DM Sans', sans-serif", weight: '600' } }
                }
            }
        }
    });
</script>

<?php include("footer.php"); ?>
