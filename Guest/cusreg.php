<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, redirect to customer dashboard
if (isset($_SESSION['customer_id'])) {
    header("Location: ../Customer/index.php");
    exit();
}

include_once("../dboperation.php");
$obj = new dboperation();
$sql = "SELECT * FROM tbl_district ORDER BY district_name ASC";
$districtResult = $obj->executequery($sql);

$pageTitle = "Customer Registration | MelodyMart - Premium Musical Instruments";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Join MelodyMart today to browse world-class musical instruments, manage orders, and enjoy musician support.">

    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Custom Guest Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- jQuery for AJAX Dependent Dropdowns -->
    <script src="../jquery-3.6.0.min.js"></script>
</head>
<body>

<!-- NAVBAR -->
<header class="navbar">
    <div class="container nav-inner">
        <a href="index.php" class="logo">
            <span class="logo-icon"><i class="fa-solid fa-music"></i></span>
            <span class="logo-name">Melody<span>Mart</span></span>
        </a>

        <div class="nav-actions">
            <a href="index.php" class="btn btn-outline"><i class="fa-solid fa-house"></i> Home</a>
            <a href="login.php" class="btn btn-gold"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
        </div>
    </div>
</header>

<!-- MAIN REGISTRATION SECTION -->
<main class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-card auth-card-reg">

            <!-- VISUAL SHOWCASE SIDE -->
            <div class="auth-visual visual-reg">
                <div class="auth-visual-top">
                    <span class="brand-badge"><i class="fa-solid fa-sparkles"></i> Musician Community</span>
                    <div class="auth-visual-content">
                        <h2>Start Your Sound Journey <span>With Us.</span></h2>
                        <p>Create your personal MelodyMart account to discover artisanal instruments, save favorite gear, and experience musician-tested setups.</p>

                        <ul class="auth-perks">
                            <li class="auth-perk-item">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Verified Authentic Manufacturer Warranties</span>
                            </li>
                            <li class="auth-perk-item">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Personalized Setup & Tuner Consultation</span>
                            </li>
                            <li class="auth-perk-item">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Express Shock-Proof Protective Packaging</span>
                            </li>
                            <li class="auth-perk-item">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Fast Tracked Delivery To Your Doorstep</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="auth-visual-footer">
                    <span>Crafted for Musicians Worldwide</span>
                    <span>MelodyMart &copy; <?= date('Y') ?></span>
                </div>
            </div>

            <!-- REGISTRATION FORM SIDE -->
            <div class="auth-form-wrap">
                <div class="auth-header">
                    <span class="eyebrow"><i class="fa-solid fa-user-plus"></i> NEW ACCOUNT</span>
                    <h1>Customer Registration</h1>
                    <p>Complete the form below to register your customer profile.</p>
                </div>

                <form action="cusregaction.php" method="POST" enctype="multipart/form-data" class="auth-form">

                    <!-- Customer Full Name -->
                    <div class="form-group">
                        <label class="form-label" for="custname">Full Name</label>
                        <div class="input-box">
                            <i class="fa-solid fa-user input-icon"></i>
                            <input
                                type="text"
                                id="custname"
                                name="custname"
                                class="form-input"
                                placeholder="e.g. John Doe"
                                pattern="[A-Z][a-zA-Z\s]*"
                                title="Name must start with a capital letter and contain only letters and spaces"
                                required
                                autofocus>
                        </div>
                    </div>

                    <!-- Phone and Email Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="phoneno">Phone Number</label>
                            <div class="input-box">
                                <i class="fa-solid fa-phone input-icon"></i>
                                <input
                                    type="tel"
                                    id="phoneno"
                                    name="phoneno"
                                    class="form-input"
                                    placeholder="10-digit mobile"
                                    pattern="[6-9][0-9]{9}"
                                    title="Enter a valid 10-digit mobile number starting with 6, 7, 8, or 9"
                                    required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Email Address</label>
                            <div class="input-box">
                                <i class="fa-solid fa-envelope input-icon"></i>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-input"
                                    placeholder="name@example.com"
                                    required>
                            </div>
                        </div>
                    </div>

                    <!-- District and Location Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="districtid">District</label>
                            <div class="input-box">
                                <i class="fa-solid fa-city input-icon"></i>
                                <select class="form-select-custom" id="districtid" name="districtid" required>
                                    <option value="" selected>Choose Your District</option>
                                    <?php if ($districtResult): ?>
                                        <?php while ($d = mysqli_fetch_array($districtResult)): ?>
                                            <option value="<?= htmlspecialchars((string)$d['district_id']) ?>">
                                                <?= htmlspecialchars($d['district_name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="locationid">Location</label>
                            <div class="input-box">
                                <i class="fa-solid fa-location-dot input-icon"></i>
                                <select class="form-select-custom" id="locationid" name="locationid" required>
                                    <option value="" selected>Choose Your Location</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Username and Password Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="username">Username</label>
                            <div class="input-box">
                                <i class="fa-solid fa-id-badge input-icon"></i>
                                <input
                                    type="text"
                                    id="username"
                                    name="username"
                                    class="form-input"
                                    placeholder="Choose username"
                                    required
                                    autocomplete="username">
                            </div>
                            <div id="username-status" style="font-size: 12.5px; margin-top: 5px; font-weight: 500;"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-box">
                                <i class="fa-solid fa-lock input-icon"></i>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-input"
                                    placeholder="At least 6 chars"
                                    pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$"
                                    title="At least 6 characters, including 1 letter, 1 number, and 1 special character"
                                    required
                                    autocomplete="new-password">
                                <i class="fa-regular fa-eye toggle-password" id="togglePassword" title="Show/Hide Password"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="form-actions">
                        <button type="reset" class="btn btn-outline" style="color: var(--text); border-color: #d0cdc3;">
                            <i class="fa-solid fa-rotate-left"></i> Reset
                        </button>
                        <button type="submit" name="submit" class="btn btn-gold btn-submit">
                            Register Now <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>

                </form>

                <div class="auth-switch-text">
                    Already have an account?
                    <a href="login.php">Sign In Here</a>
                </div>
            </div>

        </div>
    </div>
</main>

<script>
    // AJAX Location Dropdown based on District selection
    $(document).ready(function () {
        $("#districtid").change(function () {
            var districtid = $(this).val();
            if (!districtid) {
                $("#locationid").html("<option value=''>Choose Your Location</option>");
                return;
            }

            $.ajax({
                url: "getloc_drop.php",
                method: "POST",
                data: { districtid: districtid },
                success: function (response) {
                    $("#locationid").html("<option value=''>Choose Your Location</option>" + response);
                },
                error: function () {
                    $("#locationid").html("<option value=''>Error loading locations</option>");
                }
            });
        });

        // Password visibility toggle
        const toggleBtn = document.getElementById('togglePassword');
        const pwdInput = document.getElementById('password');

        if (toggleBtn && pwdInput) {
            toggleBtn.addEventListener('click', () => {
                const isPassword = pwdInput.type === 'password';
                pwdInput.type = isPassword ? 'text' : 'password';
                toggleBtn.classList.toggle('fa-eye', !isPassword);
                toggleBtn.classList.toggle('fa-eye-slash', isPassword);
            });
        }

        // Live AJAX Username Uniqueness Verification
        let userCheckTimer = null;
        $("#username").on("input change", function () {
            clearTimeout(userCheckTimer);
            const usernameVal = $(this).val().trim();
            const statusDiv = $("#username-status");
            const submitBtn = $(".btn-submit");

            if (usernameVal.length < 3) {
                statusDiv.html("");
                submitBtn.prop("disabled", false);
                return;
            }

            statusDiv.html("<span style='color: #8b8e86;'><i class='fa-solid fa-spinner fa-spin mr-1'></i> Checking availability...</span>");

            userCheckTimer = setTimeout(function () {
                $.ajax({
                    url: "check_username.php",
                    method: "POST",
                    dataType: "json",
                    data: { username: usernameVal },
                    success: function (data) {
                        if (data.status === "taken") {
                            statusDiv.html("<span style='color: #dc3545;'><i class='fa-solid fa-circle-xmark mr-1'></i> " + data.message + "</span>");
                            submitBtn.prop("disabled", true);
                        } else if (data.status === "available") {
                            statusDiv.html("<span style='color: #28a745;'><i class='fa-solid fa-circle-check mr-1'></i> " + data.message + "</span>");
                            submitBtn.prop("disabled", false);
                        } else {
                            statusDiv.html("");
                            submitBtn.prop("disabled", false);
                        }
                    },
                    error: function () {
                        statusDiv.html("");
                        submitBtn.prop("disabled", false);
                    }
                });
            }, 350);
        });
    });
</script>

</body>
</html>