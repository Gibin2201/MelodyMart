<?php 
include("header.php"); 
include_once("../dboperation.php");
$obj = new dboperation(); 

$selectedSub = isset($_GET['subcat']) ? (int)$_GET['subcat'] : 0;
$where = "";
if ($selectedSub > 0) {
    $where = "WHERE p.subcat = '$selectedSub'";
}

$sql = "SELECT p.*, s.name AS subcat_name, c.category_name 
        FROM tbl_product p 
        LEFT JOIN tbl_subcategory s ON p.subcat = s.subcategory_id 
        LEFT JOIN tbl_category c ON s.category_id = c.category_id 
        $where 
        ORDER BY p.product_id DESC";
$result = $obj->executequery($sql); 

// For dropdown filter and edit modal
$subListRes = $obj->executequery("SELECT s.subcategory_id, s.name, c.category_name 
                                  FROM tbl_subcategory s 
                                  LEFT JOIN tbl_category c ON s.category_id = c.category_id 
                                  ORDER BY c.category_name ASC, s.name ASC");

$subcategories = [];
if ($subListRes) {
    while ($subRow = mysqli_fetch_assoc($subListRes)) {
        $subcategories[] = $subRow;
    }
}
?> 

<div class="main-panel"> 
    <div class="content"> 
        <div class="container-fluid"> 
            
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <div>
                    <h4 class="page-title mb-1">Product Catalog</h4>
                    <p class="text-muted small mb-0">Browse, update stock levels, edit instrument details, and manage inventory</p>
                </div>
                <a href="product.php" class="btn btn-success font-weight-bold">
                    <i class="fa-solid fa-plus mr-1"></i> Add New Product
                </a>
            </div>

            <div class="card shadow-sm border-0" style="border: 1px solid #e8e5dc !important; border-radius: 12px;"> 
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap bg-light border-bottom"> 
                    <div>
                        <h4 class="card-title mb-0">Musical Products Inventory</h4> 
                        <p class="card-subtitle small text-muted">Click on any product's stock or edit button to update details instantly</p> 
                    </div>
                    <!-- Filter by Subcategory -->
                    <div class="d-flex align-items-center mt-2 mt-md-0" style="gap: 10px;">
                        <label for="filterSubcat" class="mb-0 font-weight-bold small text-muted">Filter:</label>
                        <select class="form-control form-control-sm" id="filterSubcat" onchange="filterProducts(this.value)" style="min-width: 220px;">
                            <option value="0">All Subcategories</option>
                            <?php foreach ($subcategories as $subRow): ?>
                                <option value="<?php echo (int)$subRow['subcategory_id']; ?>" <?php echo ($selectedSub == (int)$subRow['subcategory_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($subRow['category_name'] . ' &rarr; ' . $subRow['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div> 

                <div class="card-body p-0"> 
                    <div class="table-responsive"> 
                        <table class="table table-hover align-middle mb-0"> 
                            <thead class="bg-light text-muted" style="font-size: 12px; text-transform: uppercase;"> 
                                <tr> 
                                    <th style="width: 70px;" class="px-3">Image</th> 
                                    <th>Product Name</th> 
                                    <th>Brand</th>
                                    <th>Subcategory</th> 
                                    <th>Price</th> 
                                    <th style="text-align: center;">Stock</th> 
                                    <th style="text-align: center;">Status</th>
                                    <th style="width: 170px; text-align: center;" class="px-3">Actions</th> 
                                </tr> 
                            </thead> 
                            <tbody> 
                                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                    <?php while ($r = mysqli_fetch_assoc($result)): 
                                        $stockQty = (int)$r['stock'];
                                        $isAvailable = (strtolower($r['pstatus']) === 'available');
                                    ?>
                                        <tr> 
                                            <!-- Image -->
                                            <td class="px-3 py-3 align-middle">
                                                <img src="../uploads/<?php echo htmlspecialchars($r['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($r['product_name']); ?>" 
                                                     style="width: 60px; height: 50px; object-fit: contain; background: #fafafa; border-radius: 6px; border: 1px solid #eee; padding: 2px;"
                                                     onerror="this.src='../Guest/ins.jpg'">
                                            </td>

                                            <!-- Product Name & ID -->
                                            <td class="py-3 align-middle">
                                                <strong class="text-dark d-block"><?php echo htmlspecialchars($r['product_name']); ?></strong>
                                                <small class="text-muted">ID: #<?php echo (int)$r['product_id']; ?></small>
                                            </td>

                                            <!-- Brand -->
                                            <td class="py-3 align-middle">
                                                <span class="badge badge-light border"><?php echo htmlspecialchars($r['brand'] ?: 'MelodyMart'); ?></span>
                                            </td>

                                            <!-- Subcategory -->
                                            <td class="py-3 align-middle">
                                                <span class="badge badge-warning text-dark font-weight-bold" style="background: rgba(215, 173, 105, 0.25);">
                                                    <?php echo htmlspecialchars($r['subcat_name'] ?: 'General'); ?>
                                                </span>
                                                <div class="small text-muted"><?php echo htmlspecialchars($r['category_name'] ?: ''); ?></div>
                                            </td>

                                            <!-- Price -->
                                            <td class="py-3 align-middle">
                                                <strong class="text-success" style="font-size: 15px;">₹<?php echo number_format((float)$r['amount'], 2); ?></strong>
                                            </td>

                                            <!-- Stock with Quick-Update Trigger -->
                                            <td class="py-3 text-center align-middle">
                                                <div class="d-inline-flex align-items-center" style="gap: 5px;">
                                                    <?php if ($stockQty > 5): ?>
                                                        <span class="badge badge-success px-2 py-1 font-weight-bold" style="background: #2e7d32;"><?php echo $stockQty; ?></span>
                                                    <?php elseif ($stockQty > 0): ?>
                                                        <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold" style="background: #ffb300;"><?php echo $stockQty; ?></span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger px-2 py-1 font-weight-bold" style="background: #c62828;">0</span>
                                                    <?php endif; ?>

                                                    <!-- Restock button -->
                                                    <button type="button" class="btn btn-xs btn-outline-secondary p-1" 
                                                            style="line-height: 1; border-radius: 4px;"
                                                            onclick="openQuickStockModal(<?php echo (int)$r['product_id']; ?>, '<?php echo htmlspecialchars(addslashes($r['product_name'])); ?>', <?php echo $stockQty; ?>, '<?php echo htmlspecialchars($r['pstatus']); ?>')"
                                                            title="Quick Update Stock">
                                                        <i class="fa-solid fa-boxes-packing" style="font-size: 11px;"></i>
                                                    </button>
                                                </div>
                                            </td>

                                            <!-- Status -->
                                            <td class="py-3 text-center align-middle">
                                                <?php if ($isAvailable): ?>
                                                    <span class="badge badge-success px-2 py-1" style="background: #2e7d32;">Available</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary px-2 py-1">Unavailable</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Actions: Edit Details, Full Page, Delete -->
                                            <td class="py-3 text-center px-3 align-middle" style="white-space: nowrap;">
                                                <!-- On-page Edit Details Modal -->
                                                <button type="button" class="btn btn-sm btn-primary font-weight-bold mr-1" 
                                                        style="border-radius: 6px;"
                                                        onclick='openEditProductModal(<?php echo json_encode([
                                                            "id" => (int)$r["product_id"],
                                                            "name" => $r["product_name"],
                                                            "subcat" => (int)$r["subcat"],
                                                            "brand" => $r["brand"] ?: "MelodyMart",
                                                            "amount" => $r["amount"],
                                                            "stock" => (int)$r["stock"],
                                                            "pstatus" => $r["pstatus"],
                                                            "desc" => $r["description"] ?: "",
                                                            "image" => $r["image"]
                                                        ]); ?>)'
                                                        title="Edit details in modal">
                                                    <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                                                </button>

                                                <!-- Full Page Editor -->
                                                <a href="productedit.php?pid=<?php echo (int)$r['product_id']; ?>" 
                                                   class="btn btn-sm btn-outline-dark mr-1" 
                                                   style="border-radius: 6px;"
                                                   title="Open dedicated editor page">
                                                    <i class="fa-solid fa-up-right-from-square"></i>
                                                </a>

                                                <!-- Delete Product -->
                                                <a href="deleteproduct.php?pid=<?php echo (int)$r['product_id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger" 
                                                   style="border-radius: 6px;"
                                                   onclick="return confirm('Are you sure you want to permanently delete <?php echo htmlspecialchars(addslashes($r['product_name'])); ?>?');"
                                                   title="Delete product">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr> 
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr> 
                                        <td colspan="8" class="text-center py-5 text-muted"> 
                                            <i class="fa-solid fa-box-open mb-2 text-warning" style="font-size: 32px; display: block;"></i>
                                            No products found for the selected subcategory.
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

