<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("../dboperation.php");
include("header.php");

$obj = new dboperation();

if (!isset($_SESSION['customer_id'])) {
    echo "<script>window.location='../Guest/login.php';</script>";
    exit();
}

$customerid = (int)$_SESSION['customer_id'];

// Fetch all active cart items joined with products and subcategories
$s = "SELECT c.*, p.product_name, p.amount AS unit_amount, p.image, p.stock, p.brand, 
             s.name AS subcat_name, cat.category_name 
      FROM tbl_cart c 
      INNER JOIN tbl_product p ON p.product_id = c.product_id 
      LEFT JOIN tbl_subcategory s ON s.subcategory_id = p.subcat 
      LEFT JOIN tbl_category cat ON cat.category_id = s.category_id 
      WHERE c.customer_id = '$customerid' AND c.status = 'cart' 
      ORDER BY c.cart_id DESC";

$res = $obj->executequery($s);
$items = [];
$grandTotal = 0;
$totalQty = 0;

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $items[] = $row;
        $grandTotal += (float)$row['totalamount'];
        $totalQty += (int)$row['quantity'];
    }
}
?>

<div class="cart-page-wrapper">
    <div class="container">
        
        <!-- Breadcrumb / Progress Tracker -->
        <div class="checkout-stepper mb-4">
            <div class="step-item active">
                <span class="step-num"><i class="fa-solid fa-cart-shopping"></i></span>
                <span class="step-label">1. Shopping Cart</span>
            </div>
            <div class="step-line"></div>
            <div class="step-item">
                <span class="step-num"><i class="fa-solid fa-location-dot"></i></span>
                <span class="step-label">2. Delivery Address</span>
            </div>
            <div class="step-line"></div>
            <div class="step-item">
                <span class="step-num"><i class="fa-solid fa-credit-card"></i></span>
                <span class="step-label">3. Payment &amp; Warranty</span>
            </div>
        </div>

        <?php if (!empty($items)): ?>
            <div class="row">
                <!-- Left Column: Cart Items -->
                <div class="col-lg-8 mb-4">
                    <div class="cart-card">
                        <div class="cart-card-header d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <h3 class="cart-title mb-1">Your Instrument Selection</h3>
                                <p class="cart-subtitle mb-0">Review items, verify quantities, and proceed to secure checkout</p>
                            </div>
                            <span class="cart-count-badge">
                                <?= $totalQty ?> <?= ($totalQty === 1) ? 'Item' : 'Items' ?>
                            </span>
                        </div>

                        <div class="cart-items-list">
                            <?php foreach ($items as $item): ?>
                                <?php 
                                    $cartId    = (int)$item['cart_id'];
                                    $prodId    = (int)$item['product_id'];
                                    $prodName  = htmlspecialchars($item['product_name']);
                                    $unitPrice = (float)$item['unit_amount'];
                                    $lineTotal = (float)$item['totalamount'];
                                    $qty       = (int)$item['quantity'];
                                    $maxStock  = (int)$item['stock'];
                                    $imgName   = htmlspecialchars($item['image']);
                                    $brandName = !empty($item['brand']) ? htmlspecialchars($item['brand']) : 'MelodyMart';
                                    $subcat    = !empty($item['subcat_name']) ? htmlspecialchars($item['subcat_name']) : (!empty($item['category_name']) ? htmlspecialchars($item['category_name']) : 'Instrument');
                                ?>
                                <div class="cart-item-row">
                                    <!-- Product Image -->
                                    <div class="cart-item-img-wrap">
                                        <a href="ptview.php?productid=<?= $prodId ?>">
                                            <img src="../uploads/<?= $imgName ?>" 
                                                 alt="<?= $prodName ?>" 
                                                 onerror="this.src='../Guest/ins.jpg'">
                                        </a>
                                    </div>

                                    <!-- Product Meta & Info -->
                                    <div class="cart-item-info">
                                        <div class="d-flex align-items-center mb-1 flex-wrap" style="gap: 8px;">
                                            <span class="brand-pill"><?= $brandName ?></span>
                                            <span class="subcat-pill"><?= $subcat ?></span>
                                        </div>
                                        <h4 class="cart-item-name">
                                            <a href="ptview.php?productid=<?= $prodId ?>">
                                                <?= $prodName ?>
                                            </a>
                                        </h4>
                                        <div class="unit-price-display">
                                            ₹<?= number_format($unitPrice, 2) ?> <span class="unit-label">per instrument</span>
                                        </div>
                                        <div class="stock-hint">
                                            <?php if ($maxStock <= 5): ?>
                                                <span class="text-danger"><i class="fa-solid fa-fire"></i> Only <?= $maxStock ?> left in stock!</span>
                                            <?php else: ?>
                                                <span class="text-success"><i class="fa-solid fa-circle-check"></i> In Stock &bull; Ready to ship</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Quantity Stepper -->
                                    <div class="cart-item-stepper">
                                        <span class="stepper-label">Qty</span>
                                        <div class="stepper-controls">
                                            <a href="updatecart.php?action=dec&cartid=<?= $cartId ?>" 
                                               class="btn-step <?= ($qty <= 1) ? 'disabled' : '' ?>" 
                                               title="Decrease quantity">
                                                <i class="fa-solid fa-minus"></i>
                                            </a>
                                            <span class="stepper-qty"><?= $qty ?></span>
                                            <a href="updatecart.php?action=inc&cartid=<?= $cartId ?>" 
                                               class="btn-step <?= ($qty >= $maxStock) ? 'disabled' : '' ?>" 
                                               title="Increase quantity">
                                                <i class="fa-solid fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Line Total Amount -->
                                    <div class="cart-item-pricing">
                                        <div class="line-total-amount">
                                            ₹<?= number_format($lineTotal, 2) ?>
                                        </div>
                                        <a href="deletecart.php?cartid=<?= $cartId ?>" 
                                           class="btn-remove-item" 
                                           onclick="return confirm('Remove <?= addslashes($prodName) ?> from your cart?');"
                                           title="Remove item">
                                            <i class="fa-regular fa-trash-can mr-1"></i> Remove
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Card Footer Links -->
                        <div class="cart-card-footer d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
                            <a href="catview.php" class="btn btn-outline-dark">
                                <i class="fa-solid fa-arrow-left mr-1"></i> Continue Shopping
                            </a>
                            <div class="small text-muted">
                                <i class="fa-solid fa-shield-halved text-success mr-1"></i> All instruments covered by MelodyMart 12-Month Official Warranty
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Sticky Order Summary -->
                <div class="col-lg-4">
                    <div class="order-summary-card">
                        <div class="summary-header">
                            <h4 class="summary-title mb-0">Order Summary</h4>
                        </div>

                        <div class="summary-body">
                            <!-- Items Subtotal -->
                            <div class="summary-line">
                                <span>Cart Subtotal (<?= $totalQty ?> items)</span>
                                <span class="font-weight-bold">₹<?= number_format($grandTotal, 2) ?></span>
                            </div>

                            <!-- Instrument Acoustic Setup / QC -->
                            <div class="summary-line">
                                <span>Luthier Inspection &amp; Tuning</span>
                                <span class="badge badge-success px-2 py-1" style="background: #2e7d32; font-weight: 600;">FREE</span>
                            </div>

                            <!-- Shockproof Shipping -->
                            <div class="summary-line">
                                <span>Insured Express Courier</span>
                                <span class="badge badge-success px-2 py-1" style="background: #2e7d32; font-weight: 600;">FREE</span>
                            </div>

                            <!-- Warranty Certificate -->
                            <div class="summary-line">
                                <span>12-Month Warranty Card</span>
                                <span class="text-warning font-weight-bold" style="color: #b88636 !important;">
                                    <i class="fa-solid fa-certificate"></i> Included
                                </span>
                            </div>

                            <hr class="summary-divider">

                            <!-- Grand Total -->
                            <div class="summary-line total-line">
                                <div>
                                    <span class="total-label">Estimated Total</span>
                                    <span class="d-block text-muted small">Inclusive of all taxes &amp; duties</span>
                                </div>
                                <span class="total-amount-display">
                                    ₹<?= number_format($grandTotal, 2) ?>
                                </span>
                            </div>

                            <!-- Primary Checkout Button -->
                            <a href="payment.php?total_amount=<?= $grandTotal ?>" class="btn-checkout-primary mt-3">
                                <i class="fa-solid fa-arrow-right mr-2"></i> Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Empty Cart State -->
            <div class="empty-cart-card text-center">
                <div class="empty-cart-icon-wrap">
                    <i class="fa-solid fa-guitar empty-main-icon"></i>
                    <span class="empty-badge-icon"><i class="fa-solid fa-cart-arrow-down"></i></span>
                </div>
                <h2 class="empty-title">Your Instrument Bag is Empty</h2>
                <p class="empty-desc">
                    Looks like you haven't added any musical instruments, amplifiers, or sound gear to your cart yet.<br>
                    Browse our handcrafted collections to discover instruments tailored to your playing style.
                </p>
                <div class="d-flex justify-content-center flex-wrap" style="gap: 12px;">
                    <a href="catview.php" class="btn btn-warning btn-lg font-weight-bold px-4 text-dark" style="background: #d7ad69; border-color: #d7ad69; border-radius: 8px;">
                        <i class="fa-solid fa-compass mr-1"></i> Browse Categories
                    </a>
                    <a href="index.php" class="btn btn-outline-dark btn-lg px-4" style="border-radius: 8px;">
                        <i class="fa-solid fa-house mr-1"></i> Customer Home
                    </a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<style>
