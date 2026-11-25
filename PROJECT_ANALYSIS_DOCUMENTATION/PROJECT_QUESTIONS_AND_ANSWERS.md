# PROJECT VIVA QUESTIONS & ANSWERS: OrchestraOne (MelodyMart)
**55+ Comprehensive Academic & Technical Defense Questions with Detailed Answers**

---

## Category 1: Project Overview & Objectives (Q1 – Q10)

### Q1: What is OrchestraOne (MelodyMart)?
**Answer:** OrchestraOne is an end-to-end, web-based e-commerce platform and digital warranty management system specialized for musical instruments and acoustic audio hardware. It enables musicians to browse verified instruments across two taxonomic levels, inspect detailed acoustic profiles, manage an interactive shopping cart with duplicate protection, complete a 3-step checkout, and file post-purchase digital warranty claims with defect photo attachments.

### Q2: What motivated the development of this specialized system instead of using a generic e-commerce portal?
**Answer:** Generic e-commerce systems lack support for detailed acoustic specifications (such as tonewood profiles, pickup hardware, and luthier calibration badges). Furthermore, traditional musical instrument retail relies heavily on physical paper warranty cards and store receipts, which customers frequently misplace. OrchestraOne bridges this gap by integrating a complete digital post-sale warranty claims lifecycle directly linked to verified purchases.

### Q3: Who are the primary user roles in this platform?
**Answer:** The system implements three distinct roles:
1. **Guest:** Unauthenticated visitors who can browse featured instruments and register for an account.
2. **Customer:** Authenticated musicians who view hardware specifications, manage their cart, complete purchases, track order history, and submit warranty claims.
3. **Administrator:** Store managers who manage product inventories, review customer orders, print packing slips, adjudicate warranty claims, and audit monthly sales analytics.

### Q4: What are the main functional modules of the system?
**Answer:** 
1. Database Abstraction Layer (`dboperation.php`).
2. Guest Onboarding & Cascading Geographic Registration.
3. Authentication & Role-Based Access Control.
4. Product Catalog Discovery (Category $\rightarrow$ Subcategory $\rightarrow$ Product).
5. Shopping Cart Engine with Duplicate Item Protection.
6. Multi-Step Checkout & Delivery Destination Management.
7. Payment Settlement & Inventory Stock Synchronization.
8. Order History & Invoice Generation.
9. Digital Warranty Claims Management with Photo Uploads.
10. Administrative Dashboard & Inventory Control.
11. Order Fulfillment & Printable Packing Slips.
12. Financial Reporting & Excel Data Export.

### Q5: What is the architectural model used by the application?
**Answer:** The project implements a classic **Three-Tier Web Architecture**:
* **Presentation Tier:** HTML5, CSS3, JavaScript, jQuery 3.6, Bootstrap, and Chart.js running in the client browser.
* **Application / Logic Tier:** Apache HTTP Server 2.4 executing PHP 8.5 page controllers.
* **Persistence Tier:** MySQL Relational Database (`db_orchestraone`) using the InnoDB storage engine.

### Q6: What happens when an anonymous user attempts to access customer pages directly?
**Answer:** Protected customer pages enforce session validation at the very top of each script:
```php
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../Guest/login.php");
    exit();
}
```
If `customer_id` is not present in `$_SESSION`, the server halts script execution and redirects the client to `Guest/login.php` with an HTTP 302 status code.

### Q7: What is the significance of the "MelodyMart" branding in customer views?
**Answer:** "OrchestraOne" is the master enterprise project name, while "MelodyMart" is the consumer-facing retail storefront identity presented to musicians across headers, footers, invoices, and digital certificates.

### Q8: What makes the product details page (`ptview.php`) unique?
**Answer:** It features specialized acoustic profile sections (tonewood, pickup hardware, luthier setup verification), dynamic quantity steppers that strictly enforce inventory limits, live JavaScript subtotal calculation, and conditional duplicate detection that alerts customers if the instrument is already in their active cart.

### Q9: How does the system handle physical shipping addresses?
**Answer:** During checkout, the customer enters recipient name, phone, address, and a 6-digit postal pincode in `Customer/deliveryaddress.php`. When the order is settled in `paymentaction.php`, this information is permanently stored in `tbl_deliveryaddress` linked to the master order reference (`cartmaster_id`).