<!-- 1. QUICK STOCK RESTOCK MODAL -->
<div class="modal fade" id="quickStockModal" tabindex="-1" role="dialog" aria-labelledby="stockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-weight-bold" id="stockModalLabel">
                    <i class="fa-solid fa-boxes-packing text-warning mr-2"></i> Update Product Stock
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="producteditaction.php" method="POST">
                <input type="hidden" name="action" value="quick_stock">
                <input type="hidden" name="product_id" id="quickStockPid" value="">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <strong class="d-block text-dark" id="quickStockPname" style="font-size: 16px;"></strong>
                        <span class="small text-muted">Adjust current stock quantity and availability status</span>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small text-uppercase">Stock Quantity</label>
                        <input type="number" class="form-control" name="stock" id="quickStockQty" min="0" required oninput="quickStockCheck(this.value)">
                    </div>

                    <!-- Quick Restock Helper Buttons -->
                    <div class="d-flex align-items-center mb-3" style="gap: 8px;">
                        <span class="small text-muted font-weight-bold">Quick Add:</span>
                        <button type="button" class="btn btn-xs btn-outline-secondary" onclick="addStock(5)">+5</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary" onclick="addStock(10)">+10</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary" onclick="addStock(25)">+25</button>
                        <button type="button" class="btn btn-xs btn-outline-danger" onclick="setZeroStock()">Out of Stock (0)</button>
                    </div>

                    <div class="form-group mb-2">
                        <label class="font-weight-bold text-dark small text-uppercase">Status</label>
                        <select class="form-control" name="pstatus" id="quickStockStatus">
                            <option value="available">Available</option>
                            <option value="Unavailable">Unavailable</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success font-weight-bold">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Update Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. FULL PRODUCT DETAILS EDIT MODAL -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-weight-bold" id="editModalLabel">
                    <i class="fa-solid fa-pen-to-square text-warning mr-2"></i> Edit Product Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="producteditaction.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit_product">
                <input type="hidden" name="product_id" id="editPid" value="">

                <div class="modal-body p-4">
                    
                    <!-- Product Name -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small text-uppercase">Product Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="product_name" id="editPname" required>
                    </div>

                    <!-- Subcategory & Brand -->
                    <div class="row mb-3">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-dark small text-uppercase">Subcategory <span class="text-danger">*</span></label>
                            <select class="form-control" name="subcategory_id" id="editSubcat" required>
                                <?php foreach ($subcategories as $subRow): ?>
                                    <option value="<?php echo (int)$subRow['subcategory_id']; ?>">
                                        <?php echo htmlspecialchars($subRow['category_name'] . ' &rarr; ' . $subRow['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-dark small text-uppercase">Brand / Maker</label>
                            <input type="text" class="form-control" name="brand" id="editBrand">
                        </div>
                    </div>

                    <!-- Price, Stock & Status -->
                    <div class="row mb-3">
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-dark small text-uppercase">Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="amount" id="editAmount" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-dark small text-uppercase">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="stock" id="editStock" min="0" required oninput="editStockCheck(this.value)">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-dark small text-uppercase">Status</label>
                            <select class="form-control" name="pstatus" id="editStatus">
                                <option value="available">Available</option>
                                <option value="Unavailable">Unavailable</option>
                            </select>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small text-uppercase">Specifications &amp; Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="description" id="editDesc" rows="3" required></textarea>
                    </div>

                    <!-- Product Image -->
                    <div class="form-group mb-2">
                        <label class="font-weight-bold text-dark small text-uppercase">Product Image</label>
                        <div class="d-flex align-items-center flex-wrap">
                            <img src="" id="editImgPreview" class="rounded border mr-3 mb-2" style="width: 65px; height: 65px; object-fit: contain; background: #fafafa; padding: 2px;">
                            <div class="flex-grow-1 mb-2">
                                <input type="file" class="form-control-file" name="image" accept="image/*">
                                <small class="text-muted d-block">Optional: Upload new photo to replace current image.</small>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success font-weight-bold">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Save Product Details
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function filterProducts(subcatId) {
    if (subcatId > 0) {
        window.location = 'productview.php?subcat=' + subcatId;
    } else {
        window.location = 'productview.php';
    }
}

// Quick Stock Modal
function openQuickStockModal(pid, pname, stock, status) {
    document.getElementById('quickStockPid').value = pid;
    document.getElementById('quickStockPname').innerText = pname;
    document.getElementById('quickStockQty').value = stock;
    document.getElementById('quickStockStatus').value = (status.toLowerCase() === 'available') ? 'available' : 'Unavailable';
    $('#quickStockModal').modal('show');
}

function quickStockCheck(val) {
    var select = document.getElementById('quickStockStatus');
    select.value = (parseInt(val) <= 0) ? 'Unavailable' : 'available';
}

function addStock(n) {
    var input = document.getElementById('quickStockQty');
    var current = parseInt(input.value) || 0;
    input.value = current + n;
    quickStockCheck(input.value);
}

function setZeroStock() {
    var input = document.getElementById('quickStockQty');
    input.value = 0;
    quickStockCheck(0);
}

// Full Edit Product Modal
function openEditProductModal(p) {
    document.getElementById('editPid').value = p.id;
    document.getElementById('editPname').value = p.name;
    document.getElementById('editSubcat').value = p.subcat;
    document.getElementById('editBrand').value = p.brand;
    document.getElementById('editAmount').value = p.amount;
    document.getElementById('editStock').value = p.stock;
    document.getElementById('editStatus').value = (p.pstatus.toLowerCase() === 'available') ? 'available' : 'Unavailable';
    document.getElementById('editDesc').value = p.desc;
    
    var imgPreview = document.getElementById('editImgPreview');
    if (p.image) {
        imgPreview.src = '../uploads/' + p.image;
        imgPreview.style.display = 'block';
    } else {
        imgPreview.style.display = 'none';
    }

    $('#editProductModal').modal('show');
}

function editStockCheck(val) {
    var select = document.getElementById('editStatus');
    if (parseInt(val) <= 0) {
        select.value = 'Unavailable';
    } else {
        select.value = 'available';
    }
}
</script>

<?php include("footer.php"); ?>
