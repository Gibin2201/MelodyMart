<?php include("header.php"); ?>
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Category Management</h4>
            <div class="row">
                <div class="col-md-7 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Add New Category</h4>
                            <p class="card-subtitle">Create and register a new musical instrument department</p>
                        </div>
                        <form action="categoryaction.php" method="POST" enctype="multipart/form-data">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="categoryname">Category Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="categoryname" name="categoryname" placeholder="e.g. String Instruments, Keyboards, Percussion" required>
                                </div>
                                <div class="form-group">
                                    <label for="image">Category Image <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                                    <small class="text-muted d-block mt-1">Recommended: high resolution JPG/PNG representation.</small>
                                </div>
                            </div>
                            <div class="card-action">
                                <button type="submit" name="submit" class="btn btn-success">
                                    <i class="fa-solid fa-check mr-1"></i> Save Category
                                </button>
                                <button type="reset" class="btn btn-danger">
                                    <i class="fa-solid fa-rotate-left mr-1"></i> Clear
                                </button>
                                <a href="categoryview.php" class="btn btn-outline-secondary ml-auto">
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