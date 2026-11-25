<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("../dboperation.php");
include("header.php");

$obj = new dboperation();
$productid = isset($_GET['productid']) ? (int)$_GET['productid'] : 0;

// Fetch product joined with subcategory and category
$sql = "SELECT p.*, s.name AS subcat_name, s.subcategory_id, c.category_name, c.category_id 
        FROM tbl_product p 
        LEFT JOIN tbl_subcategory s ON p.subcat = s.subcategory_id 
        LEFT JOIN tbl_category c ON s.category_id = c.category_id 
        WHERE p.product_id = '$productid'";
$res = $obj->executequery($sql);
$product = ($res && mysqli_num_rows($res) > 0) ? mysqli_fetch_assoc($res) : null;
?>

<div class="product-page-wrapper">
    <div class="container">

        <?php if (!$product): ?>
            <!-- Product Not Found State -->
            <div class="product-not-found-card text-center my-4">
                <div class="empty-icon-circle mb-3">
                    <i class="fa-solid fa-guitar"></i>
                </div>
                <h3 class="empty-title">Instrument Not Found</h3>
                <p class="empty-subtitle">The requested musical instrument may have been retired or does not exist in our catalog.</p>
                <div class="mt-3 d-flex justify-content-center gap-2">
                    <a href="catview.php" class="btn btn-gold-deluxe">
                        <i class="fa-solid fa-layer-group mr-1"></i> Browse Categories
                    </a>
                    <a href="index.php" class="btn btn-outline-dark btn-sm font-weight-bold px-3 py-2" style="border-radius: 8px;">
                        <i class="fa-solid fa-house mr-1"></i> Storefront Home
                    </a>
                </div>
            </div>
        <?php else: 
            $prodId      = (int)($product['product_id'] ?? 0);
            $prodName    = htmlspecialchars((string)($product['product_name'] ?? ''));
            $unitPrice   = (float)($product['amount'] ?? 0);
            $stock       = (int)($product['stock'] ?? 0);
            $imgName     = htmlspecialchars((string)($product['image'] ?? ''));
            $brandName   = !empty($product['brand']) ? htmlspecialchars((string)$product['brand']) : 'MelodyMart Studio';
            $subcatName  = !empty($product['subcat_name']) ? htmlspecialchars((string)$product['subcat_name']) : 'Instruments';
            $subcatId    = (int)($product['subcategory_id'] ?? 0);
            $catName     = !empty($product['category_name']) ? htmlspecialchars((string)$product['category_name']) : 'Collection';
            $catId       = (int)($product['category_id'] ?? 0);
            $rawDesc     = (isset($product['description']) && $product['description'] !== null) ? trim((string)$product['description']) : '';
            $description = ($rawDesc !== '') ? htmlspecialchars($rawDesc) : "Handcrafted with precision tonewoods and calibrated hardware to deliver pristine harmonics, rich acoustic resonance, and effortless playability for both studio recordings and live stage performances.";
            $isAvailable = ($stock > 0);

            // Check if product is already in the active customer's cart
            $isInCart = false;
            $cartQty = 0;
            if (isset($_SESSION['customer_id'])) {
                $activeCid = (int)$_SESSION['customer_id'];
                $checkCartQuery = $obj->executequery("SELECT cart_id, quantity FROM tbl_cart WHERE product_id = '$prodId' AND customer_id = '$activeCid' AND status = 'cart' LIMIT 1");
                if ($checkCartQuery && mysqli_num_rows($checkCartQuery) > 0) {
                    $isInCart = true;
                    $cartRow = mysqli_fetch_assoc($checkCartQuery);
                    $cartQty = (int)$cartRow['quantity'];
                }
            }
        ?>

        <!-- Luxury Breadcrumb Bar -->
        <nav class="deluxe-breadcrumb mb-3" aria-label="breadcrumb">
            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 10px;">
                <ol class="breadcrumb-list mb-0">
                    <li><a href="index.php"><i class="fa-solid fa-house"></i> Home</a></li>
                    <li><i class="fa-solid fa-chevron-right separator"></i></li>
                    <li><a href="catview.php">Categories</a></li>
                    <?php if ($catId > 0): ?>
                        <li><i class="fa-solid fa-chevron-right separator"></i></li>
                        <li><a href="subcatview.php?categoryid=<?= $catId ?>"><?= $catName ?></a></li>
                    <?php endif; ?>
                    <?php if ($subcatId > 0): ?>
                        <li><i class="fa-solid fa-chevron-right separator"></i></li>
                        <li><a href="productview.php?subcatid=<?= $subcatId ?>"><?= $subcatName ?></a></li>
                    <?php endif; ?>
                    <li><i class="fa-solid fa-chevron-right separator"></i></li>
                    <li class="active"><?= $prodName ?></li>
                </ol>

                <div>
                    <?php if ($subcatId > 0): ?>
                        <a href="productview.php?subcatid=<?= $subcatId ?>" class="btn-crumb-back">
                            <i class="fa-solid fa-arrow-left"></i> Back to <?= $subcatName ?>
                        </a>
                    <?php else: ?>
                        <a href="catview.php" class="btn-crumb-back">
                            <i class="fa-solid fa-arrow-left"></i> Back to Categories
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <!-- Main Product Showcase Card (Clean, Focused Product Display) -->
        <div class="product-showcase-card">
            <div class="row no-gutters">
                
                <!-- Left Column: High-End Instrument Showcase Image -->
                <div class="col-lg-6">
                    <div class="product-gallery-pane">
                        <div class="image-stage-box">
                            <span class="gallery-badge-pill">
                                <i class="fa-solid fa-certificate"></i> Verified Authentic
                            </span>
                            <img id="main-image" 
                                 src="../uploads/<?= $imgName ?>" 
                                 alt="<?= $prodName ?>" 
                                 class="product-main-img" 
                                 onerror="this.src='../Guest/ins.jpg'">
                        </div>

                        <!-- Micro Trust Highlights Beneath Media -->
                        <div class="gallery-trust-strip">
                            <div class="trust-badge-item">
                                <i class="fa-solid fa-guitar"></i>
                                <span>Master Luthier Setup</span>
                            </div>
                            <div class="trust-badge-item">
                                <i class="fa-solid fa-shield-halved"></i>
                                <span>1-Year Warranty</span>
                            </div>
                            <div class="trust-badge-item">
                                <i class="fa-solid fa-box-open"></i>
                                <span>Insured Dispatch</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Specs, Pricing & Purchase Box -->
                <div class="col-lg-6">
                    <div class="product-details-pane">
                        
                        <!-- Top Tag Strip -->
                        <div class="d-flex align-items-center mb-2 flex-wrap" style="gap: 6px;">
                            <span class="brand-pill">
                                <i class="fa-solid fa-award"></i> <?= $brandName ?>
                            </span>
                            <span class="subcat-pill">
                                <i class="fa-solid fa-music"></i> <?= $subcatName ?>
                            </span>
                            <?php if ($isAvailable): ?>
                                <?php if ($stock <= 5): ?>
                                    <span class="stock-pill low-stock">
                                        <i class="fa-solid fa-fire"></i> Only <?= $stock ?> Left In Stock
                                    </span>
                                <?php else: ?>
                                    <span class="stock-pill in-stock">
                                        <i class="fa-solid fa-circle-check"></i> In Stock (<?= $stock ?> Units)
                                    </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="stock-pill out-stock">
                                    <i class="fa-solid fa-circle-xmark"></i> Currently Sold Out
                                </span>
                            <?php endif; ?>
                            <?php if ($isInCart): ?>
                                <span class="stock-pill" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
                                    <i class="fa-solid fa-cart-shopping"></i> Already in Cart (<?= $cartQty ?>)
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Product Title -->
                        <h1 class="product-showcase-title"><?= $prodName ?></h1>

                        <!-- Musician Tone Score & Rating -->
                        <div class="musician-score-bar mb-2">
                            <div class="stars-gold">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <span class="rating-text">4.9 / 5.0</span>
                            <span class="rating-divider">&bull;</span>
                            <span class="rating-note">Master Luthier Inspected</span>
                        </div>

                        <!-- Pricing Banner -->
                        <div class="pricing-card-banner mb-3">
                            <div class="d-flex align-items-baseline">
                                <span class="currency-symbol">₹</span>
                                <span class="price-figure" id="displayUnitAmount"><?= number_format($unitPrice, 2) ?></span>
                                <span class="price-unit-tag">/ instrument</span>
                            </div>
                            <div class="tax-info">
                                <i class="fa-solid fa-check-circle text-success mr-1"></i> Inclusive of all taxes, pre-shipping setup, and digital warranty.
                            </div>
                        </div>

                        <!-- Sound & Craft Description -->
                        <div class="product-description-container mb-3">
                            <h6 class="spec-label">Acoustic &amp; Hardware Profile</h6>
                            <p class="description-text">
                                <?= nl2br($description) ?>
                            </p>
                        </div>

                        <!-- Purchase & Cart Action Form -->
                        <form action="cartaction.php" method="post" id="addToCartForm" class="product-order-form">
                            <!-- Hidden Inputs matching cartaction.php -->
                            <input type="hidden" name="productid" value="<?= $prodId ?>">
                            <input type="hidden" id="price" value="<?= $unitPrice ?>">
                            <input type="hidden" name="totalamount" id="total_hidden" value="<?= number_format($unitPrice, 2, '.', '') ?>">

                            <?php if ($isInCart): ?>
                                <div class="alert alert-warning d-flex align-items-center mb-3" style="border-radius: 8px; font-size: 13.5px; background: #fff8e1; border: 1px solid #ffe082; color: #856404; padding: 12px 16px;">
                                    <i class="fa-solid fa-triangle-exclamation mr-2" style="font-size: 18px; color: #d97706;"></i>
                                    <div>
                                        <strong>Item is already in cart</strong> (Qty: <?= $cartQty ?>). You can update quantities or proceed to checkout in your cart.
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Interactive Quantity & Subtotal Row -->
                            <div class="order-controls-box mb-3">
                                <div class="row align-items-center">
                                    
                                    <!-- Quantity Stepper -->
                                    <div class="col-sm-6 mb-2 mb-sm-0">
                                        <label for="quantity" class="control-label mb-1">
                                            <strong>Select Quantity:</strong>
                                        </label>
                                        <div class="deluxe-stepper">
                                            <button type="button" class="btn-step-action" id="btnMinus" title="Decrease Quantity" <?= (!$isAvailable || $isInCart) ? 'disabled' : '' ?>>
                                                <i class="fa-solid fa-minus"></i>
                                            </button>
                                            <input type="number" 
                                                   name="quantity" 
                                                   id="quantity" 
                                                   class="stepper-input" 
                                                   value="<?= $isInCart ? $cartQty : 1 ?>" 
                                                   min="1" 
                                                   max="<?= max(1, $stock) ?>" 
                                                   required 
                                                   <?= (!$isAvailable || $isInCart) ? 'disabled' : '' ?>>
                                            <button type="button" class="btn-step-action" id="btnPlus" title="Increase Quantity" <?= (!$isAvailable || $stock <= 1 || $isInCart) ? 'disabled' : '' ?>>
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="small text-muted mt-1" style="font-size: 10.5px;">
                                            Max available: <strong><?= $stock ?></strong> units
                                        </div>
                                    </div>

                                    <!-- Live Subtotal Calculation -->
                                    <div class="col-sm-6">
                                        <div class="live-subtotal-card">
                                            <div class="subtotal-label">Subtotal Amount</div>
                                            <div class="subtotal-value" id="totalDisplay">
                                                ₹<?= number_format($unitPrice * ($isInCart ? $cartQty : 1), 2) ?>
                                            </div>
                                            <div class="subtotal-hint">Ready for instant cart checkout</div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Cart Submit Button Row -->
                            <div class="action-buttons-strip mb-3">
                                <?php if ($isAvailable): ?>
                                    <?php if ($isInCart): ?>
                                        <button type="button" class="btn-add-to-cart" style="background:#f59e0b; color:#212529; border:none; cursor:pointer;" onclick="alert('Item is already in cart'); window.location='cartview.php';">
                                            <i class="fa-solid fa-check mr-1"></i> Item is already in cart
                                        </button>
                                        <a href="cartview.php" class="btn-view-current-cart">
                                            <i class="fa-solid fa-bag-shopping"></i> View Cart
                                        </a>
                                    <?php else: ?>
                                        <button type="submit" name="submit" class="btn-add-to-cart" id="btnSubmitCart">
                                            <i class="fa-solid fa-cart-shopping"></i> Add to Cart
                                        </button>
                                        <a href="cartview.php" class="btn-view-current-cart">
                                            <i class="fa-solid fa-bag-shopping"></i> View Cart
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button type="button" class="btn-add-to-cart disabled" disabled>
                                        <i class="fa-solid fa-circle-xmark"></i> Currently Out of Stock
                                    </button>
                                    <a href="catview.php" class="btn-view-current-cart">
                                        <i class="fa-solid fa-layer-group"></i> Browse Alternatives
                                    </a>
                                <?php endif; ?>
                            </div>

                        </form>

                        <!-- Assurance Perks Checklist -->
                        <div class="perks-checklist-grid">
                            <div class="perk-item">
                                <div class="perk-icon-wrap"><i class="fa-solid fa-shield-halved"></i></div>
                                <div class="perk-info">
                                    <strong>1-Year Warranty Included</strong>
                                    <span>Complimentary coverage against hardware defects</span>
                                </div>
                            </div>
                            <div class="perk-item">
                                <div class="perk-icon-wrap"><i class="fa-solid fa-truck-fast"></i></div>
                                <div class="perk-info">
                                    <strong>Insured Express Courier</strong>
                                    <span>Zero damage risk with padded custom instrument boxing</span>
                                </div>
                            </div>
                            <div class="perk-item">
                                <div class="perk-icon-wrap"><i class="fa-solid fa-rotate-left"></i></div>
                                <div class="perk-info">
                                    <strong>7-Day Hassle-Free Exchange</strong>
                                    <span>Verified swap guarantee if tone does not match your studio</span>
                                </div>
                            </div>
                            <div class="perk-item">
                                <div class="perk-icon-wrap"><i class="fa-solid fa-headset"></i></div>
                                <div class="perk-info">
                                    <strong>Musician Concierge Support</strong>
                                    <span>Direct assistance on string gauges, tuning, and pedals</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <?php endif; ?>

    </div>
