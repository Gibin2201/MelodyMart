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

// Fetch customer info for prefilling
$custRes = $obj->executequery("SELECT * FROM tbl_customer WHERE customer_id = '$customerid'");
$customerData = ($custRes) ? mysqli_fetch_assoc($custRes) : null;
$defaultName  = $customerData ? htmlspecialchars($customerData['customer_name']) : '';
$defaultPhone = $customerData ? htmlspecialchars($customerData['phone_no']) : '';

// Fetch previous address if available
$addrRes = $obj->executequery("SELECT * FROM tbl_deliveryaddress WHERE cartmasterid IN (SELECT cartmaster_id FROM tbl_cartmaster WHERE customer_id = '$customerid') ORDER BY deliveryaddressid DESC LIMIT 1");
$prevAddr = ($addrRes) ? mysqli_fetch_assoc($addrRes) : null;
$defaultAddress = $prevAddr ? htmlspecialchars($prevAddr['address']) : '';
$defaultPincode = $prevAddr ? htmlspecialchars($prevAddr['pincode']) : '';

// Fetch cart items
$cartQuery = "SELECT c.*, p.product_name, p.amount AS unit_amount, p.image, p.brand, s.name AS subcat_name 
              FROM tbl_cart c 
              INNER JOIN tbl_product p ON p.product_id = c.product_id 
              LEFT JOIN tbl_subcategory s ON s.subcategory_id = p.subcat 
              WHERE c.customer_id = '$customerid' AND c.status = 'cart' 
              ORDER BY c.cart_id DESC";
$cartRes = $obj->executequery($cartQuery);
$cartItems = [];
$computedTotal = 0;
$totalQty = 0;

if ($cartRes) {
    while ($row = mysqli_fetch_assoc($cartRes)) {
        $cartItems[] = $row;
        $computedTotal += (float)$row['totalamount'];
        $totalQty += (int)$row['quantity'];
    }
}

// Total amount
$totalamount = isset($_GET['total_amount']) ? (float)$_GET['total_amount'] : $computedTotal;
if ($totalamount <= 0 && $computedTotal > 0) {
    $totalamount = $computedTotal;
}
?>

