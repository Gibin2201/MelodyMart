<?php
include("../dboperation.php");
include("header.php");

$obj = new dboperation();

// Query: Category-wise product sale count (using tbl_subcategory)
$sql = "SELECT c.category_name, COUNT(ct.product_id) AS sale_count
        FROM tbl_cart ct
        INNER JOIN tbl_product p ON ct.product_id = p.product_id
        INNER JOIN tbl_subcategory s ON p.subcat = s.subcategory_id
        INNER JOIN tbl_category c ON s.category_id = c.category_id
        WHERE ct.status = 'paid'
        GROUP BY c.category_id, c.category_name";

$res = $obj->executequery($sql);

$categories = [];
$sales = [];
$totalSales = 0;

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $categories[] = $row['category_name'];
        $val = (int)$row['sale_count'];
        $sales[] = $val;
        $totalSales += $val;
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
                        <h4 class="card-title">Category Sales Distribution</h4>
                        <p class="card-subtitle">Breakdown of completed product orders categorized by musical department</p>
                    </div>
                    <div class="badge badge-warning p-2 text-dark font-weight-bold" style="background: var(--admin-gold); font-size: 13px;">
                        Total Units Sold: <?php echo $totalSales; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-6 mb-4 mb-lg-0 text-center">
                            <div style="position: relative; max-width: 420px; margin: 0 auto; min-height: 320px;">
                                <canvas id="categorySalesPieChart"></canvas>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h5 class="font-weight-bold mb-3 text-dark" style="font-family: var(--admin-font-heading);">
                                <i class="fa-solid fa-chart-pie mr-1 text-warning"></i> Category Performance Breakdown
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Orders Sold</th>
                                            <th>Share %</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($categories)): ?>
                                            <?php for ($k = 0; $k < count($categories); $k++): ?>
                                                <?php $pct = $totalSales > 0 ? round(($sales[$k] / $totalSales) * 100, 1) : 0; ?>
                                                <tr>
                                                    <td><strong><?php echo htmlspecialchars($categories[$k]); ?></strong></td>
                                                    <td class="font-weight-bold"><?php echo $sales[$k]; ?> units</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="mr-2"><?php echo $pct; ?>%</span>
                                                            <div class="progress flex-grow-1" style="height: 6px; max-width: 100px;">
                                                                <div class="progress-bar" style="width: <?php echo $pct; ?>%; background: var(--admin-gold);"></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endfor; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-muted">No sales data available.</td>
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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxPie = document.getElementById('categorySalesPieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($categories); ?>,
            datasets: [{
                data: <?php echo json_encode($sales); ?>,
                backgroundColor: [
                    'rgba(215, 173, 105, 0.9)',
                    'rgba(18, 20, 17, 0.85)',
                    'rgba(239, 207, 150, 0.9)',
                    'rgba(119, 121, 111, 0.85)',
                    'rgba(184, 143, 75, 0.9)'
                ],
                borderColor: '#ffffff',
                borderWidth: 2,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { family: "'DM Sans', sans-serif", weight: '600' },
                        padding: 16
                    }
                }
            }
        }
    });
</script>

<?php include("footer.php"); ?>
