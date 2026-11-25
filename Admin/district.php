<?php include("header.php"); ?>
<div class="main-panel"> 
    <div class="content"> 
        <div class="container-fluid"> 
            <h4 class="page-title">District Management</h4> 
            <div class="row">
                <div class="col-md-7 col-lg-6">
                    <div class="card"> 
                        <div class="card-header"> 
                            <h4 class="card-title">Add New District</h4> 
                            <p class="card-subtitle">Register a new regional delivery & store territory</p> 
                        </div> 
                        <form action="districtaction.php" method="POST"> 
                            <div class="card-body"> 
                                <div class="form-group"> 
                                    <label for="districtname">District Name <span class="text-danger">*</span></label> 
                                    <input type="text" class="form-control" id="districtname" name="districtname" placeholder="Enter district name" maxlength="100" required> 
                                    <small class="text-muted d-block mt-1">Provide the official district name.</small> 
                                </div> 
                            </div> 
                            <div class="card-action"> 
                                <button type="submit" name="submit" class="btn btn-success">
                                    <i class="fa-solid fa-check mr-1"></i> Save District
                                </button> 
                                <button type="reset" class="btn btn-danger">
                                    <i class="fa-solid fa-rotate-left mr-1"></i> Clear
                                </button> 
                                <a href="districtview.php" class="btn btn-outline-secondary ml-auto">
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
<?php include("footer.php"); ?>