### Q10: How does the platform handle invoice printing for warehouse dispatch?
**Answer:** The administrator accesses `Admin/orderdetail.php?order_id=X`, which renders an itemized customer invoice and packing slip. Using CSS `@media print` rules, all navigation bars, sidebars, and action buttons are automatically hidden, formatting the document cleanly for physical paper printing via `window.print()`.

---

## Category 2: Database Architecture & Normalization (Q11 – Q25)

### Q11: What is the name of the database, and what storage engine is used?
**Answer:** The database is named `db_orchestraone`. All 12 tables use the **InnoDB** storage engine to guarantee ACID transaction compliance, row-level locking, and foreign key integrity.

### Q12: Why is InnoDB preferred over MyISAM in an e-commerce platform?
**Answer:** MyISAM only supports table-level locking, meaning if one customer is checking out, the entire table is locked against other users. Furthermore, MyISAM lacks transaction support and crash recovery logs. InnoDB provides **row-level locking** (enabling concurrent user checkouts) and guarantees transaction safety via redo logs.

### Q13: Does the database satisfy First Normal Form (1NF)?
**Answer:** Yes. Every table in `db_orchestraone` possesses a primary key (`*_id`), and all attribute fields hold atomic, indivisible values without repeating columns or delimited lists.

### Q14: Does the database satisfy Second Normal Form (2NF)?
**Answer:** Yes. 2NF requires 1NF compliance and zero partial functional dependencies. Because every table in `db_orchestraone` uses a single-column primary key (such as `product_id`, `cart_id`, `customer_id`), no partial key dependencies can exist.

### Q15: Does the database strictly satisfy Third Normal Form (3NF)?
**Answer:** The core entities satisfy 3NF, but specific fields are intentionally denormalized for performance:
* `tbl_cart.totalamount` is derived (`quantity * product.amount`). Storing it eliminates repeated multi-table multiplication across cart and invoice queries.
* `customer_id` is retained in `tbl_payment` and `tbl_warranty` alongside master keys to allow fast, direct filtering without multi-level outer joins.

### Q16: What is a Virtual Generated Column, and how is it used in this project?
**Answer:** A virtual generated column is a database column whose value is computed dynamically from an expression rather than stored on disk. In `tbl_deliveryaddress`, we added:
```sql
cartmaster_id INT GENERATED ALWAYS AS (cartmasterid) VIRTUAL
```
and in `tbl_product`:
```sql
subcategory_id INT GENERATED ALWAYS AS (subcat) VIRTUAL
```
This enables both legacy column names (`cartmasterid`, `subcat`) and standardized naming conventions to function interchangeably without breaking existing queries.

### Q17: What was the primary key typo in `tbl_payment`, and how was it corrected?
**Answer:** The primary key was originally misspelled as `paymeny_id` (with a 'y'). We executed:
```sql
ALTER TABLE tbl_payment CHANGE paymeny_id payment_id INT NOT NULL AUTO_INCREMENT;
```
and updated the single corresponding reference in `Admin/orderdetail.php` to restore proper naming consistency.

### Q18: Why were currency fields converted from `INT` to `DECIMAL(10,2)`?
**Answer:** When currency is stored as an integer, fractional currency amounts (e.g., ₹9,499.50) are truncated, resulting in accounting discrepancies. `DECIMAL(10,2)` guarantees exact, fixed-point monetary precision up to 8 integer digits and 2 decimal places.

### Q19: Why was `tbl_cart.status` changed from `TEXT` to `VARCHAR(20)`?
**Answer:** In MySQL, `TEXT` fields require external off-page pointer storage and cannot be indexed without explicit prefix lengths. Because cart status only holds `'cart'` or `'paid'`, `VARCHAR(20)` drastically reduces storage overhead and allows direct indexing (`idx_cart_status`).

### Q20: What indexes were added to improve database performance?
**Answer:** High-performance B-Tree indexes were added on all 13 relational foreign key columns across `tbl_location`, `tbl_customer`, `tbl_product`, `tbl_cart`, `tbl_cartmaster`, `tbl_deliveryaddress`, `tbl_payment`, and `tbl_warranty`. This converts expensive full-table scans into indexed index lookups during SQL joins.

### Q21: What is the relationship between `tbl_cart` and `tbl_cartmaster`?
**Answer:** It is a **1-to-Many** relationship. While an item is in an active cart, `cartmaster_id` is `NULL`. Once the customer completes checkout, all items in that cart are linked to the newly generated `cartmaster_id` and their status transitions from `'cart'` to `'paid'`.

