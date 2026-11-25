<?php 
include("header.php");
include("../dboperation.php");
$obj = new dboperation();

$s = "SELECT * FROM tbl_category ORDER BY category_id DESC";
$res = $obj->executequery($s);
?>
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h4 class="page-title mb-0">Category Management</h4>
                <a href="category.php" class="btn btn-success">
                    <i class="fa-solid fa-plus mr-1"></i> Add Category
                </a>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="card-title">Registered Categories</h4>
                        <p class="card-subtitle">View, edit, and organize musical instrument categories</p>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">#</th>
                                    <th>Category Name</th>
                                    <th>Image</th>
                                    <th style="width: 180px; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1; 
                                if ($res && mysqli_num_rows($res) > 0) {
                                    while ($r = mysqli_fetch_array($res)) { 
                                ?> 
                                    <tr>
                                        <td class="font-weight-bold text-muted"><?php echo $i++; ?></td>
                                        <td>
                                            <strong class="text-dark font-weight-bold" style="font-size: 15px;">
                                                <?php echo htmlspecialchars($r["category_name"]); ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <img src="../uploads/<?php echo rawurlencode(basename($r['category_image'])); ?>" 
                                                 alt="<?php echo htmlspecialchars($r["category_name"]); ?>" 
                                                 style="width: 70px; height: 55px; object-fit: cover; border-radius: 8px; border: 2px solid var(--admin-gold); padding: 2px; background: #fff;"
                                                 onerror="this.src='https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=200&q=80'">
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="categoryedit.php?cid=<?php echo (int)$r['category_id']; ?>" class="btn-category-edit mr-1" title="Edit Category">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </a>
                                            <a href="deletecategory.php?cid=<?php echo (int)$r['category_id']; ?>" class="btn-category-delete" onclick="return confirm('Are you sure you want to delete this category?');" title="Delete Category">
                                                <i class="fa-solid fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr> 
                                <?php 
                                    } 
                                } else { 
                                ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="fa-solid fa-folder-open mb-2" style="font-size: 30px; display: block; color: var(--admin-gold);"></i>
                                            No categories found. Click "Add Category" to create one.
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include("footer.php"); ?>