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

// Prefill existing customer data
$custRes = $obj->executequery("SELECT * FROM tbl_customer WHERE customer_id = '$customerid'");
$customerData = ($custRes) ? mysqli_fetch_assoc($custRes) : null;
$defaultName  = $customerData ? htmlspecialchars($customerData['customer_name']) : '';
$defaultPhone = $customerData ? htmlspecialchars($customerData['phone_no']) : '';

// Prefill latest address
$addrRes = $obj->executequery("SELECT * FROM tbl_deliveryaddress WHERE cartmasterid IN (SELECT cartmaster_id FROM tbl_cartmaster WHERE customer_id = '$customerid') ORDER BY deliveryaddressid DESC LIMIT 1");
$prevAddr = ($addrRes) ? mysqli_fetch_assoc($addrRes) : null;
$defaultAddress = $prevAddr ? htmlspecialchars($prevAddr['address']) : '';
$defaultPincode = $prevAddr ? htmlspecialchars($prevAddr['pincode']) : '';
?>

<div class="address-page-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="checkout-card">
                    <div class="checkout-card-header d-flex align-items-center">
                        <div class="header-icon-wrap mr-3">
                            <i class="fa-solid fa-house-chimney-user"></i>
                        </div>
                        <div>
                            <h4 class="checkout-card-title mb-0">Delivery Address</h4>
                            <p class="checkout-card-subtitle mb-0">Manage and verify your shipping destination</p>
                        </div>
                    </div>

                    <div class="checkout-card-body">
                        <form action="payment.php" method="GET">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold" for="fullname">Full Name <span class="text-danger">*</span></label>
                                    <div class="input-icon-wrap">
                                        <i class="fa-solid fa-user field-icon"></i>
                                        <input type="text" id="fullname" name="fullname" class="form-control form-control-mm" placeholder="Recipient Name" value="<?= $defaultName ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold" for="phone">Phone Number <span class="text-danger">*</span></label>
                                    <div class="input-icon-wrap">
                                        <i class="fa-solid fa-phone field-icon"></i>
                                        <input type="tel" id="phone" name="phone" class="form-control form-control-mm" placeholder="Mobile Number" value="<?= $defaultPhone ?>" required pattern="^\+?[0-9\s\-]{10,15}$">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label font-weight-bold" for="address">Street Address <span class="text-danger">*</span></label>
                                <textarea id="address" name="address" rows="3" class="form-control form-control-mm" placeholder="Building, Street, Landmark" required><?= $defaultAddress ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold" for="pincode">Postal PIN Code <span class="text-danger">*</span></label>
                                    <div class="input-icon-wrap">
                                        <i class="fa-solid fa-location-dot field-icon"></i>
                                        <input type="text" id="pincode" name="pincode" class="form-control form-control-mm" placeholder="PIN Code" value="<?= $defaultPincode ?>" required pattern="[0-9]{5,8}">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top" style="gap: 10px;">
                                <a href="cartview.php" class="btn btn-outline-dark">
                                    <i class="fa-solid fa-arrow-left mr-1"></i> Back to Cart
                                </a>
                                <a href="payment.php" class="btn btn-warning font-weight-bold text-dark px-4" style="background: #d7ad69; border-color: #d7ad69; border-radius: 8px;">
                                    Continue to Payment <i class="fa-solid fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.address-page-wrapper {
    background: #f7f5ef;
    min-height: 80vh;
    padding: 120px 0 60px;
    font-family: 'DM Sans', -apple-system, sans-serif;
    color: #121411;
}
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
}
textarea.form-control-mm {
    padding-left: 14px;
}
</style>

<?php include("footer.php"); ?>