### Q22: What is the relationship between `tbl_category` and `tbl_subcategory`?
**Answer:** A **1-to-Many** relationship. One parent category (e.g., *Stringed Instruments*) contains multiple subcategories (e.g., *Acoustic Guitars*, *Classical Guitars*, *Electric Basses*).

### Q23: How is data integrity maintained if a category is deleted?
**Answer:** In `tbl_subcategory`, the foreign key constraint includes `ON DELETE CASCADE`:
```sql
CONSTRAINT fk_subcat_category FOREIGN KEY (category_id) REFERENCES tbl_category(category_id) ON DELETE CASCADE
```
Deleting a category automatically deletes all associated subcategories.

### Q24: What information is stored in `tbl_deliveryaddress`?
**Answer:** It stores the recipient person's name (`personname`), 10-digit mobile number (`mobno`), physical street address (`address`), postal pincode (`pincode`), and the linked order ID (`cartmasterid`).

### Q25: How does `tbl_warranty` reference purchased products?
**Answer:** It references the specific line item in `tbl_cart` through foreign key `cart_id`. This confirms that the warranty claim is linked directly to a verified purchase.

---

## Category 3: Backend Logic & Controllers (Q26 – Q40)

### Q26: Explain the structure and purpose of `dboperation.php`.
**Answer:** `dboperation.php` is an Object-Oriented database wrapper class. It initializes a persistent MySQLi connection inside its `__construct()` method and provides a public `executequery($sql)` method that receives raw queries, executes them using `mysqli_query()`, and returns the result resource handle.

### Q27: How does `loginaction.php` handle dual-role authentication?
**Answer:** It inspects submitted credentials in a strict two-tier hierarchy:
1. First, it queries `tbl_adminlogin`. If a match is found, it establishes `$_SESSION['Login_id']` and redirects to `../Admin/index.php`.
2. Second, if no admin match is found, it queries `tbl_customer`. If matched, it establishes `$_SESSION['customer_id']` and redirects to `../Customer/index.php`.
3. If neither matches, it alerts the user and redirects back to `login.php`.

### Q28: How does the shopping cart prevent duplicate items from being added?
**Answer:** In `Customer/cartaction.php`, before running any `INSERT`, the script executes:
```sql
SELECT * FROM tbl_cart WHERE product_id = '$pid' AND customer_id = '$customerid' AND status = 'cart';
```
If `mysqli_num_rows() > 0`, the script halts execution, displays a JavaScript alert `"Item is already in cart"`, and redirects to `cartview.php`.

### Q29: What happens on the product details page (`ptview.php`) if an item is already in the cart?
**Answer:** The script queries `tbl_cart` for the logged-in customer. If found:
1. An alert banner appears: *"Item is already in cart (Qty: X)"*.
2. The quantity stepper is locked to the cart quantity.
3. The **Add to Cart** button is replaced with an *"Item is already in cart"* button that directs the user to their cart.

### Q30: Explain the step-by-step transaction pipeline in `Customer/paymentaction.php`.
**Answer:**
1. **Insert Master Order:** Creates a record in `tbl_cartmaster` (`cmstatus = 'Paid'`) and captures `$cartId = mysqli_insert_id()`.
2. **Insert Payment:** Logs the settled total in `tbl_payment` (`pstatus = 'Paid'`).
3. **Insert Shipping Destination:** Saves recipient details in `tbl_deliveryaddress` referencing `$cartId`.
4. **Update Cart Status:** Sets `status = 'paid'` and `cartmaster_id = '$cartId'` in `tbl_cart` for active items.
5. **Inventory Decrement:** Deducts purchased quantities from `tbl_product.stock`. If stock reaches 0, sets `pstatus = 'Unavailable'`.
6. **Redirect:** Navigates to `Customer/ordersuccess.php?order_id=$cartId`.

### Q31: How is physical inventory updated when an order is completed?
**Answer:** In `paymentaction.php`, the server queries all items in the customer's active cart. For each item, it executes:
```sql
UPDATE tbl_product SET stock = stock - '$quantity' WHERE product_id = '$productId';
```
If the new stock count equals 0, it executes:
```sql
UPDATE tbl_product SET pstatus = 'Unavailable', stock = 0 WHERE product_id = '$productId';
```

### Q32: How does `Customer/updatecart.php` prevent a user from ordering more than available stock?
**Answer:** When incrementing quantity (`action = inc`), the script queries `tbl_product.stock`. It executes `$newQty = $curQty + 1` only if `$curQty < $maxStock`. If quantity already equals max stock, the value remains unchanged.

