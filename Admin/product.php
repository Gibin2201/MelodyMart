<?php 
include("header.php");
include_once("../dboperation.php");
$db = new dboperation();
$sql = "SELECT * FROM tbl_subcategory ORDER BY name ASC";
$result = $db->executequery($sql);
?>
<div class="main-panel"> 
    <div class="content"> 
        <div class="container-fluid"> 
            <h4 class="page-title">Product Management</h4> 
            <div class="row">
                <div class="col-md-9 col-lg-8">
                    <div class="card"> 
                        <div class="card-header"> 
                            <h4 class="card-title">Add New Musical Product</h4> 
                            <p class="card-subtitle">Register instruments, audio gear, and accessories with detailed specifications.</p> 
                        </div>
                        <form action="productaction.php" method="POST" enctype="multipart/form-data"> 
                            <div class="card-body"> 
                                <div class="form-group"> 
                                    <label for="productname">Product Name <span class="text-danger">*</span></label> 
                                    <input type="text" class="form-control" name="productname" id="productname" placeholder="e.g. Fender Stratocaster Player II, Yamaha P-125 Piano" required> 
                                </div> 

                                <div class="form-group">
                                    <label for="subcategory_id">Subcategory <span class="text-danger">*</span></label>
                                    <select class="form-control" name="subcategory_id" id="subcategory_id" required>
                                        <option value="">-- Select Subcategory --</option>
                                        <?php 
                                        if ($result) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                            <option value="<?php echo (int)$row['subcategory_id']; ?>">
                                                <?php echo htmlspecialchars($row['name']); ?>
                                            </option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <small class="text-muted d-block mt-1">Select the specific instrument type or classification.</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group"> 
                                            <label for="brand">Brand / Maker</label> 
                                            <input type="text" class="form-control" name="brand" id="brand" placeholder="e.g. Yamaha, Fender, Gibson" value="MelodyMart"> 
                                        </div> 
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group"> 
                                            <label for="amount">Price Amount (INR ₹) <span class="text-danger">*</span></label> 
                                            <input type="number" class="form-control" name="amount" id="amount" placeholder="e.g. 14999.00" min="0" step="0.01" required> 
                                        </div> 
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group"> 
                                            <label for="stock">Stock Quantity <span class="text-danger">*</span></label> 
                                            <input type="number" class="form-control" name="stock" id="stock" placeholder="e.g. 25" min="0" required> 
                                        </div> 
                                    </div>
                                </div>

                                <div class="form-group"> 
                                    <label for="description">Product Description <span class="text-danger">*</span></label> 
                                    <textarea class="form-control" name="description" id="description" rows="4" placeholder="Detail specifications, tone quality, wood materials, craftsmanship, and accessories included..." required></textarea> 
                                </div> 

                                <div class="form-group"> 
                                    <label for="image">Product Image <span class="text-danger">*</span></label> 
                                    <input type="file" class="form-control" name="image" id="image" accept="image/*" required> 
                                    <small class="text-muted d-block mt-1">Upload high-resolution instrument photograph (JPG, PNG, WebP).</small> 
                                </div> 
                            </div> 
                            <div class="card-action d-flex align-items-center flex-wrap" style="gap: 10px;"> 
                                <button type="submit" name="submit" class="btn btn-success">
                                    <i class="fa-solid fa-plus mr-1"></i> Add Product
                                </button> 
                                <button type="reset" class="btn btn-danger">
                                    <i class="fa-solid fa-rotate-left mr-1"></i> Clear Form
                                </button> 
                                <a href="productview.php" class="btn btn-outline-secondary ml-auto">
                                    <i class="fa-solid fa-boxes-stacked mr-1"></i> View All Products
                                </a>
                            </div> 
                        </form>
                    </div> 
                </div> 
            </div>
        </div> 
    </div> 
</div>
<?php include("footer.php"); ?>