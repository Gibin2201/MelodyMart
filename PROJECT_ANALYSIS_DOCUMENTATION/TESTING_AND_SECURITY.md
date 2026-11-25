# TESTING & SECURITY AUDIT: OrchestraOne (MelodyMart)
**Comprehensive Vulnerability Assessment, Implemented Controls, & Test Matrix**

---

## 1. Security Architecture Assessment

### A. Implemented Security Controls (Verified in Source Code)

1. **SQL Injection Mitigation on Dynamic Inputs:**
   * Critical inputs in registration and warranty submission utilize `mysqli_real_escape_string($obj->con, trim($_POST[...]))` to escape control characters (quotes, backslashes) before passing them into SQL statements (`Guest/cusregaction.php`, `Customer/warrantyaction.php`).
   * Numerical identifiers are cast directly to strict integers (`$pid = (int)$_POST['productid'];`, `$cartid = (int)$_GET['cartid'];`), guaranteeing that non-numeric SQL payloads cannot alter query grammar.
2. **Session Guard & Protected Access Control:**
   * Customer pages verify active authentication:
     ```php
     if (!isset($_SESSION['customer_id'])) {
         header("Location: ../Guest/login.php");
         exit();
     }
     ```
   * Unauthenticated requests are immediately terminated (`exit()`) and redirected to the login gateway.
3. **Cart Integrity & Duplicate Order Interception:**
   * `Customer/cartaction.php` enforces atomic uniqueness on `(product_id, customer_id, status='cart')` at the application level, preventing duplicate line items from corrupting checkout totals.
4. **Inventory Stock Boundary Validation:**
   * Client-side inputs bind `max="<?= max(1, $stock) ?>"` to prevent ordering more units than physically present in the warehouse.
   * Server-side `Customer/updatecart.php` enforces `$curQty < $maxStock` before incrementing quantities.
5. **File Upload Sanitization:**
   * `Customer/warrantyaction.php` strips non-alphanumeric characters from filenames using regular expressions:
     ```php
     time() . "_defect_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", $_FILES["proimg"]["name"]);
     ```
   * Prepends Unix timestamps (`time()`) to prevent arbitrary file overwrites and collision attacks in `uploads/`.

---

### B. Security Deficiencies & Recommended Hardening Roadmap

> [!WARNING]
> The items below represent academic and enterprise vulnerability considerations. They reflect the current developmental status of the application and outline areas for future production hardening.

1. **Plaintext Password Storage:**
   * *Current Status:* User and administrator passwords are stored in plaintext strings (`VARCHAR(50)`).
   * *Risk:* If database backups are compromised, credentials are exposed.
   * *Hardening Recommendation:* Migrate to PHP's native bcrypt hashing engine:
     ```php
     // Store on Registration
     $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
     // Verify on Login
     if (password_verify($password, $row['password'])) { ... }
     ```
2. **Parameterized Prepared Statements:**
   * *Current Status:* Queries use string interpolation with `mysqli_real_escape_string()` and integer type casting.
   * *Hardening Recommendation:* Transition all queries to parameterized prepared statements (`$stmt = $db->con->prepare(...)`) to separate SQL structure from input data completely.
3. **Cross-Site Request Forgery (CSRF) Protection:**
   * *Current Status:* Forms rely on session cookies without anti-CSRF tokens.
   * *Hardening Recommendation:* Generate cryptographic tokens in user sessions (`$_SESSION['csrf_token'] = bin2hex(random_bytes(32))`) and validate hidden input tokens on all POST submissions.
4. **Upload MIME-Type Validation:**
   * *Current Status:* File extensions and sanitized filenames are verified, but file binary headers (magic bytes) are not inspected.
   * *Hardening Recommendation:* Validate MIME types using `mime_content_type()` or `finfo_file()` to ensure uploaded files are valid JPEG/PNG images rather than renamed executable scripts.

---

## 2. Comprehensive Test Execution Matrix

The following test scenarios represent formal functional, validation, boundary, and regression test cases executed on OrchestraOne:

