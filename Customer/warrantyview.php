<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("header.php");
include_once("../dboperation.php");

$obj = new dboperation();

if (!isset($_SESSION['customer_id'])) {
    echo "<script>window.location='../Guest/login.php';</script>";
    exit();
}

$customerid = (int)$_SESSION['customer_id'];

// Fetch customer warranty requests with cart, product, and subcategory joins
$sql = "SELECT w.*, c.cart_date, c.quantity, c.totalamount, p.product_name, p.image AS prod_original_img, sub.name AS subcat_name 
        FROM tbl_warranty w 
        INNER JOIN tbl_cart c ON c.cart_id = w.cart_id 
        INNER JOIN tbl_product p ON p.product_id = c.product_id 
        LEFT JOIN tbl_subcategory sub ON sub.subcategory_id = p.subcat 
        WHERE w.customer_id = '$customerid' 
        ORDER BY w.warranty_id DESC";

$res = $obj->executequery($sql);
$claimsCount = ($res) ? mysqli_num_rows($res) : 0;
?>

<div class="warranty-view-wrapper py-5" style="background: #f7f5ef; min-height: 85vh;">
    <div class="container">

        <!-- Page Header Breadcrumb -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap pb-3 border-bottom" style="border-color: #e8e5dc !important;">
            <div>
                <span class="badge badge-warning px-3 py-1 mb-1 text-dark font-weight-bold" style="background: #d7ad69; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-shield-halved mr-1"></i> WARRANTY SERVICE HUB
                </span>
                <h2 style="font-family: 'Playfair Display', Georgia, serif; font-weight: 700; color: #121411; margin-top: 4px;">
                    My Warranty Claims
                </h2>
                <p class="text-muted small mb-0">Track official inspection updates, scheduling dates, and review decisions for your instruments.</p>
            </div>
            <div class="mt-2 mt-md-0">
                <a href="vieworders.php" class="btn btn-outline-dark font-weight-bold px-3 py-2" style="border-radius: 8px;">
                    <i class="fa-solid fa-box-archive mr-1"></i> View All Orders
                </a>
            </div>
        </div>

        <?php if ($claimsCount > 0): ?>
            <div class="row">
                <?php while ($r = mysqli_fetch_array($res)): 
                    $wstatus = strtolower($r["wstatus"]);
                ?>
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; overflow: hidden; border: 1px solid #e8e5dc !important; background: #ffffff;">
                            
                            <!-- Card Header: Claim ID + Status Badge -->
                            <div class="card-header py-3 px-4 d-flex justify-content-between align-items-center flex-wrap" style="background: #121411; color: #ffffff;">
                                <div>
                                    <span class="badge badge-warning text-dark font-weight-bold mr-2" style="background: #d7ad69;">
                                        #MM-WAR-<?php echo str_pad($r['warranty_id'], 4, '0', STR_PAD_LEFT); ?>
                                    </span>
                                    <span class="small text-muted" style="color: #bbb !important;">
                                        Applied: <?php echo htmlspecialchars($r["applydate"]); ?>
                                    </span>
                                </div>
                                <div class="mt-2 mt-sm-0">
                                    <?php if ($wstatus === 'accepted'): ?>
                                        <span class="badge badge-success px-3 py-1 font-weight-bold" style="background: #2e7d32; font-size: 12px;">
                                            <i class="fa-solid fa-circle-check mr-1"></i> Claim Approved
                                        </span>
                                    <?php elseif ($wstatus === 'rejected'): ?>
                                        <span class="badge badge-danger px-3 py-1 font-weight-bold" style="background: #c62828; font-size: 12px;">
                                            <i class="fa-solid fa-circle-xmark mr-1"></i> Claim Declined
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-warning px-3 py-1 font-weight-bold text-dark" style="background: #ffb300; font-size: 12px;">
                                            <i class="fa-solid fa-clock-rotate-left mr-1"></i> Under Review
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Card Body: Instrument Details & Evidence -->
                            <div class="card-body p-4">
                                
                                <!-- Instrument Row -->
                                <div class="d-flex align-items-center pb-3 mb-3 border-bottom" style="border-color: #f0ede6 !important;">
                                    <img src="../uploads/<?php echo htmlspecialchars($r['prod_original_img']); ?>" 
                                         alt="<?php echo htmlspecialchars($r['product_name']); ?>" 
                                         class="rounded border mr-3" 
                                         style="width: 70px; height: 70px; object-fit: contain; background: #fafafa; padding: 4px;"
                                         onerror="this.src='../Guest/ins.jpg'">
                                    <div>
                                        <h5 class="font-weight-bold text-dark mb-1" style="font-family: 'Playfair Display', Georgia, serif; font-size: 17px;">
                                            <?php echo htmlspecialchars($r["product_name"]); ?>
                                        </h5>
                                        <?php if (!empty($r['subcat_name'])): ?>
                                            <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 11px;">
                                                <?php echo htmlspecialchars($r['subcat_name']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="small text-muted ml-2">
                                            Cart Ref: #CRT-<?php echo str_pad($r['cart_id'], 5, '0', STR_PAD_LEFT); ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Issue Statement -->
                                <div class="mb-3">
                                    <strong class="text-dark small text-uppercase d-block mb-1">
                                        <i class="fa-solid fa-comment-dots text-warning mr-1"></i> Reported Problem:
                                    </strong>
                                    <p class="mb-0 text-muted p-3 rounded" style="background: #fdfcf9; border: 1px solid #eee; font-size: 13.5px; line-height: 1.5;">
                                        <?php echo nl2br(htmlspecialchars($r["issuedesc"])); ?>
                                    </p>
                                </div>

                                <!-- Uploaded Evidence Images -->
                                <div class="mb-3">
                                    <strong class="text-dark small text-uppercase d-block mb-2">
                                        <i class="fa-solid fa-paperclip text-muted mr-1"></i> Uploaded Evidence:
                                    </strong>
                                    <div class="row">
                                        <!-- Warranty Card Uploaded -->
                                        <div class="col-6">
                                            <div class="evidence-box text-center p-2 rounded border" style="background: #faf8f2;">
                                                <span class="d-block small font-weight-bold text-muted mb-1">Warranty Card</span>
                                                <a href="../uploads/<?php echo htmlspecialchars($r['wimg']); ?>" target="_blank" title="Click to view full image">
                                                    <img src="../uploads/<?php echo htmlspecialchars($r['wimg']); ?>" 
                                                         alt="Warranty Card" 
                                                         class="img-fluid rounded" 
                                                         style="max-height: 80px; object-fit: cover;"
                                                         onerror="this.src='../Guest/ins.jpg'">
                                                </a>
                                            </div>
                                        </div>
                                        <!-- Defect Photo Uploaded -->
                                        <div class="col-6">
                                            <div class="evidence-box text-center p-2 rounded border" style="background: #faf8f2;">
                                                <span class="d-block small font-weight-bold text-muted mb-1">Defect Photo</span>
                                                <a href="../uploads/<?php echo htmlspecialchars($r['prod_img']); ?>" target="_blank" title="Click to view full image">
                                                    <img src="../uploads/<?php echo htmlspecialchars($r['prod_img']); ?>" 
                                                         alt="Defect Photo" 
                                                         class="img-fluid rounded" 
                                                         style="max-height: 80px; object-fit: cover;"
                                                         onerror="this.src='../Guest/ins.jpg'">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Response / Next Step Box -->
                                <div class="resolution-box mt-3 p-3 rounded" style="border: 1px solid #e8e5dc; background: <?php echo ($wstatus === 'accepted') ? '#e8f5e9' : (($wstatus === 'rejected') ? '#ffebee' : '#fffde7'); ?>;">
                                    <?php if ($wstatus === 'accepted'): ?>
                                        <div class="d-flex align-items-start">
                                            <i class="fa-solid fa-calendar-check text-success mr-2 mt-1" style="font-size: 18px;"></i>
                                            <div>
                                                <strong class="text-success d-block">Claim Approved &amp; Scheduled</strong>
                                                <span class="small text-dark">
                                                    Inspection / Service Date: <strong><?php echo htmlspecialchars($r["responsedate"] ?: "Contacting Customer"); ?></strong>. 
                                                    Our representative will reach out for pickup or in-store calibration.
                                                </span>
                                            </div>
                                        </div>
                                    <?php elseif ($wstatus === 'rejected'): ?>
                                        <div class="d-flex align-items-start">
                                            <i class="fa-solid fa-triangle-exclamation text-danger mr-2 mt-1" style="font-size: 18px;"></i>
                                            <div>
                                                <strong class="text-danger d-block">Warranty Claim Declined</strong>
                                                <span class="small text-dark">
                                                    This request does not qualify under standard hardware warranty terms (e.g. accidental drop, unauthorized modification, or cosmetic wear).
                                                </span>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex align-items-start">
                                            <i class="fa-solid fa-hourglass-half text-warning mr-2 mt-1" style="font-size: 18px;"></i>
                                            <div>
                                                <strong class="text-dark d-block">Claim Under Verification</strong>
                                                <span class="small text-muted">
                                                    Our technical administrators are verifying your serial credentials and defect photos. Status update expected within 24 hours.
                                                </span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-3 pt-2 text-right">
                                    <a href="warrantycard.php?cartid=<?php echo (int)$r['cart_id']; ?>" target="_blank" class="btn btn-sm btn-outline-dark font-weight-bold" style="border-radius: 6px;">
                                        <i class="fa-solid fa-certificate text-warning mr-1"></i> Digital Warranty Card
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="card shadow-sm border-0 text-center py-5 px-3" style="border-radius: 16px; border: 1px solid #e8e5dc !important; background: #ffffff;">
                <div class="mb-3" style="width: 70px; height: 70px; margin: 0 auto; border-radius: 50%; background: rgba(215, 173, 105, 0.15); display: grid; place-items: center; color: #d7ad69; font-size: 28px;">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h4 class="font-weight-bold text-dark mb-2" style="font-family: 'Playfair Display', Georgia, serif;">No Warranty Claims Submitted</h4>
                <p class="text-muted small mx-auto mb-4" style="max-width: 440px;">
                    All your purchased instruments are backed by MelodyMart's 1-Year Official Warranty. If an issue arises, you can submit a claim directly from your purchase order history.
                </p>
                <div>
                    <a href="vieworders.php" class="btn text-dark font-weight-bold px-4 py-2" style="background: #d7ad69; border-radius: 8px;">
                        <i class="fa-solid fa-box-archive mr-1"></i> Browse Your Orders
                    </a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include("footer.php"); ?>