### Q33: How does file upload sanitization work in `Customer/warrantyaction.php`?
**Answer:** It uses regular expressions to strip special characters from the uploaded filename:
```php
$proimg = time() . "_defect_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", $_FILES["proimg"]["name"]);
move_uploaded_file($_FILES["proimg"]["tmp_name"], "../uploads/" . $proimg);
```
Prepending `time()` ensures each filename is unique and prevents directory traversal or file overwrite attacks.

### Q34: What administrative actions are executed in `Admin/warrantyaction.php`?
**Answer:** It receives the claim ID (`wid`) and decision flag (`action = accept` or `reject`). It updates `tbl_warranty`:
```sql
UPDATE tbl_warranty SET wstatus = 'Accepted' (or 'Rejected'), responsedate = CURDATE() WHERE warranty_id = '$wid';
```

### Q35: How does `Admin/exportexcel.php` generate downloadable Excel spreadsheets?
**Answer:** It queries paid transactions within a date range and sets HTTP download headers:
```php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=MelodyMart_Sales_Report_" . date('Y-m-d') . ".xls");
```
It then prints an HTML table structure that spreadsheet software parses natively into columns and rows.

### Q36: How does `Admin/productview.php` allow quick stock updates without full page reloads?
**Answer:** It includes a Bootstrap modal (`#quickStockModal`) with JavaScript functions that populate product ID, name, stock count, and availability status. The administrator modifies values in the modal and submits them directly to `producteditaction.php`.

### Q37: How does `logout.php` terminate user sessions?
**Answer:** It invokes `session_start()`, calls `session_destroy()` to erase all server-side session variables, and issues `header("location:Guest/index.php")` to redirect the user to the public storefront.

### Q38: How does `Guest/check_username.php` communicate asynchronously with the client?
**Answer:** It listens for POST requests containing a username string, queries `tbl_customer`, sets `Content-Type: application/json`, and returns `{"available": true}` or `{"available": false}`.

### Q39: What is the role of `Guest/getloc_drop.php`?
**Answer:** It receives a `district_id` via AJAX POST, executes `SELECT * FROM tbl_location WHERE district_id = '$did'`, and echoes `<option>` HTML tags that populate the child Location dropdown in real time.

### Q40: How does `Admin/index.php` calculate monthly revenue for charts?
**Answer:** It executes an aggregate query on `tbl_payment`:
```sql
SELECT MONTH(payment_date) AS month_no, DATE_FORMAT(payment_date, '%M') AS month_name, SUM(commission) AS total_payment
FROM tbl_payment WHERE pstatus = 'Paid' GROUP BY month_no, month_name ORDER BY month_no;
```
It then passes the result arrays to Chart.js.

---

## Category 4: Frontend, AJAX & UI Design (Q41 – Q50)

### Q41: Which CSS and UI frameworks are used in OrchestraOne?
**Answer:** Bootstrap 4 and 5 are used for grid layouts, modals, and responsive navigation. FontAwesome 6 provides iconography, and Google Fonts supplies **Playfair Display** (for luxury headers) and **DM Sans** (for hardware specs and forms).

### Q42: How does the dependent District-to-Location dropdown work on the client side?
**Answer:** A jQuery event listener binds to the change event of the District dropdown:
```javascript
$("#districtid").change(function() {
    var did = $(this).val();
    $.ajax({
        url: "getloc_drop.php",
        method: "POST",
        data: { id: did },
        success: function(response) {
            $("#locationid").html(response);
        }
    });
});
```

### Q43: How does the interactive quantity stepper work on `Customer/ptview.php`?
**Answer:** Client-side JavaScript binds click events to `#btnPlus` and `#btnMinus`. When clicked, it updates `#quantity.value` within `[1, maxStock]` and dynamically updates `#totalDisplay` by multiplying quantity by the unit price.

### Q44: What is the purpose of the 3-step checkout stepper in `cartview.php`?
**Answer:** It provides visual feedback across the checkout progression: **1. Shopping Cart** $\rightarrow$ **2. Delivery Address** $\rightarrow$ **3. Payment & Warranty**, improving checkout completion rates.

### Q45: How does Chart.js render the monthly sales chart on the Admin Dashboard?
**Answer:** A `<canvas id="salesChart">` element is initialized in JavaScript. PHP injects month names and revenue arrays into the `labels` and `data` properties of a `bar` chart configuration object.

