<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check customer authentication cleanly without annoying alert popups
if (!isset($_SESSION["customer_id"]) || empty($_SESSION["customer_id"])) {
    header("Location: ../Guest/login.php");
    exit();
}

$activeCustomerId = (int)$_SESSION["customer_id"];
$activeCustomerName = isset($_SESSION["username"]) ? htmlspecialchars($_SESSION["username"]) : "Customer";
$activeCustomerInitial = strtoupper(substr($activeCustomerName, 0, 1));
$currentPage = basename($_SERVER['PHP_SELF']);

// Live Cart Count for the logged-in customer
$cartCount = 0;
require_once(__DIR__ . "/../dboperation.php");
try {
    $dbHeader = new dboperation();
    $cartRes = $dbHeader->executequery("SELECT COUNT(*) as cnt FROM tbl_cart WHERE customer_id='$activeCustomerId' AND status='cart'");
    if ($cartRes && $rowC = mysqli_fetch_assoc($cartRes)) {
        $cartCount = (int)$rowC['cnt'];
    }
} catch (\Throwable $e) {
    $cartCount = 0;
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">

<head>
	<!-- Mobile Specific Meta -->
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- Favicon-->
	<link rel="shortcut icon" href="img/fav.png">
	<!-- Meta Description -->
	<meta name="description" content="MelodyMart - Customer Portal & Musical Instruments">
	<meta name="keywords" content="instruments, guitars, pianos, audio equipment">
	<meta charset="UTF-8">
	<!-- Site Title -->
	<title>MelodyMart | Customer Store</title>

	<!-- Google Fonts: DM Sans & Playfair Display -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">

	<!-- FontAwesome 6 Icons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

	<!-- CSS Stylesheets -->
	<link rel="stylesheet" href="css/linearicons.css">
	<link rel="stylesheet" href="css/themify-icons.css">
	<link rel="stylesheet" href="css/bootstrap.css">
	<link rel="stylesheet" href="css/owl.carousel.css">
	<link rel="stylesheet" href="css/nice-select.css">
	<link rel="stylesheet" href="css/nouislider.min.css">
	<link rel="stylesheet" href="css/ion.rangeSlider.css" />
	<link rel="stylesheet" href="css/ion.rangeSlider.skinFlat.css" />
	<link rel="stylesheet" href="css/magnific-popup.css">
	<link rel="stylesheet" href="css/main.css">

	<!-- MelodyMart Customer Luxury Theme CSS -->
	<link rel="stylesheet" href="css/customer-theme.css">
</head>

<body>

	<!-- Main Luxury Navigation Bar (Clean & Focused) -->
	<header class="navbar-melodymart">
		<div class="container">
			<div class="d-flex align-items-center justify-content-between">
				
				<!-- Brand Logo -->
				<a class="brand-logo" href="index.php">
					<span class="logo-icon-box">
						<i class="fa-solid fa-music"></i>
					</span>
					<span class="logo-title">
						Melody<span>Mart</span>
					</span>
				</a>

				<!-- Primary Navigation Links (Home, Categories, Orders, Warranty) -->
				<nav class="d-none d-lg-flex align-items-center" style="gap: 8px;">
					<a href="index.php" class="nav-link-mm <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
						<i class="fa-solid fa-house-chimney"></i> Home
					</a>
					<a href="catview.php" class="nav-link-mm <?php echo in_array($currentPage, ['catview.php', 'subcatview.php', 'productview.php', 'ptview.php']) ? 'active' : ''; ?>">
						<i class="fa-solid fa-layer-group"></i> Categories
					</a>
					<a href="vieworders.php" class="nav-link-mm <?php echo ($currentPage == 'vieworders.php') ? 'active' : ''; ?>">
						<i class="fa-solid fa-box-archive"></i> Orders
					</a>
					<a href="warrantyview.php" class="nav-link-mm <?php echo in_array($currentPage, ['warrantyview.php', 'warrantycard.php', 'warranty.php']) ? 'active' : ''; ?>">
						<i class="fa-solid fa-shield-halved"></i> Warranty
					</a>
				</nav>

				<!-- Right Action Hub: Live Cart & Customer Profile Dropdown -->
				<div class="d-flex align-items-center" style="gap: 14px;">

					<!-- Live Cart Counter Widget -->
					<a href="cartview.php" class="nav-cart-box" title="View Shopping Cart">
						<i class="fa-solid fa-cart-shopping"></i>
						<?php if ($cartCount > 0): ?>
							<span class="cart-badge-count"><?php echo $cartCount; ?></span>
						<?php endif; ?>
					</a>

					<!-- Customer Profile Pill & Dropdown -->
					<div class="dropdown">
						<a class="customer-profile-pill dropdown-toggle" href="#" role="button" id="customerUserMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<span class="customer-avatar-circle"><?php echo $activeCustomerInitial; ?></span>
							<span class="customer-pill-text d-none d-xl-block">
								<span class="customer-pill-name"><?php echo $activeCustomerName; ?></span>
								<span class="customer-pill-role">Musician</span>
							</span>
							<i class="fa-solid fa-chevron-down text-muted ml-1" style="font-size: 10px;"></i>
						</a>
						
						<div class="dropdown-menu dropdown-menu-right mm-dropdown-menu" aria-labelledby="customerUserMenu">
							<div class="mm-dropdown-header">
								<div class="user-title"><?php echo $activeCustomerName; ?></div>
								<div class="user-subtitle">
									<i class="fa-solid fa-circle-check text-success mr-1"></i> Verified Musician
								</div>
							</div>
							<a class="mm-dropdown-item" href="viewprofile.php">
								<i class="fa-solid fa-id-card text-warning"></i> View Profile
							</a>
							<a class="mm-dropdown-item highlight-edit" href="profileedit.php">
								<i class="fa-solid fa-user-pen text-info"></i> Edit Profile
							</a>
							<div class="mm-divider"></div>
							<a class="mm-dropdown-item" href="cartview.php">
								<i class="fa-solid fa-cart-shopping text-warning"></i> My Cart (<?php echo $cartCount; ?>)
							</a>
							<a class="mm-dropdown-item" href="vieworders.php">
								<i class="fa-solid fa-box-open text-primary"></i> Order History
							</a>
							<a class="mm-dropdown-item" href="warrantyview.php">
								<i class="fa-solid fa-shield-halved text-success"></i> Warranty Claims
							</a>
							<div class="mm-divider"></div>
							<a class="mm-dropdown-item text-danger font-weight-bold" href="../logout.php">
								<i class="fa-solid fa-power-off"></i> Logout
							</a>
						</div>
					</div>

					<!-- Mobile Hamburger Toggle -->
					<button class="navbar-toggler-mm d-lg-none" type="button" data-toggle="collapse" data-target="#customerMobileNav" aria-controls="customerMobileNav" aria-expanded="false" aria-label="Toggle navigation">
						<i class="fa-solid fa-bars"></i>
					</button>

				</div>
			</div>

			<!-- Mobile Collapsible Menu -->
			<div class="collapse d-lg-none mt-3 pt-3 border-top" id="customerMobileNav" style="border-color: rgba(255, 255, 255, 0.1) !important;">
				<div class="d-flex flex-column" style="gap: 8px;">
					<a href="index.php" class="nav-link-mm <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
						<i class="fa-solid fa-house-chimney"></i> Home
					</a>
					<a href="catview.php" class="nav-link-mm <?php echo in_array($currentPage, ['catview.php', 'subcatview.php', 'productview.php', 'ptview.php']) ? 'active' : ''; ?>">
						<i class="fa-solid fa-layer-group"></i> Browse Categories
					</a>
					<a href="cartview.php" class="nav-link-mm">
						<i class="fa-solid fa-cart-shopping"></i> My Cart (<?php echo $cartCount; ?>)
					</a>
					<a href="vieworders.php" class="nav-link-mm">
						<i class="fa-solid fa-box-archive"></i> Order History
					</a>
					<a href="warrantyview.php" class="nav-link-mm">
						<i class="fa-solid fa-shield-halved"></i> Warranty Claims
					</a>
					<div class="mm-divider"></div>
					<a href="viewprofile.php" class="nav-link-mm">
						<i class="fa-solid fa-id-card text-warning"></i> View Profile
					</a>
					<a href="profileedit.php" class="nav-link-mm highlight-edit">
						<i class="fa-solid fa-user-pen text-info"></i> Edit Profile
					</a>
					<a href="../logout.php" class="nav-link-mm text-danger font-weight-bold">
						<i class="fa-solid fa-power-off"></i> Logout
					</a>
				</div>
			</div>

		</div>
	</header>