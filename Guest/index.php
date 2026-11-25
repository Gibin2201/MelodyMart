<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../dboperation.php";

/**
 * MelodyMart - Find Your Sound
 * Categories loaded dynamically from database (tbl_category managed via Admin/category.php and categoryaction.php)
 */

// Application & Navigation URLs
$pageTitle   = "MelodyMart | Find Your Sound & Premium Musical Instruments";
$loginUrl    = "login.php";
$registerUrl = "cusreg.php";
$shopUrl     = "#categories";

// Check if customer is authenticated
$isCustomerLoggedIn = isset($_SESSION['customer_id']) && !empty($_SESSION['customer_id']);

// Fetch categories from database (populated by Admin/category.php and categoryaction.php)
$categories = [];
try {
    $db = new dboperation();
    $catQuery = "SELECT * FROM tbl_category ORDER BY category_id ASC";
    $catResult = $db->executequery($catQuery);
    if ($catResult) {
        while ($row = mysqli_fetch_assoc($catResult)) {
            $categories[] = $row;
        }
    }
} catch (\Throwable $e) {
    $categories = [];
}

/**
 * Helper function to return valid category image URL or reliable fallback
 */
function categoryImage(?string $path): string {
    if (!empty($path)) {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        $localPath = __DIR__ . '/../uploads/' . $path;
        if (file_exists($localPath)) {
            return '../uploads/' . rawurlencode($path);
        }
    }
    return 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=800&q=80';
}

/**
 * Helper function to return valid instrument image URL or reliable fallback
 */
function instrumentImage(?string $path): string {
    if (!empty($path)) {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        $localUpload = __DIR__ . '/../uploads/' . $path;
        if (file_exists($localUpload)) {
            return '../uploads/' . rawurlencode($path);
        }
        if (file_exists(__DIR__ . '/' . ltrim($path, '/'))) {
            return $path;
        }
    }
    return 'ins.jpg';
}

