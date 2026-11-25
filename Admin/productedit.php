<?php 
include("header.php");
include_once("../dboperation.php");
$db = new dboperation();

$pid = isset($_GET["pid"]) ? (int)$_GET["pid"] : 0;
$sql = "SELECT p.*, s.name AS subcat_name 
        FROM tbl_product p 
        LEFT JOIN tbl_subcategory s ON p.subcat = s.subcategory_id 
        WHERE p.product_id = '$pid'";
$res = $db->executequery($sql);
$product = ($res) ? mysqli_fetch_assoc($res) : null;

// Subcategories list for dropdown
$subcatList = $db->executequery("SELECT s.subcategory_id, s.name, c.category_name 
                                FROM tbl_subcategory s 
                                LEFT JOIN tbl_category c ON s.category_id = c.category_id 
                                ORDER BY c.category_name ASC, s.name ASC");
?>

<div class="main-panel"> 
    <div class="content"> 
        <div class="container-fluid"> 
            
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <div>
                    <h4 class="page-title mb-1">Edit Musical Product</h4>
                    <p class="text-muted small mb-0">Update instrument specifications, pricing, stock levels, and media</p>
                </div>
                <a href="productview.php" class="btn btn-outline-secondary font-weight-bold">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Back to Products
                </a>
            </div>

            <?php if (!$product): ?>
                <div class="alert alert-warning py-4 text-center">
                    <h5><i class="fa-solid fa-triangle-exclamation mr-1"></i> Product Not Found</h5>
                    <p class="mb-3 text-muted">The requested product could not be found in the catalog.</p>
                    <a href="productview.php" class="btn btn-dark">View All Products</a>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-lg-9 col-md-11">
                        <div class="card shadow-sm border-0" style="border: 1px solid #e8e5dc !important; border-radius: 12px;">
                            <div class="card-header bg-dark text-white py-3 px-4 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 font-weight-bold" style="font-family: 'Playfair Display', Georgia, serif;">
                                    <i class="fa-solid fa-pen-to-square text-warning mr-2"></i> Update #<?php echo $pid; ?> &bull; <?php echo htmlspecialchars($product['product_name']); ?>
                                </h5>
                                <span class="badge badge-warning text-dark font-weight-bold">
                                    ID: #<?php echo $pid; ?>
                                </span>
                            </div>

                            <form action="producteditaction.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="edit_product">
                                <input type="hidden" name="product_id" value="<?php echo $pid; ?>">

                                <div class="card-body p-4">
                                    
                                    <!-- Product Name -->
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark">Product / Instrument Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                                    </div>

                                    <!-- Subcategory & Brand -->
                                    <div class="row mb-3">
                                        <div class="col-md-6 form-group">
                                            <label class="font-weight-bold text-dark">Subcategory <span class="text-danger">*</span></label>
                                            <select class="form-control" name="subcategory_id" required>
                                                <?php if ($subcatList): ?>
                                                    <?php while ($subRow = mysqli_fetch_assoc($subcatList)): ?>
                                                        <option value="<?php echo (int)$subRow['subcategory_id']; ?>" <?php echo ((int)$product['subcat'] === (int)$subRow['subcategory_id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($subRow['category_name'] . ' &rarr; ' . $subRow['name']); ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label class="font-weight-bold text-dark">Brand / Maker</label>
                                            <input type="text" class="form-control" name="brand" value="<?php echo htmlspecialchars($product['brand'] ?: 'MelodyMart'); ?>">
                                        </div>
                                    </div>

                                    <!-- Price, Stock & Status -->
                                    <div class="row mb-3">
                                        <div class="col-md-4 form-group">
                                            <label class="font-weight-bold text-dark">Price Amount (₹) <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">₹</span>
                                                </div>
                                                <input type="number" class="form-control" name="amount" value="<?php echo htmlspecialchars($product['amount']); ?>" min="0" step="0.01" required>
                                            </div>
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label class="font-weight-bold text-dark">Stock Quantity <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="stock" id="stockInput" value="<?php echo (int)$product['stock']; ?>" min="0" required oninput="checkStockStatus(this.value)">
                                            <small class="text-muted">Setting 0 automatically sets status to Unavailable.</small>
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label class="font-weight-bold text-dark">Availability Status</label>
                                            <select class="form-control" name="pstatus" id="statusSelect">
                                                <option value="available" <?php echo (strtolower($product['pstatus']) === 'available') ? 'selected' : ''; ?>>Available</option>
                                                <option value="Unavailable" <?php echo (strtolower($product['pstatus']) !== 'available') ? 'selected' : ''; ?>>Unavailable</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark">Detailed Specifications &amp; Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="description" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                                    </div>

                                    <!-- Image Update -->
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark">Product Photograph</label>
                                        <div class="d-flex align-items-center flex-wrap mb-2">
                                            <div class="mr-3 mb-2 text-center">
                                                <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                                                     alt="Current Product Image" 
                                                     id="currentImgPreview"
                                                     style="width: 90px; height: 90px; object-fit: contain; background: #fafafa; border: 1px solid #ddd; border-radius: 8px; padding: 4px;"
                                                     onerror="this.src='../Guest/ins.jpg'">
                                                <span class="d-block small text-muted mt-1">Current Image</span>
                                            </div>
                                            <div class="flex-grow-1 mb-2">
                                                <input type="file" class="form-control" name="image" accept="image/*" onchange="previewNewImage(this)">
                                                <small class="text-muted d-block mt-1">Leave empty to keep current photo, or select a new JPG, PNG, or WebP to replace it.</small>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="card-footer bg-light p-4 d-flex justify-content-between align-items-center">
                                    <a href="productview.php" class="btn btn-secondary font-weight-bold">
                                        <i class="fa-solid fa-xmark mr-1"></i> Cancel
                                    </a>
                                    <button type="submit" name="submit_edit" class="btn btn-success font-weight-bold px-4">
                                        <i class="fa-solid fa-floppy-disk mr-1"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div> 
    </div> 
</div> 

<script>
function checkStockStatus(val) {
    var select = document.getElementById('statusSelect');
    if (parseInt(val) <= 0) {
        select.value = 'Unavailable';
    } else {
        select.value = 'available';
    }
}

function previewNewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('currentImgPreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include("footer.php"); ?>