</div>

<!-- ========================================================
     COMPACT PRODUCT-FOCUSED STYLING (MELODYMART OBSIDIAN & GOLD)
     ======================================================== -->
<style>
/* Page Wrapper & Base Reset */
.product-page-wrapper {
    background: #f7f5ef;
    min-height: 85vh;
    padding: 22px 0 45px;
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #272a25;
}

/* Compact Container Width for Balanced Desktop Layout */
.product-page-wrapper .container {
    max-width: 1040px;
}

/* Breadcrumb Bar */
.deluxe-breadcrumb {
    background: #ffffff;
    border: 1px solid #e8e5dc;
    border-radius: 10px;
    padding: 8px 16px;
    box-shadow: 0 2px 8px rgba(18, 20, 17, 0.03);
}

.breadcrumb-list {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    list-style: none;
    padding: 0;
    gap: 6px;
    font-size: 12px;
}

.breadcrumb-list a {
    color: #6a6d63;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

.breadcrumb-list a:hover {
    color: #b88f4b;
}

.breadcrumb-list .separator {
    font-size: 9px;
    color: #a4a69e;
}

.breadcrumb-list .active {
    color: #121411;
    font-weight: 600;
}

.btn-crumb-back {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 600;
    color: #121411;
    background: #f4f2ea;
    border: 1px solid #dcd7ca;
    padding: 4px 10px;
    border-radius: 6px;
    text-decoration: none !important;
    transition: all 0.2s ease;
}

.btn-crumb-back:hover {
    background: #121411;
    color: #ffffff;
    border-color: #121411;
}

/* Product Showcase Main Card */
.product-showcase-card {
    background: #ffffff;
    border: 1px solid #e8e5dc;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(18, 20, 17, 0.06);
    overflow: hidden;
}

/* Gallery Pane (Left Column) */
.product-gallery-pane {
    padding: 22px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background: linear-gradient(180deg, #fdfdfc 0%, #f7f5ef 100%);
    border-right: 1px solid #e8e5dc;
}

.image-stage-box {
    position: relative;
    width: 100%;
    min-height: 310px;
    background: #ffffff;
    border: 1px solid #ede9dc;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 18px;
    box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.02);
    overflow: hidden;
}

.gallery-badge-pill {
    position: absolute;
    top: 12px;
    left: 12px;
    background: #121411;
    color: #d7ad69;
    border: 1px solid rgba(215, 173, 105, 0.35);
    padding: 4px 10px;
    border-radius: 16px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    z-index: 2;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12);
}

