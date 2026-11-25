<?php 
include("header.php"); 
include_once("../dboperation.php");
$obj = new dboperation(); 

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Fetch master order record
$masterSql = "SELECT cm.*, 
    p.payment_date, p.pamount, p.pstatus, p.payment_id, p.commission,
    cus.customer_name, cus.phone_no, cus.email, cus.reg_date,
    da.personname, da.mobno, da.address, da.pincode
FROM tbl_cartmaster cm
LEFT JOIN tbl_payment p ON p.cartmaster_id = cm.cartmaster_id
LEFT JOIN tbl_customer cus ON cus.customer_id = cm.customer_id
LEFT JOIN tbl_deliveryaddress da ON da.cartmasterid = cm.cartmaster_id
WHERE cm.cartmaster_id = '$orderId'";

$masterRes = $obj->executequery($masterSql);
$order = ($masterRes) ? mysqli_fetch_assoc($masterRes) : null;

// Fetch items
$items = [];
if ($order) {
    $itemsSql = "SELECT c.*, pro.product_name, pro.image, pro.amount, pro.brand, pro.stock, sub.name AS subcat_name 
                 FROM tbl_cart c 
                 INNER JOIN tbl_product pro ON pro.product_id = c.product_id 
                 LEFT JOIN tbl_subcategory sub ON sub.subcategory_id = pro.subcat 
                 WHERE c.cartmaster_id = '$orderId'";
    $itemsRes = $obj->executequery($itemsSql);
    if ($itemsRes) {
        while ($row = mysqli_fetch_assoc($itemsRes)) {
            $items[] = $row;
        }
    }
}
?>

