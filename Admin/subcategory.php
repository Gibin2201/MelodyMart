<?php 
include_once('header.php');
include_once("../dboperation.php"); 
$obj = new dboperation();
$sql = "SELECT * FROM tbl_category ORDER BY category_name ASC";
$res = $obj->executequery($sql); 
?>
<div class="main-panel"> 
    <div class="content"> 
        <div class="container-fluid"> 
            <h4 class="page-title">Subcategory Management</h4>
            <div class="row">
                <div class="col-md-8 col-lg-7">
                    <div class="card"> 
                        <div class="card-header"> 
                            <h4 class="card-title">Add New Subcategory</h4> 
                            <p class="card-subtitle">Select a parent category and enter subcategory details.</p> 
                        </div> 
                        <form action="subcategoryaction.php" method="POST" enctype="multipart/form-data"> 
                            <div class="card-body"> 
                                <div class="form-group"> 
                                    <label for="categoryid">Parent Category <span class="text-danger">*</span></label> 
                                    <select class="form-control" name="categoryid" id="categoryid" required> 
                                        <option value="">-- Select Category --</option> 
                                        <?php while ($display = mysqli_fetch_array($res)) { ?>
                                            <option value="<?php echo (int)$display["category_id"]; ?>">
                                                <?php echo htmlspecialchars($display["category_name"]); ?> 
                                            </option> 
                                        <?php } ?> 
                                    </select> 
                                    <small class="text-muted d-block mt-1">Choose the category to which this subcategory belongs.</small> 
                                </div> 
                                
                                <div class="form-group"> 
                                    <label for="subcategoryname">Subcategory Name <span class="text-danger">*</span></label> 
                                    <input type="text" class="form-control" name="subcategoryname" id="subcategoryname" placeholder="e.g. Electric Guitars, Acoustic Guitars, Digital Pianos" maxlength="100" required> 
                                </div> 
                                
                                <div class="form-group"> 
                                    <label for="image">Subcategory Image <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" name="image" id="image" accept="image/*" required>
                                    <small class="text-muted d-block mt-1">Upload an image representing this subcategory.</small> 
                                </div> 
                                
                                <div class="form-group">
                                    <label for="description">Description <span class="text-danger">*</span></label> 
                                    <textarea class="form-control" name="description" id="description" placeholder="Enter subcategory description" rows="4" maxlength="1000" required></textarea>
                                </div> 
                            </div> 
                            <div class="card-action"> 
                                <button type="submit" name="submit" class="btn btn-success">
                                    <i class="fa-solid fa-check mr-1"></i> Save Subcategory
                                </button> 
                                <button type="reset" class="btn btn-danger">
                                    <i class="fa-solid fa-rotate-left mr-1"></i> Clear
                                </button> 
                                <a href="subcategoryview.php" class="btn btn-outline-secondary ml-auto">
                                    <i class="fa-solid fa-list mr-1"></i> View All
                                </a>
                            </div> 
                        </form> 
                    </div> 
                </div> 
            </div>
        </div> 
    </div> 
</div> 
<?php include_once('footer.php'); ?>