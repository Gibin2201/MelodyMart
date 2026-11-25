<?php
include_once('header.php');
include_once('../dboperation.php');
$obj = new dboperation();

$r = ['location_name' => '', 'location_id' => 0];
if (isset($_GET["lid"])) {
    $lid = (int)$_GET["lid"];
    $sql = "SELECT * FROM tbl_location WHERE location_id='$lid'";
    $res = $obj->executequery($sql);
    if ($res && $row = mysqli_fetch_array($res)) {
        $r = $row;
    }
}
?>
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Location Management</h4>
            <div class="row">
                <div class="col-md-7 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Edit Location</h4>
                            <p class="card-subtitle">Update locality name details</p>
                        </div>
                        <form action="locationeditaction.php" method="POST">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="locationname">Location Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="locationname" name="locationname" placeholder="Enter Location" value="<?php echo htmlspecialchars($r["location_name"]); ?>" required>
                                </div>
                            </div>
                            <div class="card-action">
                                <input type="hidden" name="location_id" value="<?php echo (int)$r["location_id"]; ?>">
                                <button type="submit" name="submit" class="btn btn-success">
                                    <i class="fa-solid fa-check mr-1"></i> Update Location
                                </button>
                                <a href="locationview.php" class="btn btn-danger">
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