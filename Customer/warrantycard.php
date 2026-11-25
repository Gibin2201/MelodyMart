<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("header.php");
include_once("../dboperation.php");

$obj = new dboperation();
$cartid = isset($_GET['cartid']) ? (int)$_GET['cartid'] : 0;

// Get order and product info for this cart ID
$sql = "SELECT c.*, p.product_name, p.image, p.brand, s.name AS subcat_name, 
               cm.cartmaster_id, cm.grandtotal, cm.cmstatus, cus.customer_name 
        FROM tbl_cart c 
        INNER JOIN tbl_product p ON c.product_id = p.product_id 
        LEFT JOIN tbl_subcategory s ON s.subcategory_id = p.subcat 
        INNER JOIN tbl_cartmaster cm ON c.cartmaster_id = cm.cartmaster_id 
        INNER JOIN tbl_customer cus ON cus.customer_id = c.customer_id 
        WHERE c.cart_id = '$cartid'";
$res = $obj->executequery($sql);
$display = ($res) ? mysqli_fetch_array($res) : null;
?>

<div class="warranty-page-wrapper">
  <div class="container my-5" id="warrantyCard">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
      
      <!-- Certificate Header -->
      <div class="warranty-header text-center py-4 px-3">
        <div class="warranty-seal-icon mb-2">
          <i class="fa-solid fa-award"></i>
        </div>
        <span class="warranty-badge-tag">OFFICIAL CERTIFICATE OF AUTHENTICITY</span>
        <h2 class="warranty-title mb-1">12-Month Limited Instrument Warranty</h2>
        <p class="warranty-subtitle mb-0">MelodyMart Certified Protection &bull; Verified Acoustic Calibrated Instrument</p>
      </div>

      <?php if ($display): ?>
      <div class="card-body p-4 p-md-5">
        <div class="row align-items-center mb-4">
          <!-- Instrument Photograph -->
          <div class="col-md-5 text-center mb-4 mb-md-0">
            <div class="warranty-img-box">
              <img src="../uploads/<?php echo htmlspecialchars($display['image']); ?>" 
                   alt="<?php echo htmlspecialchars($display['product_name']); ?>" 
                   class="img-fluid" 
                   onerror="this.src='../Guest/ins.jpg'">
            </div>
          </div>

          <!-- Certificate Specifications -->
          <div class="col-md-7">
            <div class="d-flex align-items-center mb-2 flex-wrap" style="gap: 8px;">
              <span class="badge badge-dark text-white px-2 py-1" style="background: #121411; font-size: 11px;">
                <?php echo htmlspecialchars(!empty($display['brand']) ? $display['brand'] : 'MelodyMart'); ?>
              </span>
              <span class="badge badge-warning text-dark font-weight-bold px-2 py-1" style="background: rgba(215, 173, 105, 0.3); font-size: 11px;">
                <?php echo htmlspecialchars(!empty($display['subcat_name']) ? $display['subcat_name'] : 'Instrument'); ?>
              </span>
            </div>

            <h3 class="fw-bold mb-3" style="font-family: 'Playfair Display', Georgia, serif; color: #121411;">
              <?php echo htmlspecialchars($display['product_name']); ?>
            </h3>

            <div class="row mb-3">
              <div class="col-6 mb-2">
                <span class="w-meta-label">Certificate / Serial</span>
                <span class="w-meta-val">#MM-WTY-<?php echo str_pad((string)$cartid, 6, '0', STR_PAD_LEFT); ?></span>
              </div>
              <div class="col-6 mb-2">
                <span class="w-meta-label">Order Reference</span>
                <span class="w-meta-val">#MM-ORD-<?php echo str_pad((string)$display['cartmaster_id'], 5, '0', STR_PAD_LEFT); ?></span>
              </div>
              <div class="col-6 mb-2">
                <span class="w-meta-label">Registered Owner</span>
                <span class="w-meta-val"><?php echo htmlspecialchars($display['customer_name']); ?></span>
              </div>
              <div class="col-6 mb-2">
                <span class="w-meta-label">Purchase Date</span>
                <span class="w-meta-val"><?php echo htmlspecialchars($display['cart_date']); ?></span>
              </div>
              <div class="col-6">
                <span class="w-meta-label">Total Amount Paid</span>
                <span class="w-meta-val text-success">₹<?php echo number_format((float)$display['totalamount'], 2); ?></span>
              </div>
              <div class="col-6">
                <span class="w-meta-label">Warranty Status</span>
                <span class="badge badge-success px-2 py-1" style="background: #2e7d32; font-weight: 600;">ACTIVE (12 Months)</span>
              </div>
            </div>

            <hr style="border-color: #eee;">

            <!-- Terms & Coverage -->
            <div class="warranty-terms-box p-3 rounded" style="background: #faf8f2; border: 1px solid #eee8db;">
              <h6 class="font-weight-bold mb-2 text-dark" style="font-size: 13px;">
                <i class="fa-solid fa-shield-halved text-warning mr-1"></i> Coverage &amp; Service Terms
              </h6>
              <ul class="mb-0 text-muted small pl-3" style="line-height: 1.6;">
                <li>Covers craftsmanship, hardware defects, electronics, and structural integrity for 12 months.</li>
                <li>Digital warranty card verified through official MelodyMart master luthier records.</li>
                <li>For servicing or claims, visit your Customer Orders tab and click <strong>Claim Warranty</strong>.</li>
              </ul>
            </div>
          </div>
        </div>

        <hr class="my-4">

        <!-- Action Controls -->
        <div class="text-center noprint">
          <p class="text-muted small mb-3">
            📝 <em>This is an official computer-generated warranty certificate and does not require a physical signature.</em>
          </p>
          <button onclick="downloadWarranty()" class="btn btn-dark font-weight-bold px-4 py-2 mr-2" style="background: #121411; border-radius: 8px;">
            <i class="fa-solid fa-print mr-1 text-warning"></i> Print / Save as PDF
          </button>
          <a href="vieworders.php" class="btn btn-outline-dark font-weight-bold px-4 py-2" style="border-radius: 8px;">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Orders
          </a>
        </div>
      </div>
      <?php else: ?>
        <div class="alert alert-warning text-center m-5 p-4 rounded-3">
          <i class="fa-solid fa-triangle-exclamation mb-2" style="font-size: 32px;"></i>
          <h5>No warranty details available for this item.</h5>
          <a href="vieworders.php" class="btn btn-dark mt-2">View Your Orders</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<style>
