<?php 
include("header.php"); 
include_once("../dboperation.php");
$obj = new dboperation(); 

// Status filter
$filterStatus = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : 'all';
$whereClause = "";
if ($filterStatus === 'pending') {
    $whereClause = "WHERE w.wstatus = 'requested'";
} elseif ($filterStatus === 'accepted') {
    $whereClause = "WHERE w.wstatus = 'accepted'";
} elseif ($filterStatus === 'rejected') {
    $whereClause = "WHERE w.wstatus = 'rejected'";
}

// KPI metrics
$cntTotal = 0;
$cntPending = 0;
$cntAccepted = 0;
$cntRejected = 0;

$statRes = $obj->executequery("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN wstatus = 'requested' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN wstatus = 'accepted' THEN 1 ELSE 0 END) as accepted,
    SUM(CASE WHEN wstatus = 'rejected' THEN 1 ELSE 0 END) as rejected
FROM tbl_warranty");

if ($statRes && $row = mysqli_fetch_assoc($statRes)) {
    $cntTotal = (int)$row['total'];
    $cntPending = (int)$row['pending'];
    $cntAccepted = (int)$row['accepted'];
    $cntRejected = (int)$row['rejected'];
}

// Query claims
$sql = "SELECT w.*, 
    cus.customer_name, cus.phone_no, cus.email,
    c.cart_date, c.totalamount,
    pro.product_name, pro.image AS product_img,
    sub.name AS subcat_name
FROM tbl_warranty w
LEFT JOIN tbl_customer cus ON cus.customer_id = w.customer_id
LEFT JOIN tbl_cart c ON c.cart_id = w.cart_id
LEFT JOIN tbl_product pro ON pro.product_id = c.product_id
LEFT JOIN tbl_subcategory sub ON sub.subcategory_id = pro.subcat
$whereClause
ORDER BY w.warranty_id DESC";

$claimsRes = $obj->executequery($sql);
?> 

<div class="main-panel"> 
    <div class="content"> 
        <div class="container-fluid"> 
            
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <div>
                    <h4 class="page-title mb-1">Customer Warranty Claims</h4>
                    <p class="text-muted small mb-0">Review inspection evidence, verify purchase receipts, and schedule servicing</p>
                </div>
                <div class="mt-2 mt-sm-0">
                    <?php if ($cntPending > 0): ?>
                        <span class="badge badge-warning px-3 py-2 font-weight-bold text-dark" style="font-size: 13px; background: #ffb300;">
                            <i class="fa-solid fa-bell mr-1"></i> <?php echo $cntPending; ?> Claim(s) Pending Review
                        </span>
                    <?php else: ?>
                        <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 13px;">
                            <i class="fa-solid fa-circle-check mr-1"></i> All Claims Reviewed
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Metric KPI Stat Cards -->
            <div class="row mb-4">
                <div class="col-sm-6 col-lg-3">
                    <a href="warrantyview.php?status=all" class="text-decoration-none">
                        <div class="stat-card <?php echo ($filterStatus === 'all') ? 'border-primary' : ''; ?>">
                            <div class="stat-content">
                                <h3><?php echo $cntTotal; ?></h3>
                                <p>Total Claims</p>
                            </div>
                            <div class="stat-icon">
                                <i class="fa-solid fa-shield-halved text-primary"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a href="warrantyview.php?status=pending" class="text-decoration-none">
                        <div class="stat-card <?php echo ($filterStatus === 'pending') ? 'border-warning' : ''; ?>">
                            <div class="stat-content">
                                <h3><?php echo $cntPending; ?></h3>
                                <p>Pending Review</p>
                            </div>
                            <div class="stat-icon">
                                <i class="fa-solid fa-clock-rotate-left text-warning"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a href="warrantyview.php?status=accepted" class="text-decoration-none">
                        <div class="stat-card <?php echo ($filterStatus === 'accepted') ? 'border-success' : ''; ?>">
                            <div class="stat-content">
                                <h3><?php echo $cntAccepted; ?></h3>
                                <p>Approved &amp; Scheduled</p>
                            </div>
                            <div class="stat-icon">
                                <i class="fa-solid fa-circle-check text-success"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a href="warrantyview.php?status=rejected" class="text-decoration-none">
                        <div class="stat-card <?php echo ($filterStatus === 'rejected') ? 'border-danger' : ''; ?>">
                            <div class="stat-content">
                                <h3><?php echo $cntRejected; ?></h3>
                                <p>Declined Claims</p>
                            </div>
                            <div class="stat-icon">
                                <i class="fa-solid fa-circle-xmark text-danger"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="card-title">Warranty Applications</h4>
                        <p class="card-subtitle">Official damage assessments and customer repair submissions</p>
                    </div>
                    <!-- Status Filter Tabs -->
                    <div class="btn-group mt-2 mt-md-0" role="group">
                        <a href="warrantyview.php?status=all" class="btn btn-sm <?php echo ($filterStatus === 'all') ? 'btn-dark' : 'btn-outline-secondary'; ?>">All (<?php echo $cntTotal; ?>)</a>
                        <a href="warrantyview.php?status=pending" class="btn btn-sm <?php echo ($filterStatus === 'pending') ? 'btn-warning text-dark font-weight-bold' : 'btn-outline-secondary'; ?>">Pending (<?php echo $cntPending; ?>)</a>
                        <a href="warrantyview.php?status=accepted" class="btn btn-sm <?php echo ($filterStatus === 'accepted') ? 'btn-success text-white font-weight-bold' : 'btn-outline-secondary'; ?>">Approved (<?php echo $cntAccepted; ?>)</a>
                        <a href="warrantyview.php?status=rejected" class="btn btn-sm <?php echo ($filterStatus === 'rejected') ? 'btn-danger text-white font-weight-bold' : 'btn-outline-secondary'; ?>">Declined (<?php echo $cntRejected; ?>)</a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light text-muted" style="font-size: 12px; text-transform: uppercase;">
                                <tr>
                                    <th class="py-3 px-4">Claim ID</th>
                                    <th class="py-3">Customer Info</th>
                                    <th class="py-3">Instrument</th>
                                    <th class="py-3 text-center">Warranty Card</th>
                                    <th class="py-3 text-center">Defect Photo</th>
                                    <th class="py-3">Reported Issue</th>
                                    <th class="py-3 text-center">Status</th>
                                    <th class="py-3 text-center px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($claimsRes && mysqli_num_rows($claimsRes) > 0): ?>
                                    <?php while ($r = mysqli_fetch_assoc($claimsRes)): 
                                        $claimRef = "#MM-WAR-" . str_pad($r['warranty_id'], 4, '0', STR_PAD_LEFT);
                                        $wstatus = strtolower($r['wstatus']);
                                        $custName = !empty($r['customer_name']) ? $r['customer_name'] : "Customer #" . $r['customer_id'];
                                        $prodName = !empty($r['product_name']) ? $r['product_name'] : "Instrument (Cart #" . $r['cart_id'] . ")";
                                    ?>
                                        <tr>
                                            <!-- Claim ID & Date -->
                                            <td class="py-3 px-4 align-middle">
                                                <strong class="text-dark font-weight-bold"><?php echo $claimRef; ?></strong>
                                                <div class="small text-muted">Date: <?php echo htmlspecialchars($r['applydate']); ?></div>
                                            </td>

                                            <!-- Customer Details -->
                                            <td class="py-3 align-middle">
                                                <div class="font-weight-bold text-dark"><?php echo htmlspecialchars($custName); ?></div>
                                                <?php if (!empty($r['phone_no'])): ?>
                                                    <span class="small text-muted d-block"><i class="fa-solid fa-phone mr-1" style="font-size: 10px;"></i><?php echo htmlspecialchars($r['phone_no']); ?></span>
                                                <?php endif; ?>
                                                <?php if (!empty($r['email'])): ?>
                                                    <span class="small text-muted d-block"><i class="fa-regular fa-envelope mr-1" style="font-size: 10px;"></i><?php echo htmlspecialchars($r['email']); ?></span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Instrument -->
                                            <td class="py-3 align-middle">
                                                <div class="d-flex align-items-center">
                                                    <img src="../uploads/<?php echo htmlspecialchars($r['product_img']); ?>" 
                                                         alt="<?php echo htmlspecialchars($prodName); ?>" 
                                                         class="rounded border mr-2" 
                                                         style="width: 48px; height: 48px; object-fit: contain; background: #fafafa; padding: 2px;"
                                                         onerror="this.src='../Guest/ins.jpg'">
                                                    <div>
                                                        <strong class="text-dark small d-block"><?php echo htmlspecialchars($prodName); ?></strong>
                                                        <?php if (!empty($r['subcat_name'])): ?>
                                                            <span class="badge badge-light border text-muted" style="font-size: 10px;"><?php echo htmlspecialchars($r['subcat_name']); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Warranty Card Evidence -->
                                            <td class="py-3 text-center align-middle">
                                                <?php if (!empty($r['wimg'])): ?>
                                                    <a href="../uploads/<?php echo htmlspecialchars($r['wimg']); ?>" target="_blank" title="Click to view full image">
                                                        <img src="../uploads/<?php echo htmlspecialchars($r['wimg']); ?>" 
                                                             alt="Warranty Card" 
                                                             class="rounded border shadow-sm" 
                                                             style="width: 55px; height: 55px; object-fit: cover;"
                                                             onerror="this.src='../Guest/ins.jpg'">
                                                    </a>
                                                    <div class="small text-muted mt-1" style="font-size: 10px;">View Card</div>
                                                <?php else: ?>
                                                    <span class="text-muted small">None</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Defect Photo Evidence -->
                                            <td class="py-3 text-center align-middle">
                                                <?php if (!empty($r['prod_img'])): ?>
                                                    <a href="../uploads/<?php echo htmlspecialchars($r['prod_img']); ?>" target="_blank" title="Click to view full image">
                                                        <img src="../uploads/<?php echo htmlspecialchars($r['prod_img']); ?>" 
                                                             alt="Defect Photo" 
                                                             class="rounded border shadow-sm" 
                                                             style="width: 55px; height: 55px; object-fit: cover;"
                                                             onerror="this.src='../Guest/ins.jpg'">
                                                    </a>
                                                    <div class="small text-muted mt-1" style="font-size: 10px;">View Defect</div>
                                                <?php else: ?>
                                                    <span class="text-muted small">None</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Reported Issue -->
                                            <td class="py-3 align-middle" style="max-width: 220px;">
                                                <div class="small text-dark" style="line-height: 1.4;">
                                                    <?php echo htmlspecialchars(mb_strimwidth($r['issuedesc'], 0, 100, '...')); ?>
                                                </div>
                                            </td>

                                            <!-- Status -->
                                            <td class="py-3 text-center align-middle" style="white-space: nowrap;">
                                                <?php if ($wstatus === 'accepted'): ?>
                                                    <span class="badge badge-success px-2 py-1 font-weight-bold" style="background: #2e7d32;">
                                                        <i class="fa-solid fa-check mr-1"></i> Approved
                                                    </span>
                                                    <?php if (!empty($r['responsedate'])): ?>
                                                        <div class="small text-muted mt-1" style="font-size: 11px;">
                                                            Scheduled: <strong><?php echo htmlspecialchars($r['responsedate']); ?></strong>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php elseif ($wstatus === 'rejected'): ?>
                                                    <span class="badge badge-danger px-2 py-1 font-weight-bold" style="background: #c62828;">
                                                        <i class="fa-solid fa-xmark mr-1"></i> Declined
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning px-2 py-1 font-weight-bold text-dark" style="background: #ffb300;">
                                                        <i class="fa-solid fa-clock mr-1"></i> Pending
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Actions -->
                                            <td class="py-3 text-center px-4 align-middle" style="white-space: nowrap;">
                                                <?php if ($wstatus === 'requested'): ?>
                                                    <!-- Accept Button (triggers modal) -->
                                                    <button type="button" class="btn btn-sm btn-success font-weight-bold mr-1" 
                                                            onclick="openAcceptModal(<?php echo (int)$r['warranty_id']; ?>, '<?php echo htmlspecialchars(addslashes($custName)); ?>', '<?php echo htmlspecialchars(addslashes($prodName)); ?>')">
                                                        <i class="fa-solid fa-check mr-1"></i> Approve
                                                    </button>
                                                    
                                                    <!-- Reject Button -->
                                                    <a href="warrantyaction.php?action=reject&warranty_id=<?php echo (int)$r['warranty_id']; ?>" 
                                                       class="btn btn-sm btn-outline-danger font-weight-bold" 
                                                       onclick="return confirm('Are you sure you want to decline Warranty Claim <?php echo $claimRef; ?>?');">
                                                        <i class="fa-solid fa-xmark mr-1"></i> Decline
                                                    </a>
                                                <?php else: ?>
                                                    <span class="small text-muted fst-italic">Completed</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <div class="mb-2" style="font-size: 28px; color: #ccc;"><i class="fa-solid fa-shield-cat"></i></div>
                                            No warranty claims found for the selected filter.
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

<!-- Modal to Accept Warranty Claim and set Scheduled Date -->
<div class="modal fade" id="acceptWarrantyModal" tabindex="-1" role="dialog" aria-labelledby="acceptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-weight-bold" id="acceptModalLabel">
                    <i class="fa-solid fa-circle-check text-success mr-2"></i> Approve Warranty Claim
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="warrantyaction.php" method="post">
                <input type="hidden" name="action" value="accept">
                <input type="hidden" name="warranty_id" id="modalWarrantyId" value="">

                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">
                        Approving claim for <strong id="modalCustName" class="text-dark"></strong> on instrument <strong id="modalProdName" class="text-dark"></strong>.
                    </p>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small text-uppercase">
                            <i class="fa-solid fa-calendar-days text-warning mr-1"></i> Scheduled Inspection / Service Date
                        </label>
                        <input type="date" name="responsedate" class="form-control" value="<?php echo date('Y-m-d', strtotime('+3 days')); ?>" required min="<?php echo date('Y-m-d'); ?>">
                        <small class="text-muted">The customer will see this date in their portal to expect inspection or instrument pickup.</small>
                    </div>

                    <div class="alert alert-light border p-2 small text-muted">
                        <i class="fa-solid fa-info-circle text-primary mr-1"></i>
                        The customer's status will immediately update to <strong>Approved</strong> with this service date.
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success font-weight-bold">
                        <i class="fa-solid fa-check mr-1"></i> Confirm &amp; Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAcceptModal(warrantyId, custName, prodName) {
    document.getElementById('modalWarrantyId').value = warrantyId;
    document.getElementById('modalCustName').innerText = custName;
    document.getElementById('modalProdName').innerText = prodName;
    $('#acceptWarrantyModal').modal('show');
}
</script>

<?php include("footer.php"); ?>
