<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("header.php");
include_once("../dboperation.php");
$obj = new dboperation();

$customerid = (int)$_SESSION["customer_id"];

// Admin & Customer catalog: tbl_product foreign key `subcat` references `tbl_subcategory.subcategory_id`
$s = "SELECT p.*, cm.*, c.*, pro.*, sub.name AS subcat_name, cus.customer_name 
      FROM tbl_payment p 
      INNER JOIN tbl_cartmaster cm ON cm.cartmaster_id = p.cartmaster_id 
      INNER JOIN tbl_cart c ON c.cartmaster_id = cm.cartmaster_id 
      INNER JOIN tbl_product pro ON pro.product_id = c.product_id 
      LEFT JOIN tbl_subcategory sub ON sub.subcategory_id = pro.subcat 
      INNER JOIN tbl_customer cus ON cus.customer_id = p.customer_id 
      WHERE p.customer_id = '$customerid' AND c.status = 'paid' 
      ORDER BY c.cart_id DESC";
$res = $obj->executequery($s);
$orderCount = ($res) ? mysqli_num_rows($res) : 0;
?>

<div class="py-5" style="background: #f7f5ef; min-height: 75vh;">
  <div class="container">
    
    <!-- Page Header Banner -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap pb-3 border-bottom" style="border-color: #e8e5dc !important;">
      <div>
        <span class="badge badge-warning px-3 py-1 mb-1 text-dark font-weight-bold" style="background: #d7ad69; letter-spacing: 0.5px;">
          <i class="fa-solid fa-box-archive mr-1"></i> ORDER HISTORY
        </span>
        <h2 style="font-family: 'Playfair Display', Georgia, serif; font-weight: 700; color: #121411; margin-top: 4px;">
          My Instrument Purchases
        </h2>
        <p class="text-muted small mb-0">Track verified payment receipts, download digital warranty cards, and file warranty claims.</p>
      </div>
      <div class="mt-2 mt-md-0">
        <a href="catview.php" class="btn btn-dark font-weight-bold px-3 py-2" style="border-radius: 8px;">
          <i class="fa-solid fa-store mr-1"></i> Shop More Instruments
        </a>
      </div>
    </div>

    <!-- Orders Card Container -->
    <div class="card shadow-sm border-0" style="border-radius: 16px; overflow: hidden; border: 1px solid #e8e5dc !important;">
      <div class="card-header py-3 px-4 d-flex justify-content-between align-items-center" style="background: #121411; color: #ffffff;">
        <h5 class="mb-0 font-weight-bold" style="font-family: 'Playfair Display', Georgia, serif; font-size: 18px;">
          <i class="fa-solid fa-bag-shopping mr-2 text-warning"></i> Completed Orders (<?php echo $orderCount; ?>)
        </h5>
        <span class="badge badge-warning text-dark font-weight-bold" style="background: #d7ad69;">
          Verified Payments
        </span>
      </div>

      <div class="card-body p-0 bg-white">
        <?php if ($orderCount > 0): ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead style="background: #faf8f2; color: #272a25; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                <tr>
                  <th class="py-3 px-4">Purchase Date</th>
                  <th class="py-3">Instrument</th>
                  <th class="py-3 text-center">Amount</th>
                  <th class="py-3 text-center">Qty</th>
                  <th class="py-3 text-center">Warranty Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($r = mysqli_fetch_array($res)): ?>  
                  <tr class="order-row border-bottom">
                    <!-- Date -->
                    <td class="px-4 py-3 align-middle" style="white-space: nowrap;">
                      <span class="badge badge-light border px-2 py-1 text-muted" style="font-size: 12px;">
                        <i class="fa-regular fa-calendar-check mr-1 text-success"></i>
                        <?php echo htmlspecialchars($r["cart_date"]); ?>
                      </span>
                      <div class="small text-muted mt-1">
                        <i class="fa-solid fa-certificate mr-1 text-warning" style="font-size: 10px;"></i>
                        MelodyMart Official
                      </div>
                    </td>

                    <!-- Product Image & Name -->
                    <td class="py-3 align-middle">
                      <div class="d-flex align-items-center">
                        <img src="../uploads/<?php echo htmlspecialchars($r['image']); ?>" 
                             alt="<?php echo htmlspecialchars($r['product_name']); ?>" 
                             class="rounded border mr-3" 
                             style="width: 65px; height: 65px; object-fit: contain; background: #fafafa; padding: 4px;"
                             onerror="this.src='../Guest/ins.jpg'">
                        <div>
                          <h6 class="font-weight-bold mb-1 text-dark" style="font-size: 15px;">
                            <?php echo htmlspecialchars($r["product_name"]); ?>
                          </h6>
                          <?php if (!empty($r["subcat_name"])): ?>
                            <span class="badge badge-warning text-dark font-weight-bold" style="background: rgba(215, 173, 105, 0.25); font-size: 11px;">
                              <?php echo htmlspecialchars($r["subcat_name"]); ?>
                            </span>
                          <?php endif; ?>
                        </div>
                      </div>
                    </td>

                    <!-- Total Amount -->
                    <td class="py-3 text-center align-middle">
                      <span class="font-weight-bold text-success" style="font-size: 16px;">
                        ₹<?php echo number_format((float)$r["totalamount"], 2); ?>
                      </span>
                    </td>

                    <!-- Quantity -->
                    <td class="py-3 text-center align-middle">
                      <span class="badge badge-dark px-2 py-1" style="background: #121411;">
                        <?php echo (int)$r["quantity"]; ?> item(s)
                      </span>
                    </td>

                    <!-- Actions: Warranty Application & Card -->
                    <td class="py-3 text-center align-middle" style="white-space: nowrap;">
                      <a href="warrantycard.php?cartid=<?php echo (int)$r['cart_id']; ?>" 
                         class="btn btn-sm btn-outline-dark font-weight-bold mr-1" 
                         style="border-radius: 6px;">
                        <i class="fa-solid fa-certificate text-warning mr-1"></i> Warranty Card
                      </a>
                      <a href="warranty.php?cartid=<?php echo (int)$r['cart_id']; ?>" 
                         class="btn btn-sm text-dark font-weight-bold" 
                         style="background: #d7ad69; border-radius: 6px;">
                        <i class="fa-solid fa-shield-halved mr-1"></i> Claim Warranty
                      </a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="text-center py-5 px-3">
            <div class="mb-3" style="width: 70px; height: 70px; margin: 0 auto; border-radius: 50%; background: rgba(215, 173, 105, 0.15); display: grid; place-items: center; color: #d7ad69; font-size: 28px;">
              <i class="fa-solid fa-box-open"></i>
            </div>
            <h4 class="font-weight-bold text-dark mb-2" style="font-family: 'Playfair Display', Georgia, serif;">No Orders Found Yet</h4>
            <p class="text-muted small mx-auto mb-4" style="max-width: 420px;">
              You have not completed any instrument purchases yet. Browse through our premium categories and find your sound!
            </p>
            <a href="catview.php" class="btn text-dark font-weight-bold px-4 py-2" style="background: #d7ad69; border-radius: 8px;">
              <i class="fa-solid fa-layer-group mr-1"></i> Browse Categories
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

<style>
.order-row:hover {
  background-color: #fdfcf9 !important;
  transition: background-color 0.2s ease;
}
</style>

<?php
include("footer.php");
?>