<div class="checkout-page-wrapper">
    <div class="container">

        <!-- Progress Stepper matching Cart -->
        <div class="checkout-stepper mb-4">
            <a href="cartview.php" class="step-item completed" title="Return to cart">
                <span class="step-num"><i class="fa-solid fa-check"></i></span>
                <span class="step-label">1. Shopping Cart</span>
            </a>
            <div class="step-line active"></div>
            <div class="step-item active">
                <span class="step-num"><i class="fa-solid fa-credit-card"></i></span>
                <span class="step-label">2. Delivery &amp; Payment</span>
            </div>
            <div class="step-line"></div>
            <div class="step-item">
                <span class="step-num"><i class="fa-solid fa-certificate"></i></span>
                <span class="step-label">3. Order Confirmation</span>
            </div>
        </div>

        <?php if ($totalamount > 0 && !empty($cartItems)): ?>
            <form id="checkoutForm" action="paymentaction.php" method="POST" autocomplete="off">
                <div class="row">

                    <!-- Left Column: Delivery Address & Payment Form -->
                    <div class="col-lg-8 mb-4">

                        <!-- SECTION 1: DELIVERY ADDRESS -->
                        <div class="checkout-card mb-4">
                            <div class="checkout-card-header d-flex align-items-center">
                                <div class="header-icon-wrap mr-3">
                                    <i class="fa-solid fa-truck-ramp-box"></i>
                                </div>
                                <div>
                                    <h4 class="checkout-card-title mb-0">Delivery Address</h4>
                                    <p class="checkout-card-subtitle mb-0">Where should we deliver your musical instruments?</p>
                                </div>
                            </div>

                            <div class="checkout-card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold" for="fullname">
                                            Recipient Name <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-icon-wrap">
                                            <i class="fa-solid fa-user field-icon"></i>
                                            <input type="text" 
                                                   name="fullname" 
                                                   id="fullname" 
                                                   class="form-control form-control-mm" 
                                                   placeholder="Full legal name" 
                                                   value="<?= $defaultName ?>" 
                                                   required 
                                                   pattern="[A-Za-z ]{3,50}">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold" for="phone">
                                            Contact Phone <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-icon-wrap">
                                            <i class="fa-solid fa-phone field-icon"></i>
                                            <input type="tel" 
                                                   name="phone" 
                                                   id="phone" 
                                                   class="form-control form-control-mm" 
                                                   placeholder="Mobile number for delivery updates" 
                                                   value="<?= $defaultPhone ?>" 
                                                   required 
                                                   pattern="^\+?[0-9\s\-]{10,15}$">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label font-weight-bold" for="address">
                                        Street Address &amp; House / Apartment Details <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="address" 
                                              id="address" 
                                              class="form-control form-control-mm" 
                                              rows="2" 
                                              placeholder="Flat, suite, building number, street name, and nearby landmark" 
                                              required><?= $defaultAddress ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold" for="pincode">
                                            Postal PIN Code <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-icon-wrap">
                                            <i class="fa-solid fa-location-dot field-icon"></i>
                                            <input type="text" 
                                                   name="pincode" 
                                                   id="pincode" 
                                                   class="form-control form-control-mm" 
                                                   placeholder="6-digit PIN code" 
                                                   value="<?= $defaultPincode ?>" 
                                                   required 
                                                   pattern="[0-9]{5,8}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3 d-flex align-items-center">
                                        <div class="shipping-notice-pill w-100">
                                            <i class="fa-solid fa-shield-halved text-success mr-2"></i>
                                            <span>Shockproof climate-controlled packaging included</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: PAYMENT METHOD -->
                        <div class="checkout-card">
                            <div class="checkout-card-header d-flex justify-content-between align-items-center flex-wrap">
                                <div class="d-flex align-items-center">
                                    <div class="header-icon-wrap mr-3">
                                        <i class="fa-solid fa-credit-card"></i>
                                    </div>
                                    <div>
                                        <h4 class="checkout-card-title mb-0">Payment Method</h4>
                                        <p class="checkout-card-subtitle mb-0">Bank-grade 256-bit encrypted transaction</p>
                                    </div>
                                </div>
                                <div class="payment-cards-icons mt-2 mt-md-0">
                                    <i class="fa-brands fa-cc-visa text-primary"></i>
                                    <i class="fa-brands fa-cc-mastercard text-danger"></i>
                                    <i class="fa-brands fa-cc-amex text-info"></i>
                                </div>
                            </div>

                            <div class="checkout-card-body">
                                <!-- Card Number -->
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold" for="card_number">
                                        Card Number <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-icon-wrap">
                                        <i class="fa-solid fa-credit-card field-icon"></i>
                                        <input type="text" 
                                               name="card_number" 
                                               id="card_number" 
                                               class="form-control form-control-mm" 
                                               placeholder="1234 5678 9012 3456" 
                                               maxlength="16" 
                                               required 
                                               pattern="[0-9]{16}">
                                    </div>
                                    <small class="text-muted">Enter 16-digit debit or credit card number without spaces</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold" for="expiry">
                                            Expiry Date (MM/YY) <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-icon-wrap">
                                            <i class="fa-regular fa-calendar-check field-icon"></i>
                                            <input type="text" 
                                                   name="expiry" 
                                                   id="expiry" 
                                                   class="form-control form-control-mm" 
                                                   placeholder="MM/YY" 
                                                   maxlength="5" 
                                                   required 
                                                   pattern="^(0[1-9]|1[0-2])\/\d{2}$">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold" for="cvv">
                                            Security Code (CVV) <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-icon-wrap">
                                            <i class="fa-solid fa-lock field-icon"></i>
                                            <input type="password" 
                                                   name="cvv" 
                                                   id="cvv" 
                                                   class="form-control form-control-mm" 
                                                   placeholder="3 or 4 digits" 
                                                   maxlength="4" 
                                                   required 
                                                   pattern="[0-9]{3,4}">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label font-weight-bold" for="card_name">
                                        Name on Card <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-icon-wrap">
                                        <i class="fa-solid fa-id-card field-icon"></i>
                                        <input type="text" 
                                               name="card_name" 
                                               id="card_name" 
                                               class="form-control form-control-mm" 
                                               placeholder="Exact name printed on card" 
                                               value="<?= $defaultName ?>" 
                                               required 
                                               pattern="[A-Za-z ]{3,50}">
                                    </div>
                                </div>

                                <!-- Hidden Total Amount Passed to paymentaction.php -->
                                <input type="hidden" name="total_amount" value="<?= $totalamount; ?>">
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Sticky Order Summary & Pay Button -->
                    <div class="col-lg-4">
                        <div class="order-summary-card">
                            <div class="summary-header">
                                <h4 class="summary-title mb-0">Order Summary</h4>
                            </div>

                            <div class="summary-body">
                                <!-- Mini Items Preview -->
                                <div class="mini-items-preview mb-3">
                                    <?php foreach ($cartItems as $cItem): ?>
                                        <div class="mini-item-row">
                                            <img src="../uploads/<?= htmlspecialchars($cItem['image']) ?>" 
                                                 alt="<?= htmlspecialchars($cItem['product_name']) ?>" 
                                                 class="mini-item-thumb" 
                                                 onerror="this.src='../Guest/ins.jpg'">
                                            <div class="mini-item-meta">
                                                <div class="mini-item-name"><?= htmlspecialchars($cItem['product_name']) ?></div>
                                                <div class="mini-item-sub">
                                                    Qty: <?= (int)$cItem['quantity'] ?> &times; ₹<?= number_format((float)$cItem['unit_amount'], 2) ?>
                                                </div>
                                            </div>
                                            <div class="mini-item-total">
                                                ₹<?= number_format((float)$cItem['totalamount'], 2) ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <hr class="summary-divider">

                                <!-- Financial Breakdown -->
                                <div class="summary-line">
                                    <span>Items Subtotal (<?= $totalQty ?> items)</span>
                                    <span class="font-weight-bold">₹<?= number_format($totalamount, 2) ?></span>
                                </div>
                                <div class="summary-line">
                                    <span>Insured Courier Delivery</span>
                                    <span class="badge badge-success px-2 py-1" style="background: #2e7d32; font-weight: 600;">FREE</span>
                                </div>
                                <div class="summary-line">
                                    <span>Digital Warranty Certificate</span>
                                    <span class="text-warning font-weight-bold" style="color: #b88636 !important;">
                                        <i class="fa-solid fa-certificate"></i> Included
                                    </span>
                                </div>

                                <hr class="summary-divider">

                                <!-- Grand Total Line -->
                                <div class="summary-line total-line mb-3">
                                    <div>
                                        <span class="total-label">Grand Total</span>
                                        <span class="d-block text-muted small">All taxes &amp; shipping included</span>
                                    </div>
                                    <span class="total-amount-display">
                                        ₹<?= number_format($totalamount, 2) ?>
                                    </span>
                                </div>

                                <!-- Primary Submit Button -->
                                <button type="submit" name="submit" class="btn-checkout-primary w-100">
                                    <i class="fa-solid fa-shield-check mr-2"></i> Pay Now (₹<?= number_format($totalamount, 2) ?>)
                                </button>

                                <div class="text-center mt-3">
                                    <a href="cartview.php" class="text-muted small" style="text-decoration: underline;">
                                        <i class="fa-solid fa-arrow-left mr-1"></i> Return to Shopping Cart
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

        <?php else: ?>
            <!-- Fallback If No Items -->
            <div class="empty-cart-card text-center">
                <div class="empty-cart-icon-wrap">
                    <i class="fa-solid fa-receipt empty-main-icon"></i>
                </div>
                <h2 class="empty-title">No Active Items for Payment</h2>
                <p class="empty-desc">
                    Your cart appears to be empty or has already been completed.<br>
                    Explore our instrument departments to start a new order.
                </p>
                <a href="catview.php" class="btn btn-warning btn-lg font-weight-bold px-4 text-dark" style="background: #d7ad69; border-color: #d7ad69; border-radius: 8px;">
                    <i class="fa-solid fa-compass mr-1"></i> Browse Instruments
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>

