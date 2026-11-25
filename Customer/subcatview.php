<?php 
include("header.php");
include_once("../dboperation.php");
$obj = new dboperation();
$catid = isset($_GET['categoryid']) ? (int)$_GET['categoryid'] : (isset($_GET['catid']) ? (int)$_GET['catid'] : 0);
$s = "SELECT * FROM tbl_subcategory WHERE category_id = '$catid' ORDER BY name ASC";
$res = $obj->executequery($s);

// Category title
$catName = "Instruments";
$qCat = $obj->executequery("SELECT category_name FROM tbl_category WHERE category_id = '$catid'");
if ($qCat && $rCat = mysqli_fetch_assoc($qCat)) {
    $catName = $rCat['category_name'];
}
?>	

<br><br><br>

<section class="section_gap">
    <div class="container">
        <!-- Section title -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-7 text-center">
                <div class="section-title">
                    <h1><?php echo htmlspecialchars($catName); ?> Subcategories</h1>
                    <p>Select an instrument type to browse models, brands, and availability</p>
                </div>
            </div>
        </div>

        <!-- Subcategories Grid -->
        <div class="row">
            <?php 
            if ($res && mysqli_num_rows($res) > 0) {
                while($r = mysqli_fetch_array($res)) { 
                    $subId = (int)$r['subcategory_id'];
                    $subName = htmlspecialchars($r['name']);
                    $subImg = htmlspecialchars($r['image']);
                    $subDesc = !empty($r['description']) ? htmlspecialchars($r['description']) : '';
            ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 rounded overflow-hidden" style="transition: transform 0.25s ease;">
                        <a href="productview.php?subcatid=<?php echo $subId; ?>">
                            <div style="width:100%; height:200px; overflow:hidden; background:#f5f5f5;">
                                <img src="../uploads/<?php echo $subImg; ?>" 
                                     alt="<?php echo $subName; ?>" 
                                     class="card-img-top" 
                                     style="width:100%; height:100%; object-fit:cover;"
                                     onerror="this.src='../Guest/ins.jpg'">
                            </div>
                        </a>
                        <div class="card-body text-center p-3 d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="card-title font-weight-bold mb-1">
                                    <a href="productview.php?subcatid=<?php echo $subId; ?>" class="text-dark">
                                        <?php echo $subName; ?>
                                    </a>
                                </h5>
                                <?php if (!empty($subDesc)): ?>
                                    <p class="text-muted small mb-3" style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?php echo $subDesc; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div>
                                <a href="productview.php?subcatid=<?php echo $subId; ?>" class="btn btn-sm btn-outline-warning text-dark font-weight-bold" style="background: rgba(215, 173, 105, 0.2); border-color:#d7ad69;">
                                    View Products <i class="fa-solid fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
                }
            } else { 
            ?>
                <div class="col-12 text-center py-5">
                    <div class="p-5 bg-white rounded shadow-sm">
                        <i class="fa-solid fa-music mb-3 text-warning" style="font-size:36px;"></i>
                        <h4>No subcategories found in this category.</h4>
                        <p class="text-muted">Explore other categories or check back soon.</p>
                        <a href="catview.php" class="btn btn-primary mt-2">All Categories</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<?php include("footer.php"); ?>