// Fetch Featured Instruments dynamically from database (tbl_product linked with tbl_subcategory & tbl_category)
$products = [];
try {
    $db = new dboperation();
    $prodQuery = "SELECT p.*, s.name AS subcat_name, c.category_name 
                  FROM tbl_product p 
                  LEFT JOIN tbl_subcategory s ON p.subcat = s.subcategory_id 
                  LEFT JOIN tbl_category c ON s.category_id = c.category_id 
                  WHERE p.stock > 0 
                  ORDER BY p.product_id DESC LIMIT 8";
    $prodResult = $db->executequery($prodQuery);
    if ($prodResult && mysqli_num_rows($prodResult) > 0) {
        while ($pRow = mysqli_fetch_assoc($prodResult)) {
            $products[] = $pRow;
        }
    }
} catch (\Throwable $e) {
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Discover premium musical instruments and accessories at MelodyMart. Guitars, pianos, drums, and pro audio gear for musicians of every level.">

    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Deferred JavaScript -->
    <script src="assets/js/script.js" defer></script>
</head>
<body>

<header class="navbar">
    <div class="container nav-inner">

        <a href="index.php" class="logo">
            <span class="logo-icon"><i class="fa-solid fa-music"></i></span>
            <span class="logo-name">Melody<span>Mart</span></span>
        </a>

        <button class="menu-toggle" id="menuToggle"
                aria-label="Toggle navigation"
                aria-expanded="false">
            <i class="fa-solid fa-bars"></i>
        </button>

        <nav>
            <ul class="nav-links" id="navLinks">
                <li><a href="#home">Home</a></li>
                <li><a href="#categories">Categories</a></li>
                <li><a href="#products">Featured Products</a></li>
                <li><a href="#benefits">Why Choose Us</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>

        <div class="nav-actions">
            <?php if ($isCustomerLoggedIn): ?>
                <a href="../Customer/index.php" class="btn btn-outline"><i class="fa-solid fa-user"></i> Dashboard</a>
                <a href="../logout.php" class="btn btn-gold"><i class="fa-solid fa-power-off"></i> Logout</a>
            <?php else: ?>
                <a href="<?= htmlspecialchars($loginUrl) ?>" class="btn btn-outline">Login</a>
                <a href="<?= htmlspecialchars($registerUrl) ?>" class="btn btn-gold">Register</a>
            <?php endif; ?>
        </div>

    </div>
</header>

<main>
    <!-- HERO -->
    <section class="hero" id="home">
        <div class="container">
            <div class="hero-content">
                <span class="eyebrow"><i class="fa-solid fa-sparkles"></i> YOUR MUSIC DESTINATION</span>

                <h1>Discover Your<br>Perfect <span>Sound.</span></h1>

                <p>
                    Explore finely crafted musical instruments for aspiring beginners,
                    passionate musicians, and seasoned professionals.
                    Find the instrument that brings your artistic voice to life.
                </p>

                <div class="hero-buttons">
                    <a href="<?= htmlspecialchars($shopUrl) ?>" class="btn btn-gold">
                        Explore Instruments <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a href="<?= htmlspecialchars($registerUrl) ?>" class="btn btn-outline">
                        <i class="fa-regular fa-user"></i> Join MelodyMart
                    </a>
                </div>

                <div class="hero-stats">
                    <div class="hero-stat-item">
                        <span class="stat-number">5,000+</span>
                        <span class="stat-label">Instruments</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="hero-stat-item">
                        <span class="stat-number">100%</span>
                        <span class="stat-label">Authentic Gear</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="hero-stat-item">
                        <span class="stat-number">4.9 / 5</span>
                        <span class="stat-label">Musician Rating</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CATEGORIES -->
    <section class="section" id="categories">
        <div class="container">
            <div class="section-heading">
                <span class="eyebrow">EXPLORE OUR COLLECTION</span>
                <h2>Find Your Instrument</h2>
                <p>Discover the right sound crafted for your personal musical journey.</p>
            </div>

            <div class="categories-grid">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <?php 
                            $catId   = (int)$category['category_id'];
                            $catName = htmlspecialchars($category['category_name']);
                            $catImg  = categoryImage($category['category_image'] ?? null);
                            $catUrl  = $isCustomerLoggedIn 
                                ? "../Customer/subcatview.php?categoryid=" . $catId 
                                : "login.php?redirect=" . urlencode("../Customer/subcatview.php?categoryid=" . $catId) . "&notice=login_required";
                        ?>
                        <a class="category-card" href="<?= htmlspecialchars($catUrl) ?>">
                            <div class="category-image-wrap">
                                <img src="<?= htmlspecialchars($catImg) ?>" alt="<?= $catName ?>" loading="lazy">
                            </div>

                            <h3><?= $catName ?></h3>
                            <p>Discover our selection of <?= strtolower($catName) ?> and equipment.</p>

                            <span class="category-link">
                                <?= $isCustomerLoggedIn ? 'Browse Instruments' : 'Login to Explore' ?> <i class="fa-solid fa-arrow-right"></i>
                            </span>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 8px; border: 1px solid var(--border);">
                        <p style="color: var(--muted); margin-bottom: 12px;">No categories found in the database yet.</p>
                        <a href="login.php" class="btn btn-gold">Login to Manage</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- FEATURED PRODUCTS -->
    <section class="section products-section" id="products">
        <div class="container">
            <div class="section-top">
                <div class="section-heading">
                    <span class="eyebrow"><i class="fa-solid fa-guitar"></i> HANDPICKED FOR YOU</span>
                    <h2>Featured Instruments</h2>
                    <p>Authentic, verified instruments currently available in our live inventory.</p>
                </div>

                <?php 
                    $allProdsUrl = $isCustomerLoggedIn 
                        ? "../Customer/catview.php" 
                        : "login.php?redirect=" . urlencode("../Customer/catview.php") . "&notice=login_required";
                ?>
                <a href="<?= htmlspecialchars($allProdsUrl) ?>" class="text-link">
                    <?= $isCustomerLoggedIn ? 'Browse Full Catalog' : 'View All Instruments' ?> <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="products-grid">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <?php 
                            $prodId   = (int)$product['product_id'];
                            $prodName = htmlspecialchars($product['product_name']);
                            $prodImg  = htmlspecialchars(instrumentImage($product['image'] ?? null));
                            $brand    = !empty($product['brand']) ? htmlspecialchars($product['brand']) : 'MelodyMart';
                            $typeName = !empty($product['subcat_name']) ? htmlspecialchars($product['subcat_name']) : (!empty($product['category_name']) ? htmlspecialchars($product['category_name']) : 'Instrument');
                            $priceVal = number_format((float)$product['amount'], 2);
                            $stockQty = (int)$product['stock'];

                            // Target URL: Direct purchase if logged in, otherwise redirect to login
                            $targetUrl = $isCustomerLoggedIn 
                                ? "../Customer/ptview.php?productid=" . $prodId 
                                : "login.php?redirect=" . urlencode("../Customer/ptview.php?productid=" . $prodId) . "&notice=login_required";
                        ?>
                        <article class="product-card">
                            <div class="product-image">
                                <span class="product-badge">
                                    <i class="fa-solid fa-certificate mr-1"></i> <?= $brand ?>
                                </span>
                                <a href="<?= htmlspecialchars($targetUrl) ?>" style="display: block; width: 100%; height: 100%;">
                                    <img
                                        src="<?= $prodImg ?>"
                                        alt="<?= $prodName ?>"
                                        onerror="this.src='ins.jpg'"
                                        loading="lazy">
                                </a>
                            </div>

                            <div class="product-info">
                                <span class="product-type">
                                    <?= $typeName ?>
                                </span>

                                <h3>
                                    <a href="<?= htmlspecialchars($targetUrl) ?>" style="color: inherit; text-decoration: none;">
                                        <?= $prodName ?>
                                    </a>
                                </h3>

                                <div class="product-rating">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <span style="color: #2e7d32; font-weight: 600; font-size: 11px; margin-left: 6px;">
                                        <i class="fa-solid fa-circle-check"></i> In Stock (<?= $stockQty ?>)
                                    </span>
                                </div>

                                <div class="product-bottom">
                                    <div class="price-wrap">
                                        <span class="product-price">
                                            ₹<?= $priceVal ?>
                                        </span>
                                    </div>
                                    <a href="<?= htmlspecialchars($targetUrl) ?>" class="btn-product-buy">
                                        <?php if ($isCustomerLoggedIn): ?>
                                            <i class="fa-solid fa-cart-shopping"></i> View &amp; Buy
                                        <?php else: ?>
                                            <i class="fa-solid fa-lock"></i> Buy Now
                                        <?php endif; ?>
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 8px; border: 1px solid var(--border);">
                        <i class="fa-solid fa-guitar mb-3 text-warning" style="font-size: 36px;"></i>
                        <p style="color: var(--muted); margin-bottom: 12px;">No instruments currently featured in stock.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- BENEFITS -->
    <section class="section" id="benefits">
        <div class="container">
            <div class="section-heading">
                <span class="eyebrow">THE MELODYMART EXPERIENCE</span>
                <h2>Made for Music Lovers</h2>
                <p>Everything you need to inspire and elevate your musical journey.</p>
            </div>

            <div class="benefits-grid">
                <div class="benefit">
                    <div class="benefit-icon"><i class="fa-solid fa-guitar"></i></div>
                    <h3>Wide Selection</h3>
                    <p>Explore thousands of premium instruments and accessories in one trusted place.</p>
                </div>

                <div class="benefit">
                    <div class="benefit-icon"><i class="fa-solid fa-award"></i></div>
                    <h3>Authentic Guarantee</h3>
                    <p>Direct manufacturer partnerships ensure 100% genuine warranties on every piece.</p>
                </div>

                <div class="benefit">
                    <div class="benefit-icon"><i class="fa-solid fa-truck-fast"></i></div>
                    <h3>Safe Express Delivery</h3>
                    <p>Specialized shock-resistant packaging ensures instruments arrive stage-ready.</p>
                </div>

                <div class="benefit">
                    <div class="benefit-icon"><i class="fa-solid fa-headphones-simple"></i></div>
                    <h3>Musician Support</h3>
                    <p>Our dedicated team of working musicians provides expert advice and setup help.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta">
        <div class="container">
            <span class="eyebrow">LET THE MUSIC BEGIN</span>
            <h2>Your Musical Journey Starts Here</h2>
            <p>Discover hand-tuned instruments that inspire endless creativity and expression.</p>

            <a href="<?= htmlspecialchars($shopUrl) ?>" class="btn btn-gold">
                Start Exploring Now <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </section>

    <!-- ABOUT -->
    <section class="section" id="about">
        <div class="container about-content">
            <div class="about-image">
                <img
                    src="https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=1000&q=85"
                    alt="MelodyMart Music Studio"
                    loading="lazy">
            </div>

            <div class="about-text">
                <span class="eyebrow">ABOUT MELODYMART</span>
                <h2>Music Begins with the Right Instrument</h2>

                <p>
                    MelodyMart is an artisanal destination crafted for musicians who value tone,
                    craftsmanship, and playability. We bring together iconic heritage brands and
                    cutting-edge modern sound equipment.
                </p>

                <p>
                    Whether you are tuning your first acoustic guitar or configuring a full recording
                    studio, our catalog delivers the instruments and accessories you need to express your passion.
                </p>

                <a href="<?= htmlspecialchars($shopUrl) ?>" class="btn btn-gold">
                    Explore Our Store <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>
</main>

<!-- FOOTER -->
<footer id="contact">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-about">
                <a href="index.php" class="logo">
                    <span class="logo-icon"><i class="fa-solid fa-music"></i></span>
                    <span class="logo-name">Melody<span>Mart</span></span>
                </a>
                <p>Find your sound. Play your passion. Discover your next instrument at MelodyMart.</p>
                <div class="footer-socials">
                    <a href="#contact" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#contact" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#contact" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#contact" aria-label="Spotify"><i class="fa-brands fa-spotify"></i></a>
                </div>
            </div>

            <div class="footer-column">
                <h3>Explore</h3>
                <a href="#home">Home</a>
                <a href="#categories">Categories</a>
                <a href="#products">Featured Products</a>
                <a href="#benefits">Why MelodyMart</a>
                <a href="#about">About Us</a>
            </div>

            <div class="footer-column">
                <h3>Your Account</h3>
                <a href="<?= htmlspecialchars($loginUrl) ?>">Login</a>
                <a href="<?= htmlspecialchars($registerUrl) ?>">Register</a>
                <a href="<?= htmlspecialchars($shopUrl) ?>">Shop Instruments</a>
                <a href="#contact">Order Tracking</a>
            </div>

            <div class="footer-column">
                <h3>Contact & Support</h3>
                <span class="contact-item"><i class="fa-solid fa-location-dot"></i> Music Row, Studio 4, NY</span>
                <span class="contact-item"><i class="fa-solid fa-phone"></i> +1 (800) 555-TUNE</span>
                <span class="contact-item"><i class="fa-solid fa-envelope"></i> support@melodymart.com</span>
                <span class="contact-item"><i class="fa-solid fa-clock"></i> Mon - Sat: 9am - 8pm</span>
            </div>
        </div>

        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> MelodyMart. All rights reserved.</span>
            <span>Crafted with passion for musicians worldwide.</span>
        </div>
    </div>
</footer>

</body>
</html>