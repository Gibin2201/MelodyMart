<?php 
include("header.php");
include_once("../dboperation.php");
$obj = new dboperation();
$s = "SELECT * FROM tbl_category ORDER BY category_name ASC";
$res = $obj->executequery($s);
?>	

<br><br><br>

<section class="latest-products section_gap">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-7 text-center">
                <div class="section-title">
                    <h1>Musical Categories</h1>
                    <p>Select a category to explore handcrafted instruments and specialized audio gear</p>
                </div>
            </div>
        </div>

        <div class="row">
            <?php 
            if ($res && mysqli_num_rows($res) > 0) {
                while($r = mysqli_fetch_array($res)) { 
            ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 rounded overflow-hidden" style="transition: transform 0.25s ease;">
                        <a href="subcatview.php?categoryid=<?php echo (int)$r['category_id']; ?>">
                            <div style="width:100%; height:200px; overflow:hidden; background:#f4f4f4;">
                                <img src="../uploads/<?php echo htmlspecialchars($r['category_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($r['category_name']); ?>" 
                                     class="card-img-top" 
                                     style="width:100%; height:100%; object-fit:cover;"
                                     onerror="this.src='../Guest/ins.jpg'">
                            </div>
                        </a>
                        <div class="card-body text-center p-3">
                            <h5 class="card-title font-weight-bold mb-2">
                                <a href="subcatview.php?categoryid=<?php echo (int)$r['category_id']; ?>" class="text-dark">
                                    <?php echo htmlspecialchars($r['category_name']); ?>
                                </a>
                            </h5>
                            <a href="subcatview.php?categoryid=<?php echo (int)$r['category_id']; ?>" class="btn btn-sm btn-outline-warning text-dark font-weight-bold" style="background: rgba(215, 173, 105, 0.2); border-color:#d7ad69;">
                                Explore Subcategories <i class="fa-solid fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php 
                } 
            } else { 
            ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No categories available at the moment.</p>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<?php include("footer.php"); ?>