<style>
/* Checkout Page Stylesheet - Matching Cart Luxury Aesthetic */
.checkout-page-wrapper {
    background: #f7f5ef;
    min-height: 85vh;
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
    text-decoration: none;
}
.step-item.active {
    color: #121411;
}
.step-item.completed {
    color: #2e7d32;
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
.step-item.completed .step-num {
    background: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #c8e6c9;
}
.step-line {
    flex: 1;
    height: 2px;
    background: #e8e5dc;
    margin: 0 16px;
    max-width: 80px;
}
.step-line.active {
    background: #d7ad69;
}

/* Checkout Cards */
.checkout-card {
    background: #ffffff;
    border: 1px solid #e8e5dc;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0,0,0,0.04);
}
.checkout-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #eeece5;
    background: #fcfbf9;
}
.header-icon-wrap {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: rgba(215, 173, 105, 0.18);
    color: #b88636;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}
.checkout-card-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 19px;
    font-weight: 700;
    color: #121411;
}
.checkout-card-subtitle {
    font-size: 12px;
    color: #72756e;
}
.checkout-card-body {
    padding: 24px;
}

/* Form inputs with icons */
.input-icon-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.field-icon {
    position: absolute;
    left: 14px;
    color: #8c8f87;
    font-size: 14px;
    pointer-events: none;
}
.form-control-mm {
    border-radius: 8px;
    border: 1px solid #dcd7ca;
    padding: 10px 14px 10px 40px;
    font-size: 14px;
    color: #121411;
    background: #ffffff;
    transition: border-color 0.25s, box-shadow 0.25s;
}
.form-control-mm:focus {
    border-color: #d7ad69;
    box-shadow: 0 0 0 3px rgba(215, 173, 105, 0.25);
    background: #ffffff;
}
textarea.form-control-mm {
    padding-left: 14px;
}

