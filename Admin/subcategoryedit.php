<?php
include_once('header.php');
include_once('../dboperation.php');
$obj = new dboperation();

$r = ['name' => '', 'image' => '', 'description' => '', 'subcategory_id' => 0];
if (isset($_GET["lid"])) {
    $lid = (int)$_GET["lid"];
    $sql = "SELECT * FROM tbl_subcategory WHERE subcategory_id='$lid'";
    $res = $obj->executequery($sql);
    if ($res && $row = mysqli_fetch_array($res)) {
        $r = $row;
    }
}
?>
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Subcategory Management</h4>
            <div class="row">
                <div class="col-md-7 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Edit Subcategory</h4>
                            <p class="card-subtitle">Update subcategory details and specifications</p>
                        </div>
                        <form action="subcategoryeditaction.php" method="POST" enctype="multipart/form-data">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="subcategoryname">Subcategory Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="subcategoryname" id="subcategoryname" placeholder="Enter subcategory" value="<?php echo htmlspecialchars($r["name"]); ?>" required>
                                </div>
                                <div class="form-group"> 
                                    <label for="image">Subcategory Image</label>
                                    <?php if (!empty($r['image'])): ?>
                                        <div class="mb-2">
                                            <img src="../uploads/<?php echo htmlspecialchars($r['image']); ?>" alt="Current Image" style="width: 100px; height: 75px; object-fit: cover; border-radius: 8px; border: 2px solid var(--admin-gold); padding: 2px;">
                                            <span class="d-block text-muted small mt-1">Current image</span>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" name="image" id="image" accept="image/*">
                                    <small class="text-muted d-block mt-1">Leave blank to retain current image.</small> 
                                </div> 
                                <div class="form-group">
                                    <label for="description">Description <span class="text-danger">*</span></label> 
                                    <textarea class="form-control" name="description" id="description" placeholder="Enter subcategory description" rows="4" maxlength="1000" required><?php echo htmlspecialchars($r["description"]); ?></textarea>
                                </div> 
                            </div> 
                            <div class="card-action">
                                <input type="hidden" name="subcategory_id" value="<?php echo (int)$r["subcategory_id"]; ?>">
                                <button type="submit" name="submit" class="btn btn-success">
                                    <i class="fa-solid fa-check mr-1"></i> Update Subcategory
                                </button>
                                <a href="subcategoryview.php" class="btn btn-danger">
                                    <i class="fa-solid fa-xmark mr-1"></i> Cancel
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