### Q46: How does the password visibility toggle work on `Guest/login.php`?
**Answer:** A JavaScript click listener on `#togglePassword` checks the `type` attribute of the password input. If `type === "password"`, it toggles to `"text"` and switches the FontAwesome icon from an open eye to a slashed eye.

### Q47: How does the print stylesheet work in `Admin/orderdetail.php`?
**Answer:** It utilizes CSS `@media print` rules:
```css
@media print {
    .sidebar, .navbar, .btn, .d-print-none {
        display: none !important;
    }
}
```
This strips away non-essential UI controls when physical printing is invoked.

### Q48: How is image fallback handled if an instrument image fails to load?
**Answer:** Image tags include inline `onerror` fallback handlers:
```html
<img src="../uploads/<?= $imgName ?>" onerror="this.src='../Guest/ins.jpg'">
```
If an image is missing, the browser gracefully displays a placeholder instrument graphic.

### Q49: What is the purpose of `jquery-3.6.0.min.js` stored in the root folder?
**Answer:** It provides a local, self-contained copy of the jQuery library, allowing AJAX calls, dropdown cascades, and DOM manipulation to function even when testing offline without an active internet connection.

### Q50: How does the customer order history page (`vieworders.php`) organize past purchases?
**Answer:** It displays paid items inside styled product cards with brand pills, product codes, unit prices, quantities, and direct links to claim digital warranty coverage.

---

## Category 5: Security, Testing & System Validation (Q51 – Q60)

### Q51: How does the system defend against SQL Injection?
**Answer:** 
1. Input strings are sanitized using `mysqli_real_escape_string($obj->con, trim($_POST[...]))` to escape special characters.
2. Identifiers and numeric inputs are cast to strict integers (`(int)$_GET['productid']`).
3. For future production releases, transitioning to parameterized prepared statements (`$stmt->bind_param()`) is recommended.

### Q52: What is the current password storage mechanism, and how should it be improved for production?
**Answer:** Passwords are currently stored as plaintext strings for academic demonstration. In production, passwords should be hashed using PHP's native `password_hash($password, PASSWORD_BCRYPT)` and verified using `password_verify()`.

### Q53: How does the system prevent Cross-Site Scripting (XSS)?
**Answer:** Output strings rendered into HTML are wrapped in `htmlspecialchars()`, which converts special characters (like `<`, `>`, `&`, `"`) into HTML entities, preventing script injection.

### Q54: What happens if a customer attempts to purchase an item that went out of stock while in their cart?
**Answer:** When settling payment in `paymentaction.php`, the system queries current stock levels. If stock is insufficient, the transaction is rejected, and stock status automatically updates to `'Unavailable'`.

### Q55: How are file upload vulnerabilities mitigated in warranty claims?
**Answer:** 
1. Non-alphanumeric characters are stripped from filenames via regular expressions.
2. Filenames are prepended with current Unix timestamps (`time()`).
3. Files are stored in a designated `uploads/` directory outside executable application paths.

### Q56: What is Cross-Site Request Forgery (CSRF), and how can OrchestraOne be protected against it?
**Answer:** CSRF is an attack where an unauthorized command is transmitted from a user that the web application trusts. It can be prevented by generating a cryptographically secure token in `$_SESSION['csrf_token']` and validating it as a hidden field on all POST requests.

### Q57: How was the system validated during testing?
**Answer:** A 25-point test matrix was executed covering:
* Unit syntax verification via `php -l`.
* Functional validation of registration, cascading dropdowns, and username checks.
* Boundary testing on stock bounds and duplicate cart prevention.
* End-to-end integration testing of the entire purchase, fulfillment, and warranty lifecycle.

### Q58: What default test credentials are configured for demonstration?
**Answer:**
* **Administrator:** Username `admin` / Password `admin` (accesses `Admin/index.php`).
* **Customer:** Username `customer` / Password `customer` (accesses `Customer/index.php`).

### Q59: What are the main limitations of the current implementation?
**Answer:**
1. Simulated payment gateway rather than live banking webhooks.
2. Plaintext password storage requiring migration to Bcrypt.
3. Local email dispatch rather than commercial SMS or real-time courier tracking APIs.

### Q60: How can OrchestraOne be scaled in the future?
**Answer:**
1. Migrating to a modern PHP framework (Laravel or Symfony) using MVC and Eloquent ORM.
2. Integrating live payment APIs (Stripe, Razorpay) and courier tracking webhooks.
3. Adding interactive audio samples allowing musicians to preview instrument sounds prior to purchase.
4. Implementing a Luthier Video Consultation booking module for custom acoustic setups.
