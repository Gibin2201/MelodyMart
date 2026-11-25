<?php 
include("header.php");
include_once("../dboperation.php");
$obj = new dboperation();

$subcatid = isset($_GET['subcatid']) ? (int)$_GET['subcatid'] : 0;
// In tbl_product, the subcategory foreign key is `subcat`
$s = "SELECT * FROM tbl_product WHERE subcat = '$subcatid'";
$res = $obj->executequery($s);

// Fetch subcategory name if possible
$subcatName = "Instruments";
$subQuery = $obj->executequery("SELECT name FROM tbl_subcategory WHERE subcategory_id = '$subcatid'");
if ($subQuery && $rowSub = mysqli_fetch_assoc($subQuery)) {
    $subcatName = $rowSub['name'];
}
?>	
<br><br><br>

<section class="section_gap">
    <div class="container">
        <!-- Section title -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <div class="section-title">
                    <h1><?php echo htmlspecialchars($subcatName); ?></h1>
                    <p>Browse authentic musical instruments available in this collection</p>
                </div>
            </div>
        </div>

        <div class="row">
            <?php 
            if ($res && mysqli_num_rows($res) > 0) {
                while($r = mysqli_fetch_array($res)) { 
            ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 rounded overflow-hidden">
                        <a href="ptview.php?productid=<?php echo (int)$r['product_id']; ?>"> 
                            <div style="width:100%; height:220px; overflow:hidden; background:#f9f9f9;" class="d-flex align-items-center justify-content-center">
                                <img src="../uploads/<?php echo htmlspecialchars($r['image']); ?>" 
                                     class="img-fluid" 
                                     alt="<?php echo htmlspecialchars($r['product_name']); ?>"
                                     style="max-height:100%; object-fit:contain; padding:10px;"
                                     onerror="this.src='../Guest/ins.jpg'">
                            </div>
                        </a>
                        <div class="card-body text-center d-flex flex-column justify-content-between p-3">
                            <div>
                                <span class="badge badge-warning text-dark mb-2" style="background:#d7ad69;">
                                    <?php echo !empty($r['brand']) ? htmlspecialchars($r['brand']) : 'MelodyMart'; ?>
                                </span>
                                <h5 class="card-title font-weight-bold mb-1">
                                    <a href="ptview.php?productid=<?php echo (int)$r['product_id']; ?>" class="text-dark">
                                        <?php echo htmlspecialchars($r['product_name']); ?>
                                    </a>
                                </h5>
                                <p class="text-success font-weight-bold mb-3" style="font-size:16px;">
                                    ₹<?php echo number_format((float)$r['amount'], 2); ?>
                                </p>
                            </div>
                            <div>
                                <a href="ptview.php?productid=<?php echo (int)$r['product_id']; ?>" class="btn btn-outline-dark btn-block btn-sm">
                                    <i class="fa-solid fa-cart-shopping mr-1"></i> View &amp; Buy
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
                        <i class="fa-solid fa-guitar mb-3 text-warning" style="font-size:40px;"></i>
                        <h4>No instruments found in this subcategory</h4>
                        <p class="text-muted">Explore our other instrument departments or browse our featured collections.</p>
                        <a href="catview.php" class="btn btn-primary mt-2">Browse All Categories</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<?php include("footer.php"); ?>