/* Modern E-Commerce Cart Stylesheet */
.cart-page-wrapper {
    background: #f7f5ef;
    min-height: 80vh;
    padding: 120px 0 60px;
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #121411;
}

/* Stepper Progress Bar */
.checkout-stepper {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    border: 1px solid #e8e5dc;
    border-radius: 12px;
    padding: 16px 24px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.03);
}
.step-item {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #8c8f87;
    font-size: 13px;
    font-weight: 600;
}
.step-item.active {
    color: #121411;
}
.step-item .step-num {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1ede4;
    color: #70736c;
    font-size: 13px;
}
.step-item.active .step-num {
    background: #d7ad69;
    color: #121411;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(215, 173, 105, 0.4);
}
.step-line {
    flex: 1;
    height: 2px;
    background: #e8e5dc;
    margin: 0 16px;
    max-width: 80px;
}

/* Main Cart Card */
.cart-card {
    background: #ffffff;
    border: 1px solid #e8e5dc;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0,0,0,0.04);
}
.cart-card-header {
    padding: 22px 28px;
    border-bottom: 1px solid #eeece5;
    background: #fcfbf9;
}
.cart-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 22px;
    font-weight: 700;
    color: #121411;
}
.cart-subtitle {
    font-size: 13px;
    color: #72756e;
}
.cart-count-badge {
    background: rgba(215, 173, 105, 0.2);
    color: #8f6828;
    border: 1px solid rgba(215, 173, 105, 0.4);
    font-size: 12px;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 20px;
}