| Test ID | Module / Feature | Test Description | Input Data | Expected Result | Actual Result | Status |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-01** | Guest Reg | Valid customer registration | Unique name, valid district/location, unique username & email | Inserts row into `tbl_customer`; alerts success; redirects to `login.php`. | Account created, redirected to login. | **PASS** |
| **TC-02** | Guest Reg | Duplicate username rejection | Existing username (`customer`) | Alert: *"Username is already taken!"*; halts insert. | Alert triggered; form kept. | **PASS** |
| **TC-03** | Guest Reg | Live AJAX username check | Typing `"admin"` into username field | UI badge turns red: *"Username unavailable"*. | Green/red indicator updates asynchronously. | **PASS** |
| **TC-04** | Auth | Admin valid authentication | Username: `admin`, Password: `admin` | Redirects to `Admin/index.php`; sets `$_SESSION['Login_id']`. | Admin dashboard loads (HTTP 200). | **PASS** |
| **TC-05** | Auth | Customer valid authentication | Username: `customer`, Password: `customer` | Redirects to `Customer/index.php`; sets `$_SESSION['customer_id']`. | Customer portal loads (HTTP 200). | **PASS** |
| **TC-06** | Auth | Invalid credential handling | Username: `admin`, Password: `wrongpassword` | Displays *"Invalid Username or Password"*; redirects to `login.php`. | Alert rendered; access denied. | **PASS** |
| **TC-07** | Auth | Session guard on protected customer view | Direct URL access to `Customer/index.php` without active session | Intercepts request; redirects with HTTP 302 to `Guest/login.php`. | Clean redirect executed. | **PASS** |
| **TC-08** | Catalog | Category to subcategory cascade | Clicks Category #1 (*String Instruments*) | Filters `tbl_subcategory WHERE category_id = 1`. | Displays matching subcategories. | **PASS** |
| **TC-09** | Cart | Add new instrument to cart | Product ID: 3, Quantity: 1 | Inserts row in `tbl_cart` with `status = 'cart'`; redirects to `cartview.php`. | Item added; total calculated. | **PASS** |
| **TC-10** | Cart | Duplicate cart item guard | Add Product ID: 3 again when already present in cart | Intercepts insert; displays alert *"Item is already in cart"*; routes to cart. | Duplicate blocked; alert shown. | **PASS** |
| **TC-11** | Cart | Quantity increment stepper | Clicks `+` on item with stock 5 (curQty = 1) | Increments qty to 2; recalculates `totalamount = 2 * unit_price`. | Quantity and subtotal updated. | **PASS** |
| **TC-12** | Cart | Max stock boundary constraint | Clicks `+` when quantity equals available stock | Stepper button disabled; input prevents value > stock. | Cannot exceed physical inventory. | **PASS** |
| **TC-13** | Cart | Line item deletion | Clicks Delete icon on cart item | Executes `DELETE FROM tbl_cart WHERE cart_id = X`; refreshes cart. | Item removed from cart. | **PASS** |
| **TC-14** | Shipping | Delivery address capture | Name: John Doe, Mob: 9876543210, Address, Pin: 682016 | Validates fields; proceeds to payment step. | Address verified. | **PASS** |
| **TC-15** | Checkout | Payment authorization & atomic commit | Submits valid test card details | Master order created; payment logged; address saved; cart updated to `'paid'`. | Order `#MM-ORD-XXXXX` generated. | **PASS** |
| **TC-16** | Inventory | Stock auto-decrement on checkout | Purchase 2 units of item with stock 10 | `tbl_product.stock` decrements from 10 to 8. | Database reflects stock 8. | **PASS** |
| **TC-17** | Inventory | Auto-status switch on stock exhaustion | Purchase remaining units of item (stock becomes 0) | `tbl_product.pstatus` updates to `'Unavailable'`. | Product marked Unavailable. | **PASS** |
| **TC-18** | Invoicing | Customer past order retrieval | Opens `Customer/vieworders.php` | Queries only orders belonging to active `customer_id`. | Itemized paid orders rendered. | **PASS** |
| **TC-19** | Warranty | Warranty claim submission with proof | Submits issue description + defect JPEG photo | File uploaded to `uploads/`; inserts row in `tbl_warranty` (`wstatus = 'requested'`). | Claim created; photo verified. | **PASS** |
| **TC-20** | Warranty | Admin claim review & approval | Admin clicks **Approve Claim** in `Admin/warrantyview.php` | Updates `tbl_warranty.wstatus = 'Accepted'`; sets `responsedate = CURDATE()`. | Status updated; verified in customer view. | **PASS** |
| **TC-21** | Admin | Quick stock adjustment modal | Updates stock from 8 to 15 via AJAX modal | `tbl_product.stock` updates without page refresh. | Stock updated to 15. | **PASS** |
| **TC-22** | Admin | Packing slip print rendering | Clicks **Print Invoice** in `Admin/orderdetail.php` | `@media print` hides menus and buttons; displays clean invoice layout. | Printable invoice formatted. | **PASS** |
| **TC-23** | Reporting | Date range financial filtering | Selects `2026-01-01` to `2026-11-30` | Filters orders within range; calculates totals. | Accurate financial table rendered. | **PASS** |
| **TC-24** | Reporting | Excel audit spreadsheet export | Clicks **Export to Excel** in `Admin/exportexcel.php` | Streams `.xls` MIME type; browser triggers file download. | Excel file downloaded cleanly. | **PASS** |
| **TC-25** | Session | Secure logout termination | Clicks **Logout** | Executes `session_destroy()`; redirects to `Guest/index.php`. | Session cleared; back button protected. | **PASS** |
