<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("header.php");
include_once("../dboperation.php");

$obj = new dboperation();

if (!isset($_SESSION["customer_id"])) {
    echo "<script>window.location='../Guest/login.php';</script>";
    exit();
}

$customerid = (int)$_SESSION["customer_id"];
$cartid = isset($_GET["cartid"]) ? (int)$_GET["cartid"] : 0;

// Fetch verified cart item, product info, subcategory, and customer details
$query = "SELECT c.*, p.product_name, p.image, p.amount, p.brand, sub.name AS subcat_name, cus.customer_name, cus.phone_no, cus.email 
          FROM tbl_cart c 
          INNER JOIN tbl_customer cus ON cus.customer_id = c.customer_id 
          INNER JOIN tbl_product p ON p.product_id = c.product_id 
          LEFT JOIN tbl_subcategory sub ON sub.subcategory_id = p.subcat 
          WHERE c.cart_id = '$cartid' AND c.customer_id = '$customerid'";

$res = $obj->executequery($query);
$item = ($res) ? mysqli_fetch_array($res) : null;

if (!$item) {
    echo "<div class='container my-5 text-center py-5'>
            <div class='alert alert-warning py-4'>
                <h4><i class='fa-solid fa-triangle-exclamation mr-2'></i> Invalid or Unverified Purchase Item</h4>
                <p class='mb-3'>We could not locate this purchased instrument under your customer account.</p>
                <a href='vieworders.php' class='btn btn-dark'>Return to Order History</a>
            </div>
          </div>";
    include("footer.php");
    exit();
}
?>