/* Items List */
.cart-items-list {
    padding: 0;
}
.cart-item-row {
    display: flex;
    align-items: center;
    padding: 24px 28px;
    border-bottom: 1px solid #f0ede6;
    gap: 20px;
    transition: background 0.2s ease;
}
.cart-item-row:last-child {
    border-bottom: none;
}
.cart-item-row:hover {
    background: #faf8f3;
}

/* Image Wrap */
.cart-item-img-wrap {
    width: 100px;
    height: 90px;
    flex-shrink: 0;
    background: #faf9f6;
    border: 1px solid #e8e5dc;
    border-radius: 10px;
    padding: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.cart-item-img-wrap img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
}
.cart-item-img-wrap:hover img {
    transform: scale(1.08);
}

/* Info */
.cart-item-info {
    flex: 1;
    min-width: 180px;
}
.brand-pill {
    background: #121411;
    color: #f7f5ef;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 2px 8px;
    border-radius: 4px;
}
.subcat-pill {
    background: rgba(215, 173, 105, 0.25);
    color: #7c581a;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 4px;
}
.cart-item-name {
    font-size: 16px;
    font-weight: 700;
    line-height: 1.35;
    margin-bottom: 4px;
}
.cart-item-name a {
    color: #121411;
    text-decoration: none;
    transition: color 0.2s ease;
}
.cart-item-name a:hover {
    color: #b88636;
}
.unit-price-display {
    font-size: 13px;
    color: #555750;
    font-weight: 500;
}
.unit-label {
    font-size: 11px;
    color: #8c8f87;
}
.stock-hint {
    font-size: 11px;
    margin-top: 3px;
    font-weight: 500;
}

