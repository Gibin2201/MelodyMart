<?php
include("header.php");
include_once('../dboperation.php');
$obj = new dboperation();

// Get customer ID from GET or fallback to active session
$cid = isset($_GET["cid"]) && !empty($_GET["cid"]) ? (int)$_GET["cid"] : (int)$_SESSION['customer_id'];

// Fetch customer data joined with current location and district
$sqlCustomer = "SELECT c.*, l.location_name, l.district_id, d.district_name 
                FROM tbl_customer c 
                LEFT JOIN tbl_location l ON c.location_id = l.location_id 
                LEFT JOIN tbl_district d ON l.district_id = d.district_id 
                WHERE c.customer_id = '$cid'";
$resCustomer = $obj->executequery($sqlCustomer);
$r = mysqli_fetch_assoc($resCustomer);

if (!$r) {
    echo "<div class='container py-5'><div class='alert alert-warning text-center'>Customer record not found. <a href='index.php'>Return Home</a></div></div>";
    include("footer.php");
    exit();
}

// Fetch all districts for the dropdown
$sqlDistricts = "SELECT * FROM tbl_district ORDER BY district_name ASC";
$resDistricts = $obj->executequery($sqlDistricts);

// Fetch locations for current customer's district (if exists)
$currentDistrictId = !empty($r['district_id']) ? (int)$r['district_id'] : 0;
$resLocations = null;
if ($currentDistrictId > 0) {
    $resLocations = $obj->executequery("SELECT * FROM tbl_location WHERE district_id = '$currentDistrictId' ORDER BY location_name ASC");
}
?>

<div class="profile-wrapper-luxury">
    <div class="container">
        
        <div class="profile-card-luxury">
            <!-- Luxury Banner Header -->
            <div class="profile-banner-header">
                <span class="badge badge-warning px-3 py-1 mb-2 text-dark font-weight-bold" style="background: #d7ad69; letter-spacing: 1px;">
                    <i class="fa-solid fa-user-gear mr-1"></i> ACCOUNT SETTINGS
                </span>
                <h2 style="font-family: 'Playfair Display', Georgia, serif; font-size: 32px; font-weight: 700; margin-bottom: 5px;">
                    Edit Musician Profile
                </h2>
                <p class="text-white-50 small mb-0">Update your personal contact information, delivery location, and credentials.</p>
            </div>

            <!-- Profile Avatar Badge -->
            <div class="text-center">
                <div class="profile-avatar-large">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
            </div>

            <!-- Profile Edit Form -->
            <form action="profileeditaction.php" method="POST" class="profile-form-luxury">
                <input type="hidden" name="Customer_id" value="<?php echo htmlspecialchars((string)$r['customer_id']); ?>">

                <div class="row">
                    <!-- Full Name -->
                    <div class="col-md-6 form-group">
                        <label for="customername">
                            <i class="fa-solid fa-user"></i> Full Name
                        </label>
                        <input type="text" 
                               class="form-control-luxury" 
                               id="customername" 
                               name="customername" 
                               required 
                               placeholder="e.g. John Doe" 
                               value="<?php echo htmlspecialchars($r['customer_name']); ?>">
                    </div>

                    <!-- Phone Number -->
                    <div class="col-md-6 form-group">
                        <label for="phonenumber">
                            <i class="fa-solid fa-phone"></i> Phone Number
                        </label>
                        <input type="tel" 
                               class="form-control-luxury" 
                               id="phonenumber" 
                               name="phonenumber" 
                               required 
                               placeholder="10-digit mobile number" 
                               value="<?php echo htmlspecialchars($r['phone_no']); ?>">
                    </div>
                </div>

                <div class="row">
                    <!-- Email Address -->
                    <div class="col-md-12 form-group">
                        <label for="email">
                            <i class="fa-solid fa-envelope"></i> Email Address
                        </label>
                        <input type="email" 
                               class="form-control-luxury" 
                               id="email" 
                               name="email" 
                               required 
                               placeholder="e.g. musician@example.com" 
                               value="<?php echo htmlspecialchars($r['email']); ?>">
                    </div>
                </div>

                <div class="row">
                    <!-- District Selection -->
                    <div class="col-md-6 form-group">
                        <label for="districtid">
                            <i class="fa-solid fa-city"></i> District / Region
                        </label>
                        <select class="form-control-luxury" id="districtid" name="districtid">
                            <option value="">-- Choose District --</option>
                            <?php if ($resDistricts): ?>
                                <?php while ($dist = mysqli_fetch_assoc($resDistricts)): ?>
                                    <option value="<?php echo $dist['district_id']; ?>" <?php echo ($dist['district_id'] == $currentDistrictId) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dist['district_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Dependent Location Selection -->
                    <div class="col-md-6 form-group">
                        <label for="locationid">
                            <i class="fa-solid fa-location-dot"></i> City / Town Location
                        </label>
                        <select class="form-control-luxury" id="locationid" name="locationid">
                            <option value="">-- Choose Location --</option>
                            <?php if ($resLocations): ?>
                                <?php while ($loc = mysqli_fetch_assoc($resLocations)): ?>
                                    <option value="<?php echo $loc['location_id']; ?>" <?php echo ($loc['location_id'] == $r['location_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($loc['location_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <!-- Username -->
                    <div class="col-md-6 form-group">
                        <label for="username">
                            <i class="fa-solid fa-id-badge"></i> Account Username
                        </label>
                        <input type="text" 
                               class="form-control-luxury" 
                               id="username" 
                               name="username" 
                               required 
                               placeholder="Choose username" 
                               value="<?php echo htmlspecialchars($r['username']); ?>">
                    </div>

                    <!-- Password with Toggle -->
                    <div class="col-md-6 form-group">
                        <label for="password">
                            <i class="fa-solid fa-lock"></i> Account Password
                        </label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control-luxury" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   placeholder="Enter password" 
                                   value="<?php echo htmlspecialchars($r['password']); ?>"
                                   style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px; border-color: #dcd8cc;">
                                    <i class="fa-solid fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 pt-3 border-top">
                    <a href="viewprofile.php" class="btn-cancel-profile">
                        <i class="fa-solid fa-arrow-left"></i> Back to Profile
                    </a>
                    <button type="submit" name="submit" class="btn-save-profile">
                        <i class="fa-solid fa-floppy-disk"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<!-- AJAX Dynamic Location Loading Script & Password Toggle -->
<script src="../jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Dependent location dropdown
    $('#districtid').on('change', function() {
        var districtId = $(this).val();
        if (districtId) {
            $.ajax({
                type: 'POST',
                url: 'getlocation.php',
                data: { id: districtId },
                success: function(html) {
                    $('#locationid').html(html);
                },
                error: function() {
                    $('#locationid').html('<option value="">Error loading locations</option>');
                }
            });
        } else {
            $('#locationid').html('<option value="">-- Choose Location --</option>');
        }
    });

    // Password visibility toggle
    $('#togglePasswordBtn').on('click', function() {
        var passInput = $('#password');
        var icon = $('#toggleIcon');
        if (passInput.attr('type') === 'password') {
            passInput.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passInput.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
});
</script>

<?php
include("footer.php");
?>