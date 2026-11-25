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
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Fetch order master record
$masterQuery = "SELECT cm.*, p.payment_date, p.pamount, da.personname, da.mobno, da.address, da.pincode 
                FROM tbl_cartmaster cm 
                LEFT JOIN tbl_payment p ON p.cartmaster_id = cm.cartmaster_id 
                LEFT JOIN tbl_deliveryaddress da ON da.cartmasterid = cm.cartmaster_id 
                WHERE cm.cartmaster_id = '$orderId' AND cm.customer_id = '$customerid'";
$masterRes = $obj->executequery($masterQuery);
$order = ($masterRes) ? mysqli_fetch_assoc($masterRes) : null;

// Fetch ordered items
$items = [];
if ($order) {
    $itemsQuery = "SELECT c.*, pro.product_name, pro.image, pro.brand, sub.name AS subcat_name 
                   FROM tbl_cart c 
                   INNER JOIN tbl_product pro ON pro.product_id = c.product_id 
                   LEFT JOIN tbl_subcategory sub ON sub.subcategory_id = pro.subcat 
                   WHERE c.cartmaster_id = '$orderId' AND c.customer_id = '$customerid'";
    $itemsRes = $obj->executequery($itemsQuery);
    if ($itemsRes) {
        while ($row = mysqli_fetch_assoc($itemsRes)) {
            $items[] = $row;
        }
    }
}
?>

<div class="ordersuccess-wrapper">
    <div class="container">

        <!-- Progress Stepper Step 3 Completed -->
        <div class="checkout-stepper mb-4">
            <div class="step-item completed">
                <span class="step-num"><i class="fa-solid fa-check"></i></span>
                <span class="step-label">1. Shopping Cart</span>
            </div>
            <div class="step-line active"></div>
            <div class="step-item completed">
                <span class="step-num"><i class="fa-solid fa-check"></i></span>
                <span class="step-label">2. Delivery &amp; Payment</span>
            </div>
            <div class="step-line active"></div>
            <div class="step-item active">
                <span class="step-num"><i class="fa-solid fa-circle-check"></i></span>
                <span class="step-label">3. Order Confirmed</span>
            </div>
        </div>

        <?php if ($order && !empty($items)): ?>
            <div class="row justify-content-center">
                <div class="col-lg-9 col-md-11">

                    <!-- Success Confirmation Banner Card -->
                    <div class="success-card mb-4 text-center">
                        <div class="success-icon-wrap mb-3">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <span class="badge badge-success px-3 py-1 mb-2 font-weight-bold" style="background: #2e7d32; letter-spacing: 0.5px;">
                            PAYMENT SUCCESSFUL &bull; ORDER VERIFIED
                        </span>
                        <h2 class="success-title">Thank You for Your Order!</h2>
                        <p class="success-subtitle">
                            Your instrument order <strong class="text-dark">#MM-ORD-<?= str_pad((string)$orderId, 5, '0', STR_PAD_LEFT) ?></strong> has been received and is being prepared for calibration &amp; dispatch.
                        </p>
                    </div>

                    <!-- Order Details Grid Card -->
                    <div class="order-details-card mb-4">
                        <div class="row p-4 border-bottom" style="border-color: #f0ede6 !important; background: #fcfbf9;">
                            <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
                                <span class="meta-label">Order Number</span>
                                <span class="meta-val">#MM-ORD-<?= str_pad((string)$orderId, 5, '0', STR_PAD_LEFT) ?></span>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
                                <span class="meta-label">Payment Date</span>
                                <span class="meta-val"><?= htmlspecialchars($order['payment_date'] ?: date('Y-m-d')) ?></span>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
                                <span class="meta-label">Total Amount</span>
                                <span class="meta-val text-success">₹<?= number_format((float)$order['grandtotal'], 2) ?></span>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <span class="meta-label">Payment Status</span>
                                <span class="badge badge-success px-2 py-1" style="background: #2e7d32;">Paid &bull; Verified</span>
                            </div>
                        </div>

                        <div class="p-4">
                            <!-- Shipping Destination -->
                            <div class="shipping-info-box mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fa-solid fa-location-dot text-warning mr-2" style="color: #b88636 !important; font-size: 16px;"></i>
                                    <h5 class="mb-0 font-weight-bold text-dark" style="font-size: 15px;">Shipping Destination</h5>
                                </div>
                                <div class="text-muted small pl-4" style="line-height: 1.5;">
                                    <strong><?= htmlspecialchars($order['personname']) ?></strong> &bull; <?= htmlspecialchars($order['mobno']) ?><br>
                                    <?= htmlspecialchars($order['address']) ?>, PIN: <?= htmlspecialchars($order['pincode']) ?>
                                </div>
                            </div>

                            <!-- Purchased Instruments Table -->
                            <h5 class="font-weight-bold mb-3 text-dark" style="font-size: 16px;">
                                <i class="fa-solid fa-guitar mr-1 text-warning"></i> Purchased Instruments &amp; Gear
                            </h5>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead style="background: #faf8f2; font-size: 12px; text-transform: uppercase; color: #555;">
                                        <tr>
                                            <th class="py-2 px-3">Item</th>
                                            <th class="py-2 text-center">Qty</th>
                                            <th class="py-2 text-right">Price</th>
                                            <th class="py-2 text-center">Digital Warranty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td class="py-3 px-3">
                                                    <div class="d-flex align-items-center">
                                                        <img src="../uploads/<?= htmlspecialchars($item['image']) ?>" 
                                                             alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                                             style="width: 55px; height: 50px; object-fit: contain; background: #faf9f6; border-radius: 6px; border: 1px solid #e8e5dc; padding: 2px; margin-right: 12px;"
                                                             onerror="this.src='../Guest/ins.jpg'">
                                                        <div>
                                                            <div class="font-weight-bold text-dark mb-1" style="font-size: 14px;">
                                                                <?= htmlspecialchars($item['product_name']) ?>
                                                            </div>
                                                            <span class="badge badge-warning text-dark font-weight-bold" style="background: rgba(215, 173, 105, 0.25); font-size: 10px;">
                                                                <?= htmlspecialchars($item['subcat_name'] ?: 'Instrument') ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3 text-center align-middle font-weight-bold">
                                                    <?= (int)$item['quantity'] ?>
                                                </td>
                                                <td class="py-3 text-right align-middle font-weight-bold text-success" style="font-size: 15px;">
                                                    ₹<?= number_format((float)$item['totalamount'], 2) ?>
                                                </td>
                                                <td class="py-3 text-center align-middle">
                                                    <a href="warrantycard.php?cartid=<?= (int)$item['cart_id'] ?>" 
                                                       class="btn btn-sm btn-outline-warning text-dark font-weight-bold" 
                                                       style="background: rgba(215, 173, 105, 0.2); border-color: #d7ad69; border-radius: 6px;">
                                                        <i class="fa-solid fa-certificate text-warning mr-1"></i> Warranty Card
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Card Action Buttons -->
                        <div class="p-4 border-top d-flex justify-content-between align-items-center flex-wrap" style="background: #fcfbf9; gap: 12px;">
                            <a href="vieworders.php" class="btn btn-outline-dark font-weight-bold px-4" style="border-radius: 8px;">
                                <i class="fa-solid fa-box-archive mr-1"></i> View Order History
                            </a>
                            <a href="catview.php" class="btn btn-warning font-weight-bold text-dark px-4" style="background: #d7ad69; border-color: #d7ad69; border-radius: 8px;">
                                <i class="fa-solid fa-compass mr-1"></i> Explore More Instruments
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        <?php else: ?>
            <div class="empty-cart-card text-center">
                <div class="empty-cart-icon-wrap">
                    <i class="fa-solid fa-receipt empty-main-icon"></i>
                </div>
                <h2 class="empty-title">Order Information Unavailable</h2>
                <p class="empty-desc">
                    Unable to find details for this order. You can review your completed purchases anytime in your Order History.
                </p>
                <a href="vieworders.php" class="btn btn-warning font-weight-bold px-4 text-dark" style="background: #d7ad69; border-color: #d7ad69; border-radius: 8px;">
                    <i class="fa-solid fa-box-archive mr-1"></i> View My Orders
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>

