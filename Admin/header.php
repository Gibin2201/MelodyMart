<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);

// Live pending warranty count
$pendingWarrantyCount = 0;
require_once(__DIR__ . "/../dboperation.php");
try {
    $dbHeaderAdmin = new dboperation();
    $wCountRes = $dbHeaderAdmin->executequery("SELECT COUNT(*) as cnt FROM tbl_warranty WHERE wstatus = 'requested'");
    if ($wCountRes && $wRow = mysqli_fetch_assoc($wCountRes)) {
        $pendingWarrantyCount = (int)$wRow['cnt'];
    }
} catch (\Throwable $e) {
    $pendingWarrantyCount = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="utf-8">
	<title>MelodyMart | Admin Portal</title>
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
	
	<!-- Favicon -->
	<link rel="icon" href="../Guest/assets/img/favicon.png" type="image/png">
	
	<!-- Google Fonts: DM Sans & Playfair Display (Matching Guest Home) -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
	
	<!-- FontAwesome 6 Icons (Matching Guest Home) -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
	
	<!-- Core Bootstrap CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	
	<!-- Ready Layout CSS -->
	<link rel="stylesheet" href="assets/css/ready.css">
	
	<!-- MelodyMart Guest-Inspired Admin Theme CSS -->
	<link rel="stylesheet" href="assets/css/admin-guest-theme.css">
</head>
<body>
	<div class="wrapper">
		<!-- Main Header / Topbar -->
		<div class="main-header">
			<div class="logo-header">
				<a href="index.php" class="logo">
					<span class="logo-icon"><i class="fa-solid fa-music"></i></span>
					<span class="logo-text">Melody<span>Mart</span></span>
				</a>
				<button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation" title="Toggle Sidebar">
					<i class="fa-solid fa-bars"></i>
				</button>
				<button class="topbar-toggler more"><i class="fa-solid fa-ellipsis-vertical"></i></button>
			</div>
			<nav class="navbar navbar-header navbar-expand-lg">
				<div class="container-fluid">
					<div class="header-left-info d-none d-md-flex align-items-center">
						<span class="portal-badge">
							<i class="fa-solid fa-shield-halved"></i>
							<span>ADMIN CONTROL PANEL</span>
						</span>
					</div>
					<ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
						<!-- Clean Admin Profile Dropdown -->
						<li class="nav-item dropdown">
							<a class="dropdown-toggle profile-pic d-flex align-items-center" data-toggle="dropdown" href="#" aria-expanded="false">
								<div class="admin-avatar">
									<i class="fa-solid fa-user-shield"></i>
								</div>
								<div class="admin-user-meta d-none d-sm-block text-left ml-2">
									<span class="admin-name font-weight-bold d-block text-white">Admin</span>
									<span class="admin-role d-block">Super Admin</span>
								</div>
								<i class="fa-solid fa-chevron-down ml-2 text-muted" style="font-size: 11px;"></i>
							</a>
							<ul class="dropdown-menu dropdown-user dropdown-menu-right shadow-lg border-0">
								<li>
									<div class="user-box p-3 border-bottom text-center">
										<div class="admin-avatar-lg mx-auto mb-2">
											<i class="fa-solid fa-user-shield"></i>
										</div>
										<h6 class="font-weight-bold mb-0 text-dark">MelodyMart Admin</h6>
										<p class="text-muted small mb-0">System Administrator</p>
									</div>
								</li>
								<li><a class="dropdown-item py-2" href="index.php"><i class="fa-solid fa-gauge-high mr-2 text-warning"></i> Dashboard</a></li>
								<li><a class="dropdown-item py-2" href="ordersview.php"><i class="fa-solid fa-box-archive mr-2 text-primary"></i> Customer Orders</a></li>
								<li><a class="dropdown-item py-2" href="warrantyview.php"><i class="fa-solid fa-shield-halved mr-2 text-warning"></i> Warranty Claims <?php if ($pendingWarrantyCount > 0): ?><span class="badge badge-warning text-dark ml-1"><?php echo $pendingWarrantyCount; ?></span><?php endif; ?></a></li>
								<div class="dropdown-divider my-1"></div>
								<li><a class="dropdown-item py-2 text-danger font-weight-bold" href="../logout.php"><i class="fa-solid fa-power-off mr-2"></i> Logout</a></li>
							</ul>
						</li>
					</ul>
				</div>
			</nav>
		</div>

		<!-- Sidebar Navigation -->
		<div class="sidebar">
			<div class="scrollbar-inner sidebar-wrapper">
				<!-- Admin Info Card in Sidebar -->
				<div class="user-panel-mini">
					<div class="photo">
						<span class="admin-mini-icon"><i class="fa-solid fa-music"></i></span>
					</div>
					<div class="info">
						<span class="title">MelodyMart</span>
						<span class="status"><span class="status-indicator"></span> Online &bull; Admin</span>
					</div>
				</div>

				<ul class="nav">
					<li class="nav-section-title">MAIN</li>
					<li class="nav-item <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
						<a href="index.php">
							<i class="fa-solid fa-gauge-high"></i>
							<p>Dashboard</p>
						</a>
					</li>

					<li class="nav-section-title">CATALOG MANAGEMENT</li>
					<li class="nav-item <?php echo ($currentPage == 'product.php') ? 'active' : ''; ?>">
						<a href="product.php">
							<i class="fa-solid fa-guitar"></i>
							<p>Add Product</p>
						</a>
					</li>
					<li class="nav-item <?php echo ($currentPage == 'productview.php' || $currentPage == 'productedit.php') ? 'active' : ''; ?>">
						<a href="productview.php">
							<i class="fa-solid fa-boxes-stacked"></i>
							<p>View Products</p>
						</a>
					</li>
					<li class="nav-item <?php echo ($currentPage == 'category.php') ? 'active' : ''; ?>">
						<a href="category.php">
							<i class="fa-solid fa-folder-plus"></i>
							<p>Add Category</p>
						</a>
					</li>
					<li class="nav-item <?php echo ($currentPage == 'categoryview.php' || $currentPage == 'categoryedit.php') ? 'active' : ''; ?>">
						<a href="categoryview.php">
							<i class="fa-solid fa-layer-group"></i>
							<p>View Categories</p>
						</a>
					</li>
					<li class="nav-item <?php echo ($currentPage == 'subcategory.php') ? 'active' : ''; ?>">
						<a href="subcategory.php">
							<i class="fa-solid fa-tags"></i>
							<p>Add SubCategory</p>
						</a>
					</li>
					<li class="nav-item <?php echo ($currentPage == 'subcategoryview.php' || $currentPage == 'subcategoryedit.php') ? 'active' : ''; ?>">
						<a href="subcategoryview.php">
							<i class="fa-solid fa-list-check"></i>
							<p>View SubCategories</p>
						</a>
					</li>

					<li class="nav-section-title">ORDERS &amp; SALES</li>
					<li class="nav-item <?php echo in_array($currentPage, ['ordersview.php', 'orderdetail.php']) ? 'active' : ''; ?>">
						<a href="ordersview.php">
							<i class="fa-solid fa-box-archive"></i>
							<p>Customer Orders</p>
						</a>
					</li>
					<li class="nav-item <?php echo ($currentPage == 'warrantyview.php') ? 'active' : ''; ?>">
						<a href="warrantyview.php">
							<i class="fa-solid fa-shield-halved"></i>
							<p>Warranty Claims <?php if ($pendingWarrantyCount > 0): ?><span class="badge badge-warning text-dark ml-2" style="background: #d7ad69;"><?php echo $pendingWarrantyCount; ?></span><?php endif; ?></p>
						</a>
					</li>

					<li class="nav-section-title">LOCATION MANAGEMENT</li>
					<li class="nav-item <?php echo ($currentPage == 'district.php') ? 'active' : ''; ?>">
						<a href="district.php">
							<i class="fa-solid fa-map-location-dot"></i>
							<p>Add District</p>
						</a>
					</li>
					<li class="nav-item <?php echo ($currentPage == 'districtview.php' || $currentPage == 'districtedit.php') ? 'active' : ''; ?>">
						<a href="districtview.php">
							<i class="fa-solid fa-map"></i>
							<p>View Districts</p>
						</a>
					</li>
					<li class="nav-item <?php echo ($currentPage == 'location.php') ? 'active' : ''; ?>">
						<a href="location.php">
							<i class="fa-solid fa-location-dot"></i>
							<p>Add Location</p>
						</a>
					</li>
					<li class="nav-item <?php echo ($currentPage == 'locationview.php' || $currentPage == 'locationedit.php') ? 'active' : ''; ?>">
						<a href="locationview.php">
							<i class="fa-solid fa-city"></i>
							<p>View Locations</p>
						</a>
					</li>

					<li class="nav-section-title">REPORTS &amp; ANALYTICS</li>
					<li class="nav-item <?php echo ($currentPage == 'barchartreport.php') ? 'active' : ''; ?>">
						<a href="barchartreport.php">
							<i class="fa-solid fa-chart-column"></i>
							<p>Monthly Sales Bar</p>
						</a>
					</li>
					<li class="nav-item <?php echo ($currentPage == 'piechartreport.php') ? 'active' : ''; ?>">
						<a href="piechartreport.php">
							<i class="fa-solid fa-chart-pie"></i>
							<p>Category Sales Pie</p>
						</a>
					</li>
					<li class="nav-item <?php echo ($currentPage == 'datewisereport.php') ? 'active' : ''; ?>">
						<a href="datewisereport.php">
							<i class="fa-solid fa-calendar-days"></i>
							<p>Date-wise Report</p>
						</a>
					</li>

					<li class="nav-section-title">ACCOUNT</li>
					<li class="nav-item">
						<a href="../logout.php" class="text-danger">
							<i class="fa-solid fa-power-off text-danger"></i>
							<p class="text-danger">Logout</p>
						</a>
					</li>
				</ul>
			</div>
		</div>