/* Stepper */
.cart-item-stepper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}
.stepper-label {
    font-size: 11px;
    color: #8c8f87;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}
.stepper-controls {
    display: inline-flex;
    align-items: center;
    border: 1px solid #dcd7ca;
    border-radius: 8px;
    background: #ffffff;
    overflow: hidden;
}
.btn-step {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #121411;
    text-decoration: none;
    background: #fcfbf9;
    transition: background 0.2s, color 0.2s;
    font-size: 11px;
}
.btn-step:hover:not(.disabled) {
    background: #d7ad69;
    color: #121411;
}
.btn-step.disabled {
    color: #ccc8be;
    pointer-events: none;
    background: #f7f5ef;
}
.stepper-qty {
    min-width: 36px;
    text-align: center;
    font-size: 13px;
    font-weight: 700;
    color: #121411;
}

/* Pricing and Remove Button */
.cart-item-pricing {
    text-align: right;
    min-width: 120px;
}
.line-total-amount {
    font-size: 18px;
    font-weight: 700;
    color: #198754;
    margin-bottom: 6px;
}
.btn-remove-item {
    font-size: 12px;
    color: #c93b2b;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    font-weight: 600;
    transition: color 0.2s, transform 0.2s;
}
.btn-remove-item:hover {
    color: #8c1e13;
    text-decoration: underline;
    transform: scale(1.03);
}

.cart-card-footer {
    padding: 18px 28px;
    background: #fcfbf9;
    border-top: 1px solid #eeece5;
}

/* Sticky Order Summary Card */
.order-summary-card {
    background: #ffffff;
    border: 1px solid #e8e5dc;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0,0,0,0.04);
    position: sticky;
    top: 100px;
}
.summary-header {
    padding: 20px 24px;
    border-bottom: 1px solid #eeece5;
    background: #fcfbf9;
}
.summary-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 20px;
    font-weight: 700;
    color: #121411;
}
.summary-body {
    padding: 24px;
}
.summary-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
    color: #4b4e47;
    margin-bottom: 12px;
}
.summary-divider {
    border-color: #eeece5;
    margin: 16px 0;
}
.total-line {
    font-size: 15px;
    align-items: flex-end;
    margin-bottom: 0;
}
.total-label {
    font-size: 16px;
    font-weight: 700;
    color: #121411;
}
.total-amount-display {
    font-size: 24px;
    font-weight: 700;
    color: #121411;
    font-family: 'DM Sans', sans-serif;
}

/* Checkout Button */
.btn-checkout-primary {
    background: linear-gradient(135deg, #121411 0%, #2b2e29 100%);
    color: #f7f5ef !important;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 14px 20px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-decoration: none !important;
    border: 1px solid rgba(215, 173, 105, 0.4);
    box-shadow: 0 6px 18px rgba(18, 20, 17, 0.18);
    transition: transform 0.25s ease, background 0.25s ease, box-shadow 0.25s ease;
}
.btn-checkout-primary:hover {
    background: linear-gradient(135deg, #d7ad69 0%, #c49954 100%);
    color: #121411 !important;
    border-color: #d7ad69;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(215, 173, 105, 0.4);
}

/* Trust Perks */
.trust-perks-list {
    border-top: 1px dashed #e4e0d5;
    padding-top: 18px;
}
.perk-row {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 12px;
    color: #63665e;
    margin-bottom: 10px;
    line-height: 1.4;
}
.perk-row i {
    font-size: 14px;
    margin-top: 2px;
    flex-shrink: 0;
}

/* Empty State */
.empty-cart-card {
    background: #ffffff;
    border: 1px solid #e8e5dc;
    border-radius: 16px;
    padding: 60px 30px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.04);
    max-width: 680px;
    margin: 30px auto;
}
.empty-cart-icon-wrap {
    position: relative;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: rgba(215, 173, 105, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
}
.empty-main-icon {
    font-size: 44px;
    color: #b88636;
}
.empty-badge-icon {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #121411;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    border: 2px solid #ffffff;
}
.empty-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 26px;
    font-weight: 700;
    margin-bottom: 10px;
    color: #121411;
}
.empty-desc {
    color: #6d7069;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 28px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .cart-item-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 14px;
    }
    .cart-item-pricing {
        text-align: left;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding-top: 10px;
        border-top: 1px dashed #eee;
    }
    .cart-item-stepper {
        flex-direction: row;
        width: 100%;
        justify-content: space-between;
    }
}
</style>

<?php include("footer.php"); ?>