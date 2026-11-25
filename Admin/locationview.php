<?php 
include("header.php"); 
include_once("../dboperation.php");
$obj = new dboperation(); 
$sql = "SELECT * FROM tbl_district ORDER BY district_name ASC"; 
$result = $obj->executequery($sql); 
?> 
<div class="main-panel"> 
    <div class="content"> 
        <div class="container-fluid"> 
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h4 class="page-title mb-0">Location Management</h4>
                <a href="location.php" class="btn btn-success">
                    <i class="fa-solid fa-plus mr-1"></i> Add Location
                </a>
            </div>

            <div class="card"> 
                <div class="card-header"> 
                    <h4 class="card-title">View Locations by District</h4> 
                    <p class="card-subtitle">Filter registered localities based on district</p> 
                </div> 
                <div class="card-body"> 
                    <div class="form-group mb-4" style="max-width: 450px;"> 
                        <label for="districtid" class="font-weight-bold">Select District</label> 
                        <select class="form-control" name="districtid" id="districtid"> 
                            <option value="">-- Choose District to Filter --</option> 
                            <?php while ($r = mysqli_fetch_array($result)) { ?> 
                                <option value="<?php echo (int)$r["district_id"]; ?>"> 
                                    <?php echo htmlspecialchars($r["district_name"]); ?> 
                                </option> 
                            <?php } ?> 
                        </select> 
                    </div> 

                    <div class="table-responsive"> 
                        <table class="table table-hover mb-0"> 
                            <thead> 
                                <tr> 
                                    <th style="width: 80px;">#</th> 
                                    <th>Location Name</th> 
                                    <th style="width: 180px; text-align: center;">Actions</th> 
                                </tr> 
                            </thead> 
                            <tbody id="location"> 
                                <tr> 
                                    <td colspan="3" class="text-center py-4 text-muted"> 
                                        <i class="fa-solid fa-arrow-up mb-2" style="font-size: 24px; display: block; color: var(--admin-gold);"></i>
                                        Please select a district above to view its registered locations.
                                    </td> 
                                </tr> 
                            </tbody> 
                        </table> 
                    </div> 
                </div> 
            </div> 
        </div> 
    </div> 
</div> 

<script> 
$(document).ready(function () { 
    $("#districtid").change(function () { 
        var district_id = $(this).val(); 
        if (district_id === "") { 
            $("#location").html('<tr><td colspan="3" class="text-center py-4 text-muted">Please select a district above to view locations.</td></tr>'); 
            return; 
        } 
        $("#location").html('<tr><td colspan="3" class="text-center py-4 text-muted"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Loading locations...</td></tr>'); 
        $.ajax({ 
            url: "getlocation.php", 
            method: "POST", 
            data: { districtid: district_id }, 
            success: function (response) { 
                if ($.trim(response) === "") {
                    $("#location").html('<tr><td colspan="3" class="text-center py-4 text-muted">No locations found in this district.</td></tr>');
                } else {
                    $("#location").html(response); 
                }
            }, 
            error: function () { 
                $("#location").html('<tr><td colspan="3" class="text-center py-4 text-danger"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Unable to load locations. Please try again.</td></tr>'); 
            } 
        }); 
    }); 
}); 
</script> 
<?php include("footer.php"); ?>