.warranty-page-wrapper {
  background: #f7f5ef;
  min-height: 80vh;
  padding: 100px 0 60px;
  font-family: 'DM Sans', -apple-system, sans-serif;
}

.warranty-header {
  background: linear-gradient(135deg, #121411 0%, #2b2e29 100%);
  color: #f7f5ef;
  border-bottom: 3px solid #d7ad69;
}

.warranty-seal-icon {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: rgba(215, 173, 105, 0.2);
  color: #d7ad69;
  font-size: 22px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  border: 1px solid rgba(215, 173, 105, 0.4);
}

.warranty-badge-tag {
  display: inline-block;
  color: #d7ad69;
  font-size: 11px;
  letter-spacing: 2px;
  font-weight: 700;
  text-transform: uppercase;
  margin-bottom: 4px;
}

.warranty-title {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: 24px;
  font-weight: 700;
  color: #ffffff;
}

.warranty-subtitle {
  color: #c5c2b8;
  font-size: 13px;
}

.warranty-img-box {
  width: 100%;
  max-width: 280px;
  height: 250px;
  background: #faf9f6;
  border: 2px dashed #d7ad69;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  margin: 0 auto;
}

.warranty-img-box img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.w-meta-label {
  display: block;
  font-size: 11px;
  color: #8c8f87;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 600;
  margin-bottom: 2px;
}

.w-meta-val {
  font-size: 14px;
  font-weight: 700;
  color: #121411;
}

/* Hide navigation, footer and controls when printing */
@media print {
  .noprint,
  header,
  footer,
  .main_menu {
    display: none !important;
  }
  .warranty-page-wrapper {
    background: #ffffff !important;
    padding: 0 !important;
  }
  .card {
    border: 2px solid #121411 !important;
    box-shadow: none !important;
  }
  body {
    -webkit-print-color-adjust: exact !important;
    color-adjust: exact !important;
  }
}
</style>

<script>
function downloadWarranty() {
  window.print();
}
</script>

<?php include("footer.php"); ?>