.shipping-notice-pill {
    background: #f5f9f6;
    border: 1px solid #d1e7dd;
    color: #0f5132;
    font-size: 12px;
    font-weight: 500;
    padding: 10px 14px;
    border-radius: 8px;
}

.payment-cards-icons {
    font-size: 26px;
    display: flex;
    gap: 10px;
}

/* Mini Item Preview in Summary */
.mini-items-preview {
    max-height: 220px;
    overflow-y: auto;
    padding-right: 4px;
}
.mini-item-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    padding-bottom: 10px;
    border-bottom: 1px solid #f2efe8;
}
.mini-item-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}
.mini-item-thumb {
    width: 48px;
    height: 48px;
    border-radius: 6px;
    object-fit: contain;
    background: #faf9f6;
    border: 1px solid #e8e5dc;
    padding: 2px;
}
.mini-item-meta {
    flex: 1;
    min-width: 0;
}
.mini-item-name {
    font-size: 13px;
    font-weight: 600;
    color: #121411;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mini-item-sub {
    font-size: 11px;
    color: #72756e;
}
.mini-item-total {
    font-size: 13px;
    font-weight: 700;
    color: #198754;
}

/* Order Summary */
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
    margin: 14px 0;
}
.total-line {
    font-size: 15px;
    align-items: flex-end;
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
}

/* Checkout Button */
.btn-checkout-primary {
    background: linear-gradient(135deg, #121411 0%, #2b2e29 100%);
    color: #f7f5ef !important;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 14px 20px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-decoration: none !important;
    border: 1px solid rgba(215, 173, 105, 0.4);
    box-shadow: 0 6px 18px rgba(18, 20, 17, 0.18);
    transition: transform 0.25s ease, background 0.25s ease, box-shadow 0.25s ease;
    cursor: pointer;
}
.btn-checkout-primary:hover {
    background: linear-gradient(135deg, #d7ad69 0%, #c49954 100%);
    color: #121411 !important;
    border-color: #d7ad69;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(215, 173, 105, 0.4);
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
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: rgba(215, 173, 105, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}
.empty-main-icon {
    font-size: 40px;
    color: #b88636;
}
.empty-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 10px;
    color: #121411;
}
.empty-desc {
    color: #6d7069;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 24px;
}
</style>

<script>
// Frontend Form Validation
document.getElementById("checkoutForm").addEventListener("submit", function(event) {
    let errors = [];
    let fullname = document.querySelector("[name='fullname']").value.trim();
    let phone    = document.querySelector("[name='phone']").value.trim();
    let address  = document.querySelector("[name='address']").value.trim();
    let pincode  = document.querySelector("[name='pincode']").value.trim();

    let card     = document.querySelector("[name='card_number']").value.trim();
    let expiry   = document.querySelector("[name='expiry']").value.trim();
    let cvv      = document.querySelector("[name='cvv']").value.trim();
    let cardName = document.querySelector("[name='card_name']").value.trim();

    if (fullname.length < 3) errors.push("Please enter a valid recipient full name.");
    if (!/^\+?[0-9\s\-]{10,15}$/.test(phone)) errors.push("Please enter a valid 10-digit mobile number.");
    if (address.length < 5) errors.push("Please enter a complete delivery address.");
    if (!/^[0-9]{5,8}$/.test(pincode)) errors.push("Please enter a valid postal PIN code.");

    if (!/^[0-9]{16}$/.test(card)) errors.push("Card number must be 16 numeric digits.");
    if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) errors.push("Card expiry must be in MM/YY format.");
    if (!/^[0-9]{3,4}$/.test(cvv)) errors.push("CVV must be 3 or 4 digits.");
    if (cardName.length < 3) errors.push("Please enter the name printed on your card.");

    if (errors.length > 0) {
        event.preventDefault();
        alert(errors.join("\n"));
    }
});
</script>

<?php include("footer.php"); ?>
