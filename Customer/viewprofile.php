<?php
include("header.php");
include_once("../dboperation.php");
$obj = new dboperation();

$customerid = (int)$_SESSION['customer_id'];

// Fetch customer details joined with location and district
$sql = "SELECT c.*, l.location_name, d.district_name 
        FROM tbl_customer c 
        LEFT JOIN tbl_location l ON l.location_id = c.location_id 
        LEFT JOIN tbl_district d ON d.district_id = l.district_id 
        WHERE c.customer_id = '$customerid'";
$res = $obj->executequery($sql);
$r = mysqli_fetch_assoc($res);

if (!$r) {
    echo "<div class='container py-5'><div class='alert alert-warning text-center'>Unable to load customer profile.</div></div>";
    include("footer.php");
    exit();
}

$initial = strtoupper(substr($r['customer_name'] ?? 'M', 0, 1));
$isUpdated = isset($_GET['updated']) && $_GET['updated'] == '1';
?>

<div class="profile-wrapper-luxury">
    <div class="container">

        <!-- Update Success Notification -->
        <?php if ($isUpdated): ?>
            <div class="alert-pill-success mx-auto" style="max-width: 820px;">
                <i class="fa-solid fa-circle-check fa-lg text-success"></i>
                <div>
                    <strong>Profile Updated!</strong> Your customer information and account preferences were saved successfully.
                </div>
            </div>
        <?php endif; ?>

        <div class="profile-card-luxury">
            <!-- Luxury Banner Header -->
            <div class="profile-banner-header">
                <span class="badge badge-warning px-3 py-1 mb-2 text-dark font-weight-bold" style="background: #d7ad69; letter-spacing: 1px;">
                    <i class="fa-solid fa-id-card mr-1"></i> MUSICIAN MEMBERSHIP
                </span>
                <h2 style="font-family: 'Playfair Display', Georgia, serif; font-size: 34px; font-weight: 700; margin-bottom: 5px;">
                    <?php echo htmlspecialchars($r['customer_name']); ?>
                </h2>
                <p class="text-white-50 small mb-0">
                    <i class="fa-solid fa-circle-check text-success mr-1"></i> Verified MelodyMart Musician &bull; Active Customer Account
                </p>
            </div>

            <!-- Profile Avatar Badge -->
            <div class="text-center">
                <div class="profile-avatar-large">
                    <?php echo $initial; ?>
                </div>
            </div>

            <!-- Edit Profile & Quick Actions -->
            <div class="text-center mt-3 mb-2">
                <a href="profileedit.php?cid=<?php echo (int)$r['customer_id']; ?>" class="btn-hero-gold px-4 py-2">
                    <i class="fa-solid fa-user-pen mr-1"></i> Edit Profile
                </a>
                <a href="cartview.php" class="btn btn-outline-dark ml-2 px-3 py-2" style="border-radius: 10px; font-weight: 600;">
                    <i class="fa-solid fa-cart-shopping mr-1"></i> My Cart
                </a>
            </div>

            <!-- Profile Details Grid -->
            <div class="profile-detail-grid">
                <!-- Full Name -->
                <div class="profile-detail-item">
                    <div class="item-icon">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div>
                        <div class="item-label">Customer Name</div>
                        <div class="item-val"><?php echo htmlspecialchars($r['customer_name']); ?></div>
                    </div>
                </div>

                <!-- Email Address -->
                <div class="profile-detail-item">
                    <div class="item-icon">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div>
                        <div class="item-label">Email Address</div>
                        <div class="item-val"><?php echo htmlspecialchars($r['email']); ?></div>
                    </div>
                </div>

                <!-- Phone Number -->
                <div class="profile-detail-item">
                    <div class="item-icon">
                        <i class="fa-solid fa-phone"></i>
                    </div>
                    <div>
                        <div class="item-label">Phone Number</div>
                        <div class="item-val"><?php echo htmlspecialchars($r['phone_no']); ?></div>
                    </div>
                </div>

                <!-- Location / District -->
                <div class="profile-detail-item">
                    <div class="item-icon">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <div class="item-label">Location &amp; District</div>
                        <div class="item-val">
                            <?php 
                            $loc = !empty($r['location_name']) ? htmlspecialchars($r['location_name']) : 'Not set';
                            $dist = !empty($r['district_name']) ? htmlspecialchars($r['district_name']) : '';
                            echo $dist ? "$loc, $dist" : $loc;
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Username -->
                <div class="profile-detail-item">
                    <div class="item-icon">
                        <i class="fa-solid fa-id-badge"></i>
                    </div>
                    <div>
                        <div class="item-label">Login Username</div>
                        <div class="item-val"><?php echo htmlspecialchars($r['username']); ?></div>
                    </div>
                </div>

                <!-- Member Since -->
                <div class="profile-detail-item">
                    <div class="item-icon">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div>
                        <div class="item-label">Member Since</div>
                        <div class="item-val">
                            <?php 
                            echo !empty($r['reg_date']) ? date('M d, Y', strtotime($r['reg_date'])) : 'Verified Member'; 
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Card Options -->
            <div class="p-3 bg-light border-top text-center d-flex justify-content-between align-items-center flex-wrap px-4">
                <span class="text-muted small">
                    <i class="fa-solid fa-shield-halved mr-1 text-success"></i> Your account information is protected by MelodyMart encryption.
                </span>
                <a href="profileedit.php?cid=<?php echo (int)$r['customer_id']; ?>" class="text-dark font-weight-bold small">
                    <i class="fa-solid fa-user-pen mr-1 text-warning"></i> Need to change password or contact info? Edit here &rarr;
                </a>
            </div>
        </div>

    </div>
</div>

<?php
include("footer.php");
?>