<div class="main-panel"> 
    <div class="content"> 
        <div class="container-fluid"> 
            
            <?php if (!$order): ?>
                <div class="card p-5 text-center my-4">
                    <div class="mb-3" style="font-size: 40px; color: #ffb300;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <h4>Order Not Found</h4>
                    <p class="text-muted">The requested order (#<?php echo $orderId; ?>) does not exist in the database.</p>
                    <div>
                        <a href="ordersview.php" class="btn btn-dark"><i class="fa-solid fa-arrow-left mr-1"></i> Return to Orders List</a>
                    </div>
                </div>
            <?php else: 
                $orderRef = "#MM-ORD-" . str_pad($orderId, 5, '0', STR_PAD_LEFT);
                $customerName = !empty($order['customer_name']) ? $order['customer_name'] : "Customer #" . $order['customer_id'];
                $recipient = !empty($order['personname']) ? $order['personname'] : $customerName;
                $phone = !empty($order['mobno']) ? $order['mobno'] : $order['phone_no'];
                $grandTotal = (float)$order['grandtotal'];
                $taxEst = $grandTotal * 0.18;
                $subtotalEst = $grandTotal - $taxEst;
            ?>

                <!-- Page Header with Print & Navigation -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap d-print-none">
                    <div>
                        <a href="ordersview.php" class="btn btn-sm btn-outline-secondary font-weight-bold mb-2">
                            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Orders
                        </a>
                        <h4 class="page-title mb-1">
                            Order Overview &bull; <span class="text-warning"><?php echo $orderRef; ?></span>
                        </h4>
                        <p class="text-muted small mb-0">Purchased on <?php echo htmlspecialchars($order['payment_date'] ?: date('Y-m-d')); ?> &bull; Payment Verified</p>
                    </div>
                    <div class="mt-2 mt-sm-0">
                        <button onclick="window.print()" class="btn btn-dark font-weight-bold">
                            <i class="fa-solid fa-print mr-1"></i> Print Invoice / Packing Slip
                        </button>
                    </div>
                </div>

                <!-- Printable Invoice Container -->
                <div class="printable-invoice-wrapper">

                    <!-- Order Header Banner -->
                    <div class="card shadow-sm border-0 mb-4" style="background: #121411; color: #ffffff; border-radius: 12px;">
                        <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge badge-warning text-dark font-weight-bold px-3 py-1 mr-2" style="background: #d7ad69; font-size: 13px;">
                                        <?php echo $orderRef; ?>
                                    </span>
                                    <span class="badge badge-success px-3 py-1 font-weight-bold" style="background: #2e7d32; font-size: 13px;">
                                        <i class="fa-solid fa-check mr-1"></i> <?php echo htmlspecialchars(ucfirst($order['cmstatus'] ?: 'Paid')); ?>
                                    </span>
                                </div>
                                <h3 class="mb-0 text-white font-weight-bold" style="font-family: 'Playfair Display', Georgia, serif;">
                                    Customer Invoice &amp; Dispatch Order
                                </h3>
                            </div>
                            <div class="text-right mt-3 mt-md-0">
                                <span class="text-muted small d-block" style="color: #bbb !important;">Total Amount Paid</span>
                                <h2 class="font-weight-bold mb-0 text-success" style="color: #66bb6a !important;">
                                    ₹<?php echo number_format($grandTotal, 2); ?>
                                </h2>
                            </div>
                        </div>
                    </div>

                    <!-- Customer & Shipping Information Cards -->
                    <div class="row mb-4">
                        
                        <!-- Customer Profile Info -->
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="card h-100 shadow-sm border-0" style="border: 1px solid #e8e5dc !important; border-radius: 12px;">
                                <div class="card-header bg-light py-3 px-4 border-bottom">
                                    <h6 class="mb-0 font-weight-bold text-dark">
                                        <i class="fa-solid fa-user-circle text-warning mr-2"></i> Buyer Profile
                                    </h6>
                                </div>
                                <div class="card-body p-4">
                                    <h5 class="font-weight-bold text-dark mb-2"><?php echo htmlspecialchars($customerName); ?></h5>
                                    <p class="mb-2 text-muted small">
                                        <i class="fa-solid fa-id-badge mr-2 text-muted"></i> Customer ID: <strong>#CUS-<?php echo str_pad($order['customer_id'], 4, '0', STR_PAD_LEFT); ?></strong>
                                    </p>
                                    <?php if (!empty($order['email'])): ?>
                                        <p class="mb-2 text-muted small">
                                            <i class="fa-regular fa-envelope mr-2 text-muted"></i> Email: <strong><?php echo htmlspecialchars($order['email']); ?></strong>
                                        </p>
                                    <?php endif; ?>
                                    <?php if (!empty($order['phone_no'])): ?>
                                        <p class="mb-0 text-muted small">
                                            <i class="fa-solid fa-phone mr-2 text-muted"></i> Phone: <strong><?php echo htmlspecialchars($order['phone_no']); ?></strong>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping & Delivery Address -->
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm border-0" style="border: 1px solid #e8e5dc !important; border-radius: 12px;">
                                <div class="card-header bg-light py-3 px-4 border-bottom">
                                    <h6 class="mb-0 font-weight-bold text-dark">
                                        <i class="fa-solid fa-truck-fast text-success mr-2"></i> Shipping Destination
                                    </h6>
                                </div>
                                <div class="card-body p-4">
                                    <h5 class="font-weight-bold text-dark mb-2">
                                        <i class="fa-solid fa-location-dot text-danger mr-1"></i> <?php echo htmlspecialchars($recipient); ?>
                                    </h5>
                                    <p class="mb-2 text-muted small" style="line-height: 1.6;">
                                        <?php echo nl2br(htmlspecialchars($order['address'] ?: "Primary customer delivery address")); ?>
                                    </p>
                                    <?php if (!empty($order['pincode'])): ?>
                                        <p class="mb-2 text-muted small">
                                            <strong>Postal Code / Pincode:</strong> <?php echo htmlspecialchars($order['pincode']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if (!empty($phone)): ?>
                                        <p class="mb-0 text-muted small">
                                            <strong>Contact Number:</strong> <?php echo htmlspecialchars($phone); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Itemized Purchased Instruments Table -->
                    <div class="card shadow-sm border-0 mb-4" style="border: 1px solid #e8e5dc !important; border-radius: 12px; overflow: hidden;">
                        <div class="card-header bg-light py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 font-weight-bold text-dark">
                                <i class="fa-solid fa-boxes-stacked text-primary mr-2"></i> Purchased Items (<?php echo count($items); ?>)
                            </h6>
                            <span class="badge badge-light border text-muted">
                                Dispatch Preparation
                            </span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light text-muted" style="font-size: 12px; text-transform: uppercase;">
                                        <tr>
                                            <th class="py-3 px-4">Instrument</th>
                                            <th class="py-3">Subcategory / Brand</th>
                                            <th class="py-3 text-center">Unit Price</th>
                                            <th class="py-3 text-center">Qty</th>
                                            <th class="py-3 text-right px-4">Line Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($items)): ?>
                                            <?php foreach ($items as $item): 
                                                $lineTotal = (float)$item['totalamount'];
                                                $unitPrice = ($item['quantity'] > 0) ? ($lineTotal / $item['quantity']) : $lineTotal;
                                            ?>
                                                <tr>
                                                    <td class="py-3 px-4 align-middle">
                                                        <div class="d-flex align-items-center">
                                                            <img src="../uploads/<?php echo htmlspecialchars($item['image']); ?>" 
                                                                 alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                                                 class="rounded border mr-3" 
                                                                 style="width: 55px; height: 55px; object-fit: contain; background: #fafafa; padding: 3px;"
                                                                 onerror="this.src='../Guest/ins.jpg'">
                                                            <div>
                                                                <h6 class="font-weight-bold mb-0 text-dark"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                                                <span class="small text-muted">Product Code: #<?php echo (int)$item['product_id']; ?></span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="py-3 align-middle">
                                                        <?php if (!empty($item['subcat_name'])): ?>
                                                            <span class="badge badge-light border px-2 py-1 font-weight-bold"><?php echo htmlspecialchars($item['subcat_name']); ?></span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($item['brand'])): ?>
                                                            <div class="small text-muted mt-1"><?php echo htmlspecialchars($item['brand']); ?></div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="py-3 text-center align-middle">
                                                        ₹<?php echo number_format($unitPrice, 2); ?>
                                                    </td>
                                                    <td class="py-3 text-center align-middle">
                                                        <span class="badge badge-dark px-2 py-1 font-weight-bold"><?php echo (int)$item['quantity']; ?></span>
                                                    </td>
                                                    <td class="py-3 text-right px-4 align-middle font-weight-bold text-dark">
                                                        ₹<?php echo number_format($lineTotal, 2); ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">No items found for this order.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Financial Totals Footer -->
                        <div class="card-footer bg-light p-4">
                            <div class="row justify-content-end">
                                <div class="col-md-5 col-lg-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Items Subtotal:</span>
                                        <span class="font-weight-bold text-dark small">₹<?php echo number_format($subtotalEst, 2); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">GST / Taxes (18% Incl.):</span>
                                        <span class="font-weight-bold text-dark small">₹<?php echo number_format($taxEst, 2); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Express Insured Courier:</span>
                                        <span class="font-weight-bold text-success small">FREE</span>
                                    </div>
                                    <div class="dropdown-divider my-2"></div>
                                    <div class="d-flex justify-content-between">
                                        <strong class="text-dark">Total Paid:</strong>
                                        <strong class="text-success" style="font-size: 18px;">₹<?php echo number_format($grandTotal, 2); ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Warranty & Official Guarantee Footnote -->
                    <div class="alert alert-light border p-3 text-center small text-muted mb-4" style="background: #faf8f2; border-color: #e8e5dc !important;">
                        <i class="fa-solid fa-certificate text-warning mr-1"></i>
                        Every instrument included in this order carries MelodyMart's 1-Year Comprehensive Warranty and 100% Authenticity Guarantee.
                    </div>

                </div>

            <?php endif; ?>

        </div> 
    </div> 
</div> 

<style>
@media print {
    body {
        background: #ffffff !important;
        font-size: 12pt;
    }
    .sidebar, .main-header, .footer, .d-print-none, .navbar {
        display: none !important;
    }
    .main-panel {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .content {
        padding: 0 !important;
    }
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
}
</style>

<?php include("footer.php"); ?>
