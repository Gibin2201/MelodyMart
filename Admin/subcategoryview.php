<?php 
include("header.php"); 
include_once("../dboperation.php");
$obj = new dboperation(); 
$sql = "SELECT * FROM tbl_category ORDER BY category_name ASC"; 
$result = $obj->executequery($sql); 
?> 
<div class="main-panel"> 
    <div class="content"> 
        <div class="container-fluid"> 
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h4 class="page-title mb-0">Subcategory Management</h4>
                <a href="subcategory.php" class="btn btn-success">
                    <i class="fa-solid fa-plus mr-1"></i> Add Subcategory
                </a>
            </div>

            <div class="card"> 
                <div class="card-header"> 
                    <h4 class="card-title">View Subcategories by Category</h4> 
                    <p class="card-subtitle">Filter musical subcategories based on parent department</p> 
                </div> 
                <div class="card-body"> 
                    <div class="form-group mb-4" style="max-width: 450px;"> 
                        <label for="categoryid" class="font-weight-bold">Select Category</label> 
                        <select class="form-control" name="categoryid" id="categoryid"> 
                            <option value="">-- Choose Category to Filter --</option> 
                            <?php while ($r = mysqli_fetch_array($result)) { ?> 
                                <option value="<?php echo (int)$r["category_id"]; ?>"> 
                                    <?php echo htmlspecialchars($r["category_name"]); ?> 
                                </option> 
                            <?php } ?> 
                        </select> 
                    </div> 

                    <div class="table-responsive"> 
                        <table class="table table-hover mb-0"> 
                            <thead> 
                                <tr> 
                                    <th style="width: 70px;">#</th> 
                                    <th>Subcategory</th> 
                                    <th>Image</th> 
                                    <th>Description</th> 
                                    <th style="width: 170px; text-align: center;">Actions</th> 
                                </tr> 
                            </thead> 
                            <tbody id="subcategory"> 
                                <tr> 
                                    <td colspan="5" class="text-center py-4 text-muted"> 
                                        <i class="fa-solid fa-arrow-up mb-2" style="font-size: 24px; display: block; color: var(--admin-gold);"></i>
                                        Please select a category above to view its registered subcategories.
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
    $("#categoryid").change(function () { 
        var category_id = $(this).val(); 
        if (category_id === "") { 
            $("#subcategory").html('<tr><td colspan="5" class="text-center py-4 text-muted">Please select a category above to view its registered subcategories.</td></tr>');
            return; 
        } 
        $("#subcategory").html('<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Loading subcategories...</td></tr>'); 
        $.ajax({ 
            url: "getsubcategory.php", 
            method: "POST", 
            data: { categoryid: category_id }, 
            success: function (response) { 
                if ($.trim(response) === "") {
                    $("#subcategory").html('<tr><td colspan="5" class="text-center py-4 text-muted">No subcategories found in this category.</td></tr>');
                } else {
                    $("#subcategory").html(response); 
                }
            }, 
            error: function () { 
                $("#subcategory").html('<tr><td colspan="5" class="text-center py-4 text-danger"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Unable to load subcategories. Please try again.</td></tr>');
            } 
        }); 
    }); 
});
</script> 
<?php include("footer.php"); ?>