.product-main-img {
    max-width: 100%;
    max-height: 250px;
    object-fit: contain;
    transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    filter: drop-shadow(0 10px 18px rgba(0, 0, 0, 0.09));
}

.image-stage-box:hover .product-main-img {
    transform: scale(1.04);
}

.gallery-trust-strip {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px dashed #e2decb;
}

.trust-badge-item {
    background: #ffffff;
    border: 1px solid #eae6d9;
    border-radius: 8px;
    padding: 6px 4px;
    text-align: center;
    font-size: 10px;
    font-weight: 600;
    color: #4f5249;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
    transition: transform 0.2s ease;
}

.trust-badge-item i {
    font-size: 13px;
    color: #b88f4b;
}

.trust-badge-item:hover {
    transform: translateY(-2px);
    border-color: #d7ad69;
}

/* Product Details Pane (Right Column) */
.product-details-pane {
    padding: 24px 28px;
    background: #ffffff;
}

.brand-pill {
    background: rgba(215, 173, 105, 0.15);
    border: 1px solid #d7ad69;
    color: #8c6721;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    padding: 3px 9px;
    border-radius: 16px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.subcat-pill {
    background: #f0eee6;
    border: 1px solid #ded9cb;
    color: #55584e;
    font-size: 10.5px;
    font-weight: 600;
    padding: 3px 9px;
    border-radius: 16px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.stock-pill {
    font-size: 10.5px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 16px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.stock-pill.in-stock {
    background: #e8f5e9;
    color: #1b5e20;
    border: 1px solid #a5d6a7;
}

.stock-pill.low-stock {
    background: #fff3e0;
    color: #e65100;
    border: 1px solid #ffcc80;
}

.stock-pill.out-stock {
    background: #ffebee;
    color: #b71c1c;
    border: 1px solid #ef9a9a;
}

.product-showcase-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 24px;
    font-weight: 700;
    color: #121411;
    margin: 8px 0 6px;
    line-height: 1.25;
    letter-spacing: -0.2px;
}

/* Rating & Tone Bar */
.musician-score-bar {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    font-size: 12px;
    margin-bottom: 12px;
}

.stars-gold i {
    color: #d7ad69;
    font-size: 11px;
}

.rating-text {
    font-weight: 700;
    color: #121411;
}

.rating-divider {
    color: #c4c2ba;
}

.rating-note {
    color: #72756a;
    font-size: 11.5px;
}

/* Pricing Card Banner */
.pricing-card-banner {
    background: #faf8f2;
    border: 1px solid #eae4d3;
    border-radius: 10px;
    padding: 10px 14px;
    margin-bottom: 14px;
}

.currency-symbol {
    font-size: 20px;
    font-weight: 700;
    color: #b88f4b;
    margin-right: 3px;
}

.price-figure {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 26px;
    font-weight: 700;
    color: #121411;
    line-height: 1;
}

.price-unit-tag {
    font-size: 12px;
    color: #797c72;
    margin-left: 6px;
    font-weight: 500;
}

.tax-info {
    font-size: 11px;
    color: #5d6057;
    margin-top: 3px;
    display: flex;
    align-items: center;
}

/* Description Box */
.product-description-container {
    padding-bottom: 12px;
    margin-bottom: 12px;
    border-bottom: 1px solid #eeeae0;
}

.spec-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.7px;
    color: #8c6721;
    font-weight: 700;
    margin-bottom: 3px;
}

.description-text {
    font-size: 13px;
    line-height: 1.5;
    color: #494c43;
    margin: 0;
}

/* Quantity & Subtotal Box */
.order-controls-box {
    background: #ffffff;
    border: 1.5px solid #eae5d7;
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 14px;
}

.control-label {
    font-size: 12px;
    color: #121411;
    margin-bottom: 5px;
}

.deluxe-stepper {
    display: inline-flex;
    align-items: center;
    background: #f7f5ef;
    border: 1.5px solid #dcd7ca;
    border-radius: 8px;
    overflow: hidden;
    height: 34px;
}

.btn-step-action {
    width: 34px;
    height: 34px;
    background: #ffffff;
    border: none;
    color: #121411;
    font-size: 11px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-step-action:hover:not(:disabled) {
    background: #d7ad69;
    color: #121411;
}

.btn-step-action:disabled {
    opacity: 0.4;
    cursor: not-allowed;
    background: #eae7dd;
}

.stepper-input {
    width: 46px;
    height: 34px;
    text-align: center;
    font-size: 14px;
    font-weight: 700;
    color: #121411;
    background: transparent;
    border: none;
    outline: none;
    -moz-appearance: textfield;
}

.stepper-input::-webkit-outer-spin-button,
.stepper-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Live Subtotal Card */
.live-subtotal-card {
    background: #121411;
    color: #ffffff;
    border-radius: 8px;
    padding: 6px 10px;
    border: 1px solid rgba(215, 173, 105, 0.35);
    text-align: center;
}

.subtotal-label {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: 0.7px;
    color: #d7ad69;
    font-weight: 600;
    margin-bottom: 1px;
}

.subtotal-value {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 18px;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.1;
}

.subtotal-hint {
    font-size: 9.5px;
    color: #a4a69e;
    margin-top: 1px;
}

/* Action Buttons Strip */
.action-buttons-strip {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 14px;
}

.btn-add-to-cart {
    flex: 1;
    min-width: 170px;
    background: linear-gradient(135deg, #d7ad69 0%, #b88f4b 100%);
    color: #121411 !important;
    font-weight: 700;
    font-size: 13.5px;
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    box-shadow: 0 4px 15px rgba(215, 173, 105, 0.35);
    transition: all 0.25s ease;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    text-decoration: none !important;
}

.btn-add-to-cart:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(215, 173, 105, 0.45);
    color: #121411 !important;
}

.btn-add-to-cart.disabled,
.btn-add-to-cart:disabled {
    background: #c5c2b8;
    color: #65675f !important;
    box-shadow: none;
    cursor: not-allowed;
    transform: none;
}

.btn-view-current-cart {
    background: #f7f5ef;
    color: #121411 !important;
    border: 1.5px solid #d8d3c4;
    font-weight: 600;
    font-size: 12.5px;
    padding: 9px 15px;
    border-radius: 8px;
    transition: all 0.25s ease;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

.btn-view-current-cart:hover {
    background: #121411;
    color: #ffffff !important;
    border-color: #121411;
}

/* Perks Checklist */
.perks-checklist-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
    gap: 8px;
    padding-top: 12px;
    border-top: 1px dashed #e4dfce;
}

.perk-item {
    display: flex;
    align-items: flex-start;
    gap: 8px;
}

.perk-icon-wrap {
    width: 26px;
    height: 26px;
    border-radius: 6px;
    background: rgba(215, 173, 105, 0.15);
    color: #b88f4b;
    display: grid;
    place-items: center;
    font-size: 11px;
    flex-shrink: 0;
}

.perk-info {
    line-height: 1.2;
}

.perk-info strong {
    font-size: 11px;
    color: #121411;
    display: block;
    margin-bottom: 1px;
}

.perk-info span {
    font-size: 10px;
    color: #72756a;
    display: block;
}

/* Empty State */
.product-not-found-card {
    background: #ffffff;
    border: 1px solid #e8e5dc;
    border-radius: 16px;
    padding: 45px 24px;
    max-width: 520px;
    margin: 30px auto;
    box-shadow: 0 8px 25px rgba(0,0,0,0.04);
}

.empty-icon-circle {
    width: 65px;
    height: 65px;
    border-radius: 50%;
    background: rgba(215, 173, 105, 0.15);
    color: #b88f4b;
    font-size: 28px;
    display: inline-grid;
    place-items: center;
}

.btn-gold-deluxe {
    background: linear-gradient(135deg, #d7ad69 0%, #b88f4b 100%);
    color: #121411;
    font-weight: 700;
    padding: 8px 18px;
    font-size: 13px;
    border-radius: 8px;
    text-decoration: none !important;
    border: none;
}

/* Responsive adjustments */
@media (max-width: 991px) {
    .product-gallery-pane {
        border-right: none;
        border-bottom: 1px solid #e8e5dc;
        padding: 20px;
    }
    .product-details-pane {
        padding: 20px;
    }
    .image-stage-box {
        min-height: 250px;
    }
    .product-showcase-title {
        font-size: 21px;
    }
}

@media (max-width: 576px) {
    .product-page-wrapper {
        padding: 16px 0 30px;
    }
    .gallery-trust-strip {
        grid-template-columns: 1fr;
    }
    .perks-checklist-grid {
        grid-template-columns: 1fr;
    }
    .action-buttons-strip {
        flex-direction: column;
    }
    .btn-add-to-cart,
    .btn-view-current-cart {
        width: 100%;
        text-align: center;
        justify-content: center;
    }
}
</style>

<!-- JavaScript for Live Total Calculation & Stepper Interaction -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const qtyInput       = document.getElementById('quantity');
    const priceVal       = parseFloat(document.getElementById('price').value) || 0;
    const totalDisplay   = document.getElementById('totalDisplay');
    const totalHidden    = document.getElementById('total_hidden');
    const btnMinus       = document.getElementById('btnMinus');
    const btnPlus        = document.getElementById('btnPlus');
    const maxStock       = qtyInput ? parseInt(qtyInput.getAttribute('max')) || 1 : 1;
    const minQty         = 1;

    function formatCurrency(num) {
        return '₹' + num.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function updateTotal() {
        if (!qtyInput) return;
        let qty = parseInt(qtyInput.value) || minQty;

        if (qty < minQty) {
            qty = minQty;
            qtyInput.value = qty;
        } else if (qty > maxStock) {
            qty = maxStock;
            qtyInput.value = qty;
        }

        const calculated = (qty * priceVal);
        if (totalDisplay) {
            totalDisplay.textContent = formatCurrency(calculated);
        }
        if (totalHidden) {
            totalHidden.value = calculated.toFixed(2);
        }

        // Stepper state updates
        if (btnMinus) {
            btnMinus.disabled = (qty <= minQty);
        }
        if (btnPlus) {
            btnPlus.disabled = (qty >= maxStock);
        }
    }

    if (qtyInput) {
        qtyInput.addEventListener('input', updateTotal);
        qtyInput.addEventListener('change', updateTotal);
    }

    if (btnMinus) {
        btnMinus.addEventListener('click', function() {
            let current = parseInt(qtyInput.value) || minQty;
            if (current > minQty) {
                qtyInput.value = current - 1;
                updateTotal();
            }
        });
    }

    if (btnPlus) {
        btnPlus.addEventListener('click', function() {
            let current = parseInt(qtyInput.value) || minQty;
            if (current < maxStock) {
                qtyInput.value = current + 1;
                updateTotal();
            }
        });
    }

    // Initialize immediate values on load
    updateTotal();
});
</script>

<?php include("footer.php"); ?>