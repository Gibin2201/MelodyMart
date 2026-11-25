<?php
include_once('header.php');
include_once('../dboperation.php');
$obj = new dboperation();

$r = ['category_name' => '', 'category_image' => '', 'category_id' => 0];
if (isset($_GET["cid"])) {
    $cid = (int)$_GET["cid"];
    $sql = "SELECT * FROM tbl_category WHERE category_id='$cid'";
    $res = $obj->executequery($sql);
    if ($res && $row = mysqli_fetch_array($res)) {
        $r = $row;
    }
}
?>
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Category Management</h4>
            <div class="row">
                <div class="col-md-7 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Edit Category</h4>
                            <p class="card-subtitle">Update musical instrument category details</p>
                        </div>
                        <form action="categoryeditaction.php" method="POST" enctype="multipart/form-data">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="categoryname">Category Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="categoryname" name="categoryname" placeholder="Enter Category" value="<?php echo htmlspecialchars($r["category_name"]); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="image">Category Image</label>
                                    <?php if (!empty($r['category_image'])): ?>
                                        <div class="mb-2">
                                            <img src="../uploads/<?php echo htmlspecialchars($r['category_image']); ?>" alt="Current Image" style="width: 100px; height: 75px; object-fit: cover; border-radius: 8px; border: 2px solid var(--admin-gold); padding: 2px;">
                                            <span class="d-block text-muted small mt-1">Current image</span>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    <small class="text-muted d-block mt-1">Leave empty to keep current image.</small>
                                </div>
                            </div>
                            <div class="card-action">
                                <input type="hidden" name="Category_id" value="<?php echo (int)$r["category_id"]; ?>">
                                <button type="submit" name="submit" class="btn btn-success">
                                    <i class="fa-solid fa-check mr-1"></i> Update Category
                                </button>
                                <a href="categoryview.php" class="btn btn-danger">
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