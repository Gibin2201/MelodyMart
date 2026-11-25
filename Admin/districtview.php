<?php 
include("header.php");
include("../dboperation.php");
$obj = new dboperation();
$s = "SELECT * FROM tbl_district ORDER BY district_id DESC";
$res = $obj->executequery($s);
?>
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h4 class="page-title mb-0">District Management</h4>
                <a href="district.php" class="btn btn-success">
                    <i class="fa-solid fa-plus mr-1"></i> Add District
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Registered Districts</h4>
                    <p class="card-subtitle">View, edit, and manage geographical districts</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">#</th>
                                    <th>District Name</th>
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
                                                <?php echo htmlspecialchars($r["district_name"]); ?>
                                            </strong>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="districtedit.php?did=<?php echo (int)$r['district_id']; ?>" class="btn-district-edit mr-1" title="Edit District">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </a>
                                            <a href="deletedistrict.php?did=<?php echo (int)$r['district_id']; ?>" class="btn-district-delete" onclick="return confirm('Are you sure you want to delete this district?');" title="Delete District">
                                                <i class="fa-solid fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr> 
                                <?php 
                                    } 
                                } else { 
                                ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            <i class="fa-solid fa-map mb-2" style="font-size: 30px; display: block; color: var(--admin-gold);"></i>
                                            No districts found. Click "Add District" to create one.
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