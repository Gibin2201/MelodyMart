<?php
include_once('header.php');
include_once('../dboperation.php');
$obj = new dboperation();

$r = ['district_name' => '', 'district_id' => 0];
if (isset($_GET["did"])) {
    $did = (int)$_GET["did"];
    $sql = "SELECT * FROM tbl_district WHERE district_id='$did'";
    $res = $obj->executequery($sql);
    if ($res && $row = mysqli_fetch_array($res)) {
        $r = $row;
    }
}
?>
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">District Management</h4>
            <div class="row">
                <div class="col-md-7 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Edit District</h4>
                            <p class="card-subtitle">Update regional territory name</p>
                        </div>
                        <form action="districteditaction.php" method="POST">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="districtname">District Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="districtname" name="districtname" placeholder="Enter district" value="<?php echo htmlspecialchars($r["district_name"]); ?>" required>
                                </div>
                            </div>
                            <div class="card-action">
                                <input type="hidden" name="district_id" value="<?php echo (int)$r["district_id"]; ?>">
                                <button type="submit" name="submit" class="btn btn-success">
                                    <i class="fa-solid fa-check mr-1"></i> Update District
                                </button>
                                <a href="districtview.php" class="btn btn-danger">
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