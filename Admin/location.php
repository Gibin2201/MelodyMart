<?php
include_once('header.php');
include_once("../dboperation.php");
$obj = new dboperation();
$sql = "SELECT * FROM tbl_district ORDER BY district_name ASC";
$res = $obj->executequery($sql);
?>
<div class="main-panel"> 
    <div class="content"> 
        <div class="container-fluid"> 
            <h4 class="page-title">Location Management</h4>
            <div class="row">
                <div class="col-md-7 col-lg-6">
                    <div class="card"> 
                        <div class="card-header"> 
                            <h4 class="card-title">Add New Location</h4> 
                            <p class="card-subtitle">Select a district and specify the localized area/town</p> 
                        </div> 
                        <form action="locationaction.php" method="POST"> 
                            <div class="card-body"> 
                                <div class="form-group"> 
                                    <label for="seldistrictid">Select District <span class="text-danger">*</span></label> 
                                    <select class="form-control" name="districtid" id="seldistrictid" required> 
                                        <option value="">-- Select District --</option> 
                                        <?php while ($display = mysqli_fetch_array($res)) { ?> 
                                            <option value="<?php echo (int)$display["district_id"]; ?>"> 
                                                <?php echo htmlspecialchars($display["district_name"]); ?> 
                                            </option> 
                                        <?php } ?> 
                                    </select> 
                                    <small class="text-muted d-block mt-1">Assign this location to a registered district.</small>
                                </div> 
                                <div class="form-group"> 
                                    <label for="locationname">Location Name <span class="text-danger">*</span></label> 
                                    <input type="text" class="form-control" name="locationname" id="locationname" placeholder="e.g. Downtown, West End, Central Square" maxlength="100" required> 
                                </div> 
                            </div> 
                            <div class="card-action"> 
                                <button type="submit" name="submit" class="btn btn-success">
                                    <i class="fa-solid fa-check mr-1"></i> Save Location
                                </button> 
                                <button type="reset" class="btn btn-danger">
                                    <i class="fa-solid fa-rotate-left mr-1"></i> Clear
                                </button> 
                                <a href="locationview.php" class="btn btn-outline-secondary ml-auto">
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