<?php 
include("header.php");
include_once("../dboperation.php");
$obj = new dboperation();

// Fetch Categories for Customer
$categories = [];
$resCat = $obj->executequery("SELECT * FROM tbl_category ORDER BY category_name ASC");
if ($resCat) {
    while ($row = mysqli_fetch_assoc($resCat)) {
        $categories[] = $row;
    }
}

// Fetch Featured Products for Customer
$products = [];
$resProd = $obj->executequery("SELECT p.*, s.name AS subcat_name, c.category_name 
                               FROM tbl_product p 
                               LEFT JOIN tbl_subcategory s ON p.subcat = s.subcategory_id 
                               LEFT JOIN tbl_category c ON s.category_id = c.category_id 
                               ORDER BY p.product_id DESC LIMIT 8");
if ($resProd) {
    while ($row = mysqli_fetch_assoc($resProd)) {
        $products[] = $row;
    }
}
?>

<!-- Customer Hero Banner Area -->
<section class="customer-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 col-md-12 mb-4 mb-lg-0">
                <span class="customer-badge-tag">
                    <i class="fa-solid fa-compact-disc fa-spin" style="--fa-animation-duration: 4s;"></i> MELODYMART SOUND LOUNGE
                </span>
                <h1 class="customer-hero-title">
                    Welcome back, <span><?php echo $activeCustomerName; ?></span>
                </h1>
                
                <!-- Inspiring Musician Tagline -->
                <p class="customer-hero-desc">
                    Immerse yourself in world-class tone. Every guitar, keyboard, and orchestral piece in our collection is precision-calibrated by master luthiers for your signature sound.
                </p>

                <!-- ATTRACTIVE: Live Studio Sound Visualizer & Tone Signature Bar (No Links) -->
                <div class="studio-sound-card">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap" style="gap: 10px;">
                        <div class="d-flex align-items-center" style="gap: 12px;">
                            <!-- Animated Sound Wave Visualizer -->
                            <div class="sound-wave-bars">
                                <span class="bar bar-1"></span>
                                <span class="bar bar-2"></span>
                                <span class="bar bar-3"></span>
                                <span class="bar bar-4"></span>
                                <span class="bar bar-5"></span>
                                <span class="bar bar-6"></span>
                                <span class="bar bar-7"></span>
                                <span class="bar bar-8"></span>
                            </div>
                            <div>
                                <span class="studio-status-badge">LIVE ACOUSTIC FIDELITY</span>
                                <div class="studio-status-title">Studio Master Tone &bull; Calibrated</div>
                            </div>
                        </div>
                        <span class="studio-rating-pill">
                            <i class="fa-solid fa-star text-warning mr-1"></i> 99.8% Tone Accuracy
                        </span>
                    </div>

                    <!-- Visual Tone Spectrum Badges -->
                    <div class="tone-spectrum-grid">
                        <div class="tone-pill">
                            <i class="fa-solid fa-guitar"></i>
                            <div>
                                <strong>Warm Resonance</strong>
                                <small>Hand-carved Tone Woods</small>
                            </div>
                        </div>
                        <div class="tone-pill">
                            <i class="fa-solid fa-sliders"></i>
                            <div>
                                <strong>Harmonic Balance</strong>
                                <small>Zero-Buzz Intonation</small>
                            </div>
                        </div>
                        <div class="tone-pill">
                            <i class="fa-solid fa-shield-halved"></i>
                            <div>
                                <strong>Transit Vault</strong>
                                <small>Shockproof Climate Pack</small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="col-lg-5 col-md-12 text-center">
                <!-- ATTRACTIVE: Luxury Sound Stage Showcase (No links, pure aesthetic delight) -->
                <div class="hero-stage-showcase">
                    <div class="stage-frame">
                        <img src="../Guest/ins.jpg" alt="Artisanal Musical Instruments" class="img-fluid stage-image" onerror="this.src='img/features/f-icon1.png'">
                        
                        <!-- Floating Glassmorphic Tone Pill Top -->
                        <div class="floating-tone-pill top-pill">
                            <i class="fa-solid fa-headphones-simple text-warning"></i>
                            <span>Ultra-Low Resonance Chamber</span>
                        </div>

                        <!-- Floating Glassmorphic Tone Pill Bottom -->
                        <div class="floating-tone-pill bottom-pill">
                            <i class="fa-solid fa-certificate text-warning"></i>
                            <span>100% Master Technician Inspected</span>
                        </div>
                    </div>

                    <!-- Studio Acoustic Wave Line Below Image -->
                    <div class="stage-frequency-bar mt-3">
                        <span class="freq-tag">20Hz - 20kHz</span>
                        <div class="freq-line">
                            <span class="freq-pulse"></span>
                        </div>
                        <span class="freq-label">Full Dynamic Range</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Features / Trust Highlights -->
<section class="py-5" style="background: #fbfaf6; border-bottom: 1px solid #e8e5dc;">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center h-100 border">
                    <div class="mr-3" style="font-size: 26px; width: 40px; color: #d7ad69;">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <div>
                        <h6 class="font-weight-bold mb-1 text-dark">Shockproof Delivery</h6>
                        <small class="text-muted">Rigid transit packaging</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center h-100 border">
                    <div class="mr-3" style="font-size: 26px; width: 40px; color: #d7ad69;">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <h6 class="font-weight-bold mb-1 text-dark">Verified Warranties</h6>
                        <small class="text-muted">Automated digital cards</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center h-100 border">
                    <div class="mr-3" style="font-size: 26px; width: 40px; color: #d7ad69;">
                        <i class="fa-solid fa-music"></i>
                    </div>
                    <div>
                        <h6 class="font-weight-bold mb-1 text-dark">Acoustic Setup</h6>
                        <small class="text-muted">Pre-tuned &amp; inspected</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center h-100 border">
                    <div class="mr-3" style="font-size: 26px; width: 40px; color: #d7ad69;">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <div>
                        <h6 class="font-weight-bold mb-1 text-dark">Musician Support</h6>
                        <small class="text-muted">Expert gear consultations</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Instrument Categories Section -->
<section class="py-5" style="background: #ffffff;">
    <div class="container">
        <div class="row justify-content-between align-items-end mb-4">
            <div class="col-md-8">
                <span class="text-uppercase font-weight-bold small" style="letter-spacing: 1.5px; color: #b88f4b;">
                    INSTRUMENT DEPARTMENTS
                </span>
                <h2 style="font-family: 'Playfair Display', Georgia, serif; font-size: 32px; font-weight: 700; color: #121411; margin-top: 5px;">
                    Explore by Category
                </h2>
                <p class="text-muted mb-0">Browse through string, wind, keyboard, and percussion collections.</p>
            </div>
            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                <a href="catview.php" class="btn btn-outline-dark font-weight-bold" style="border-radius: 8px;">
                    View All Categories <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <div class="row">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="category-card-deluxe">
                            <div class="category-card-img">
                                <a href="subcatview.php?categoryid=<?php echo (int)$cat['category_id']; ?>">
                                    <img src="../uploads/<?php echo htmlspecialchars($cat['category_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($cat['category_name']); ?>" 
                                         onerror="this.src='../Guest/ins.jpg'">
                                </a>
                            </div>
                            <div class="category-card-body">
                                <div>
                                    <h5 class="font-weight-bold mb-2">
                                        <a href="subcatview.php?categoryid=<?php echo (int)$cat['category_id']; ?>" class="text-dark" style="text-decoration: none;">
                                            <?php echo htmlspecialchars($cat['category_name']); ?>
                                        </a>
                                    </h5>
                                </div>
                                <a href="subcatview.php?categoryid=<?php echo (int)$cat['category_id']; ?>" 
                                   class="btn btn-sm btn-outline-warning text-dark font-weight-bold mt-2"
                                   style="border-color: #d7ad69; border-radius: 8px;">
                                    View Subcategories <i class="fa-solid fa-chevron-right ml-1" style="font-size: 11px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-4 text-muted">
                    <p>No categories available currently.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5" style="background: #f7f5ef; border-top: 1px solid #e8e5dc;">
    <div class="container">
        <div class="row justify-content-between align-items-end mb-4">
            <div class="col-md-8">
                <span class="text-uppercase font-weight-bold small" style="letter-spacing: 1.5px; color: #b88f4b;">
                    CURATED INVENTORY
                </span>
                <h2 style="font-family: 'Playfair Display', Georgia, serif; font-size: 32px; font-weight: 700; color: #121411; margin-top: 5px;">
                    Latest Instruments in Store
                </h2>
                <p class="text-muted mb-0">Hand-selected instruments ready for immediate delivery.</p>
            </div>
            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                <a href="catview.php" class="btn btn-dark font-weight-bold" style="border-radius: 8px;">
                    Browse Full Catalog <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <div class="row">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $p): ?>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="product-card-deluxe">
                            <div class="product-card-img">
                                <span class="product-card-badge">
                                    <?php echo !empty($p['subcat_name']) ? htmlspecialchars($p['subcat_name']) : (!empty($p['brand']) ? htmlspecialchars($p['brand']) : 'MelodyMart'); ?>
                                </span>
                                <a href="ptview.php?productid=<?php echo (int)$p['product_id']; ?>">
                                    <img src="../uploads/<?php echo htmlspecialchars($p['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($p['product_name']); ?>" 
                                         onerror="this.src='../Guest/ins.jpg'">
                                </a>
                            </div>
                            <div class="card-body text-center d-flex flex-column justify-content-between p-3 border-top bg-white">
                                <div>
                                    <h5 class="card-title font-weight-bold mb-2" style="font-size: 16px;">
                                        <a href="ptview.php?productid=<?php echo (int)$p['product_id']; ?>" class="text-dark" style="text-decoration: none;">
                                            <?php echo htmlspecialchars($p['product_name']); ?>
                                        </a>
                                    </h5>
                                    <p class="text-success font-weight-bold mb-3" style="font-size: 18px;">
                                        ₹<?php echo number_format((float)$p['amount'], 2); ?>
                                    </p>
                                </div>
                                <div class="d-flex" style="gap: 8px;">
                                    <a href="ptview.php?productid=<?php echo (int)$p['product_id']; ?>" 
                                       class="btn btn-sm btn-dark flex-grow-1 font-weight-bold" 
                                       style="border-radius: 8px; padding: 8px;">
                                        <i class="fa-solid fa-eye mr-1"></i> Details
                                    </a>
                                    <a href="ptview.php?productid=<?php echo (int)$p['product_id']; ?>" 
                                       class="btn btn-sm text-dark font-weight-bold flex-grow-1" 
                                       style="background: #d7ad69; border-radius: 8px; padding: 8px;">
                                        <i class="fa-solid fa-cart-plus mr-1"></i> Buy
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5 text-muted">
                    <i class="fa-solid fa-guitar fa-3x mb-3 text-warning"></i>
                    <p class="lead">No products found in the store currently.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Call to Action / Help Banner -->
<section class="py-5" style="background: linear-gradient(135deg, #121411 0%, #1a1d18 100%); color: #ffffff; border-top: 2px solid var(--mm-gold);">
    <div class="container text-center py-4">
        <h2 style="font-family: 'Playfair Display', Georgia, serif; font-size: 32px; font-weight: 700; color: #ffffff;">
            Need help selecting your next instrument?
        </h2>
        <p class="text-white-50 mx-auto" style="max-width: 620px; font-size: 15px;">
            Our dedicated MelodyMart specialists provide tuned instruments, official warranty cards, and expert customer setups.
        </p>
        <div class="d-flex justify-content-center flex-wrap mt-4" style="gap: 12px;">
            <a href="catview.php" class="btn-hero-gold">
                <i class="fa-solid fa-layer-group"></i> Browse Instrument Catalog
            </a>
            <a href="profileedit.php" class="btn-hero-outline">
                <i class="fa-solid fa-user-pen"></i> Update Profile &amp; Preferences
            </a>
        </div>
    </div>
</section>

<?php 
include("footer.php");
?>