<style>
.ordersuccess-wrapper {
    background: #f7f5ef;
    min-height: 85vh;
    padding: 120px 0 60px;
    font-family: 'DM Sans', -apple-system, sans-serif;
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
    background: #2e7d32;
}

/* Success Card */
.success-card {
    background: #ffffff;
    border: 1px solid #e8e5dc;
    border-radius: 14px;
    padding: 40px 24px 30px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.04);
}
.success-icon-wrap {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: #e8f5e9;
    color: #2e7d32;
    font-size: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    border: 2px solid #a5d6a7;
    box-shadow: 0 4px 14px rgba(46, 125, 50, 0.2);
}
.success-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 28px;
    font-weight: 700;
    color: #121411;
    margin-bottom: 8px;
}
.success-subtitle {
    color: #6a6d66;
    font-size: 15px;
    max-width: 580px;
    margin: 0 auto;
}

/* Order Details Card */
.order-details-card {
    background: #ffffff;
    border: 1px solid #e8e5dc;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0,0,0,0.04);
}
.meta-label {
    display: block;
    font-size: 11px;
    text-transform: uppercase;
    color: #8c8f87;
    letter-spacing: 0.5px;
    font-weight: 600;
    margin-bottom: 3px;
}
.meta-val {
    font-size: 15px;
    font-weight: 700;
    color: #121411;
}

.shipping-info-box {
    background: #faf8f2;
    border: 1px solid #eee9dc;
    border-radius: 10px;
    padding: 16px 20px;
}
</style>

<?php include("footer.php"); ?>