<div class="warranty-apply-wrapper py-5" style="background: #f7f5ef; min-height: 85vh;">
    <div class="container">

        <!-- Page Header Breadcrumb -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap pb-3 border-bottom" style="border-color: #e8e5dc !important;">
            <div>
                <span class="badge badge-warning px-3 py-1 mb-1 text-dark font-weight-bold" style="background: #d7ad69; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-shield-halved mr-1"></i> MELODYMART WARRANTY SERVICE
                </span>
                <h2 style="font-family: 'Playfair Display', Georgia, serif; font-weight: 700; color: #121411; margin-top: 4px;">
                    File a Warranty Claim
                </h2>
                <p class="text-muted small mb-0">Submit inspection requests for manufacturing defects, calibration issues, or hardware repairs.</p>
            </div>
            <div class="mt-2 mt-md-0">
                <a href="vieworders.php" class="btn btn-outline-dark font-weight-bold px-3 py-2" style="border-radius: 8px;">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Back to Orders
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            
            <!-- Left Column: Purchased Instrument Overview -->
            <div class="col-lg-5 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; overflow: hidden; border: 1px solid #e8e5dc !important; background: #ffffff;">
                    <div class="card-header py-3 px-4" style="background: #121411; color: #ffffff;">
                        <h5 class="mb-0 font-weight-bold" style="font-family: 'Playfair Display', Georgia, serif; font-size: 17px;">
                            <i class="fa-solid fa-guitar mr-2 text-warning"></i> Covered Instrument
                        </h5>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="product-preview-img-box mb-3 mx-auto" style="width: 180px; height: 180px; background: #faf8f2; border: 1px solid #e8e5dc; border-radius: 12px; display: flex; align-items: center; justify-content: center; padding: 10px;">
                            <img src="../uploads/<?php echo htmlspecialchars($item['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                 class="img-fluid" 
                                 style="max-height: 160px; object-fit: contain;"
                                 onerror="this.src='../Guest/ins.jpg'">
                        </div>

                        <h4 class="font-weight-bold text-dark mb-1" style="font-family: 'Playfair Display', Georgia, serif;">
                            <?php echo htmlspecialchars($item['product_name']); ?>
                        </h4>
                        
                        <?php if (!empty($item['subcat_name'])): ?>
                            <span class="badge badge-warning text-dark font-weight-bold px-3 py-1 mb-3" style="background: rgba(215, 173, 105, 0.25);">
                                <?php echo htmlspecialchars($item['subcat_name']); ?>
                            </span>
                        <?php endif; ?>

                        <div class="p-3 bg-light rounded text-left mt-3" style="border: 1px dashed #d7ad69;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Cart Reference:</span>
                                <strong class="small text-dark">#CRT-<?php echo str_pad($item['cart_id'], 5, '0', STR_PAD_LEFT); ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Purchase Date:</span>
                                <strong class="small text-dark"><?php echo htmlspecialchars($item['cart_date']); ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Quantity Covered:</span>
                                <strong class="small text-dark"><?php echo (int)$item['quantity']; ?> Unit(s)</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Paid Amount:</span>
                                <strong class="small text-success">₹<?php echo number_format((float)$item['totalamount'], 2); ?></strong>
                            </div>
                        </div>

                        <!-- Direct Link to Warranty Certificate -->
                        <div class="mt-4 pt-2 border-top">
                            <p class="small text-muted mb-2">Need your official warranty card to upload?</p>
                            <a href="warrantycard.php?cartid=<?php echo (int)$item['cart_id']; ?>" target="_blank" class="btn btn-sm btn-outline-dark font-weight-bold">
                                <i class="fa-solid fa-certificate text-warning mr-1"></i> View / Download Certificate
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Warranty Claim Form -->
            <div class="col-lg-7 mb-4">
                <div class="card shadow-sm border-0" style="border-radius: 16px; overflow: hidden; border: 1px solid #e8e5dc !important; background: #ffffff;">
                    <div class="card-header py-3 px-4" style="background: #121411; color: #ffffff;">
                        <h5 class="mb-0 font-weight-bold" style="font-family: 'Playfair Display', Georgia, serif; font-size: 17px;">
                            <i class="fa-solid fa-file-shield mr-2 text-warning"></i> Claim Submission Form
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        <form action="warrantyaction.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="cartid" value="<?php echo (int)$cartid; ?>" required>

                            <!-- Customer Name (Readonly) -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark small text-uppercase">
                                    <i class="fa-solid fa-user-check text-muted mr-1"></i> Registered Owner
                                </label>
                                <input type="text" name="customername" class="form-control form-control-mm" value="<?php echo htmlspecialchars($item["customer_name"]); ?>" readonly>
                            </div>

                            <!-- Upload 1: Warranty Card / Invoice -->
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark small text-uppercase">
                                    <i class="fa-solid fa-certificate text-warning mr-1"></i> Warranty Certificate / Purchase Receipt Image <span class="text-danger">*</span>
                                </label>
                                <div class="custom-file-upload-box">
                                    <input type="file" name="wimg" id="wimgInput" class="form-control" accept="image/*" required onchange="previewUpload(this, 'wimgPreview')">
                                    <small class="text-muted d-block mt-1">Upload a clear photo or screenshot of your MelodyMart warranty certificate.</small>
                                </div>
                                <div id="wimgPreview" class="upload-preview-box mt-2" style="display: none;">
                                    <img src="" alt="Certificate Preview" style="max-height: 120px; border-radius: 8px; border: 1px solid #ddd;">
                                </div>
                            </div>

                            <!-- Upload 2: Defect / Instrument Damage Photo -->
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark small text-uppercase">
                                    <i class="fa-solid fa-camera text-danger mr-1"></i> Defect / Instrument Issue Photo <span class="text-danger">*</span>
                                </label>
                                <div class="custom-file-upload-box">
                                    <input type="file" name="proimg" id="proimgInput" class="form-control" accept="image/*" required onchange="previewUpload(this, 'proimgPreview')">
                                    <small class="text-muted d-block mt-1">Upload high-resolution photo showing the damaged, defective, or misaligned component.</small>
                                </div>
                                <div id="proimgPreview" class="upload-preview-box mt-2" style="display: none;">
                                    <img src="" alt="Defect Preview" style="max-height: 120px; border-radius: 8px; border: 1px solid #ddd;">
                                </div>
                            </div>

                            <!-- Issue Description -->
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark small text-uppercase">
                                    <i class="fa-solid fa-align-left text-muted mr-1"></i> Detailed Problem Description <span class="text-danger">*</span>
                                </label>
                                <textarea name="issue" rows="4" class="form-control form-control-mm" placeholder="Please describe the exact issue you are facing (e.g. buzzing fretboard, tuner gear slipping, crack along soundboard, faulty electronics)..." required></textarea>
                            </div>

                            <!-- Terms Notice -->
                            <div class="alert alert-light border p-3 mb-4" style="background: #faf8f2; border-color: #e8e5dc !important; font-size: 13px; color: #555;">
                                <i class="fa-solid fa-circle-info text-warning mr-1"></i>
                                Once submitted, MelodyMart administrators will review the evidence within 24–48 hours. Upon acceptance, an inspection or service date will be scheduled.
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" name="submit" class="btn btn-block py-3 font-weight-bold text-dark" style="background: #d7ad69; border-radius: 10px; font-size: 16px; letter-spacing: 0.5px; box-shadow: 0 4px 14px rgba(215, 173, 105, 0.4);">
                                <i class="fa-solid fa-paper-plane mr-2"></i> Submit Official Warranty Claim
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<style>
.form-control-mm {
    border-radius: 8px;
    border: 1px solid #dcd8cc;
    padding: 10px 14px;
    font-size: 14px;
    background: #fdfcf9;
}
.form-control-mm:focus {
    border-color: #d7ad69;
    box-shadow: 0 0 0 3px rgba(215, 173, 105, 0.25);
    background: #ffffff;
}
</style>

<script>
function previewUpload(input, previewDivId) {
    var previewBox = document.getElementById(previewDivId);
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            previewBox.querySelector('img').src = e.target.result;
            previewBox.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        previewBox.style.display = 'none';
    }
}
</script>

<?php include("footer.php"); ?>