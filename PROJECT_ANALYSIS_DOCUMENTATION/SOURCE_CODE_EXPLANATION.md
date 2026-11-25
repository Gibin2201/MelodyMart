# SOURCE CODE EXPLANATION: OrchestraOne (MelodyMart)
**Detailed Architectural Analysis of Key Files, Logic, and Implementations**

---

## 1. System Core & Database Layer

### `dboperation.php` (Root Directory)
* **File Location:** `c:\wamp64\www\OrchestraOne\dboperation.php`
* **Purpose:** Acts as the Single Source of Truth for database connectivity across the entire application. It abstracts procedural MySQLi commands behind an Object-Oriented class.
* **Important Class & Methods:**
  * `class dboperation`: The core database connector class.
  * `function __construct()`: Constructor method that executes automatically whenever `new dboperation()` is instantiated. Initializes the connection to MySQL with host (`localhost`), username (`root`), password (`""`), and database name (`db_orchestraone`).
  * `public function executequery($sql)`: Executes the passed SQL query using `mysqli_query($this->con, $sql)` and returns the resulting resource handle or boolean flag.
* **How It Communicates:** Included via `include_once("../dboperation.php")` or `require_once` across all controller actions and view scripts.
* **Database Interactions:** Communicates directly with the MySQL server daemon over port 3306.
* **Business Logic & Concepts:**
  * Uses the conditional guard `if (!class_exists('dboperation'))` to prevent fatal redeclaration errors if included multiple times across nested includes.
  * Encapsulates connection failure handling: `if (!$this->con) die("Connection failed: " . mysqli_connect_error());`.

---

## 2. Authentication & Guest Controller Scripts

### `Guest/loginaction.php`
* **File Location:** `c:\wamp64\www\OrchestraOne\Guest\loginaction.php`
* **Purpose:** Evaluates credentials submitted from `login.php`, determines user privilege levels, sets appropriate session states, and performs role-based redirects.
* **Key Logic & Flow:**
  1. Starts session via `session_start()`.
  2. Sanitizes input: `$username = isset($_POST["username"]) ? trim($_POST["username"]) : "";`.
  3. **Admin Verification:** Executes `SELECT * FROM tbl_adminlogin WHERE username='$username' AND password='$password'`. If exactly one row is found, establishes `$_SESSION["username"] = $username; $_SESSION["Login_id"] = $row["loginid"];` and redirects to `../Admin/index.php`.
  4. **Customer Verification:** If not admin, executes `SELECT * FROM tbl_customer WHERE username='$username' AND password='$password'`. If matched, establishes `$_SESSION["username"] = $username; $_SESSION["customer_id"] = $row["customer_id"];`.
  5. **Dynamic Return Redirect:** Checks if a safe redirect parameter was provided (`$_POST["redirect"]`), forwarding the customer back to the product they were attempting to purchase. Defaults to `../Customer/index.php`.
  6. **Authentication Rejection:** Emits client-side JavaScript alert `echo "<script>alert('Invalid Username or Password'); window.location='login.php';</script>";`.

### `Guest/cusregaction.php`
* **File Location:** `c:\wamp64\www\OrchestraOne\Guest\cusregaction.php`
* **Purpose:** Validates, sanitizes, and persists new customer registration data while ensuring zero credential collisions.
* **Key Logic & Flow:**
  1. Sanitizes inputs using `mysqli_real_escape_string($obj->con, trim($_POST['custname']))` to prevent SQL injection.
  2. **Uniqueness Audit 1 (Username):** Queries `SELECT customer_id FROM tbl_customer WHERE username='$username'`. If records exist, halts with alert `"Username is already taken!"`.
  3. **Uniqueness Audit 2 (Email):** Queries `SELECT customer_id FROM tbl_customer WHERE email='$email'`. If records exist, halts with alert `"Email is already registered!"`.
  4. **Record Persistence:** Executes `INSERT INTO tbl_customer(customer_name, location_id, phone_no, email, username, password, reg_date) VALUES (...)`.
  5. **Notification Dispatch:** Conditionally loads `phpmailer.php` to dispatch onboarding credentials via email before redirecting to `login.php`.

### `Guest/check_username.php`
* **File Location:** `c:\wamp64\www\OrchestraOne\Guest\check_username.php`
* **Purpose:** An asynchronous AJAX micro-endpoint invoked on every `keyup` keystroke in the registration form to provide live username availability feedback.
* **Key Logic:**
  1. Extracts `$username = trim($_POST['username'] ?? '')`.
  2. Executes `SELECT customer_id FROM tbl_customer WHERE username = '$username' LIMIT 1`.
  3. Returns a structured JSON payload: `{"available": true/false, "message": "..."}` with HTTP header `Content-Type: application/json`.
  4. Enables non-blocking, real-time client UI validation without page reloading.

---

## 3. Customer Storefront & Cart Controllers

### `Customer/ptview.php`
* **File Location:** `c:\wamp64\www\OrchestraOne\Customer\ptview.php`
* **Purpose:** The primary instrument showcase and purchase specification interface.
* **Key Logic & Flow:**
  1. Extracts `$productid = (int)$_GET['productid']`.
  2. Joins `tbl_product` with `tbl_subcategory` and `tbl_category` to retrieve category tags, luthier profile details, and stock figures.
  3. **Duplicate Cart Detection:** Queries `SELECT cart_id, quantity FROM tbl_cart WHERE product_id = '$prodId' AND customer_id = '$activeCid' AND status = 'cart' LIMIT 1`.
  4. If the item is already in the cart, conditionally renders:
     * An alert banner: *"Item is already in cart (Qty: X)"*.
     * The cart button changes to a highlighted *"Item is already in cart"* action that alerts the user and guides them to their cart if clicked.
  5. Includes embedded JavaScript that performs real-time stepper quantity bounding (`min = 1`, `max = stock`) and recalculates subtotal display (`quantity * priceVal`).

### `Customer/cartaction.php`
* **File Location:** `c:\wamp64\www\OrchestraOne\Customer\cartaction.php`
* **Purpose:** Backend controller that processes add-to-cart requests, enforcing strict single-instance line item integrity.
* **Key Logic & Flow:**
  1. Checks user authorization (`!isset($_SESSION['customer_id'])`). Redirects unauthenticated requests to login.
  2. Casts inputs: `$pid = (int)$_POST['productid']; $tamount = (float)$_POST['totalamount']; $quantity = (int)$_POST['quantity'];`.
  3. **Server-Side Duplicate Validation:** Executes `SELECT * FROM tbl_cart WHERE product_id = '$pid' AND customer_id = '$customerid' AND status = 'cart'`.
  4. If found, stops execution immediately:
     ```php
     if ($checkRes && mysqli_num_rows($checkRes) > 0) {
         echo "<script>alert('Item is already in cart'); window.location='cartview.php';</script>";
         exit();
     }
     ```
  5. If valid, inserts into `tbl_cart` and redirects cleanly to `cartview.php`.

### `Customer/updatecart.php`
* **File Location:** `c:\wamp64\www\OrchestraOne\Customer\updatecart.php`
* **Purpose:** Handles inline quantity incrementation (`inc`) and decrementation (`dec`) from the cart interface.
* **Key Logic & Flow:**
  1. Accepts `cartid` and `action` via `$_GET`.
  2. Queries `tbl_cart` joined with `tbl_product` to check live stock availability.
  3. If action is `inc` and `curQty < maxStock`, increments `$newQty = $curQty + 1`.
  4. If action is `dec` and `curQty > 1`, decrements `$newQty = $curQty - 1`.
  5. Recomputes line item total: `$newTotal = $newQty * $unitPrice`.
  6. Executes `UPDATE tbl_cart SET quantity = $newQty, totalamount = $newTotal WHERE cart_id = $cartid` and returns to `cartview.php`.

---

## 4. Transaction Settlement & Order Fulfillment

### `Customer/paymentaction.php`
* **File Location:** `c:\wamp64\www\OrchestraOne\Customer\paymentaction.php`
* **Purpose:** The core transactional settlement engine. Orchestrates master order creation, payment logging, delivery destination persistence, cart state migration, and stock decrementing.
* **Sequential Database Steps:**
  * **Step 1 (Order Master):** Inserts grand total into `tbl_cartmaster` (`cmstatus = 'Paid'`); retrieves primary key `$cartId = mysqli_insert_id()`.
  * **Step 2 (Payment Record):** Calculates platform commission (`$commission = $totalamount * 0.10`) and logs payment in `tbl_payment` (`pstatus = 'Paid'`).
  * **Step 3 (Shipping Destination):** Persists recipient name, phone, address, and pincode in `tbl_deliveryaddress` referencing `$cartId`.
  * **Step 4 (Cart Status Migration):** Updates `tbl_cart SET status = 'paid', cartmaster_id = '$cartId' WHERE customer_id = '$customerid' AND status = 'cart'`.
  * **Step 5 (Inventory Synchronization):** Queries all products in the active cart; executes:
    ```sql
    UPDATE tbl_product SET stock = stock - '$quantity' WHERE product_id = '$productId';
    ```
    If remaining stock reaches 0, executes:
    ```sql
    UPDATE tbl_product SET pstatus = 'Unavailable', stock = 0 WHERE product_id = '$productId';
    ```
  * Redirects to `ordersuccess.php?order_id=$cartId`.

### `Admin/orderdetail.php`
* **File Location:** `c:\wamp64\www\OrchestraOne\Admin\orderdetail.php`
* **Purpose:** Generates a comprehensive customer invoice and warehouse dispatch packing slip.
* **Key Logic:**
  1. Retrieves `$orderId = (int)$_GET['order_id']`.
  2. Queries master order information from `tbl_cartmaster` joined with `tbl_payment`, `tbl_customer`, and `tbl_deliveryaddress`.
  3. Queries all purchased instruments from `tbl_cart` joined with `tbl_product`.
  4. Renders dual views: a web-based dashboard review and a clean printable document.
  5. Employs CSS `@media print` rules to hide navigation sidebars, headers, and buttons during physical printing.

---

## 5. Digital Warranty Claim Engine

### `Customer/warrantyaction.php`
* **File Location:** `c:\wamp64\www\OrchestraOne\Customer\warrantyaction.php`
* **Purpose:** Processes customer warranty claims against past paid purchases.
* **Key Logic & Flow:**
  1. Accepts `cartid` and `issue` description via `POST`.
  2. **File Upload Handling (Sanitization & Timestamping):**
     * Checks `$_FILES["wimg"]` (warranty card) and `$_FILES["proimg"]` (defect photograph).
     * Cleans original filenames to prevent path traversal: `time() . "_defect_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", $_FILES["proimg"]["name"])`.
     * Moves files to `../uploads/`.
  3. Inserts into `tbl_warranty` with `wstatus = 'requested'` and `applydate = CURDATE()`.
  4. Alerts customer with confirmation message and routes to `warrantyview.php`.

### `Admin/warrantyaction.php`
* **File Location:** `c:\wamp64\www\OrchestraOne\Admin\warrantyaction.php`
* **Purpose:** Processes administrative warranty claim approval or rejection.
* **Key Logic:**
  1. Accepts `action` (`accept` or `reject`) and `wid` (warranty ID) via `$_GET`.
  2. Updates `tbl_warranty SET wstatus = 'Accepted' (or 'Rejected'), responsedate = CURDATE() WHERE warranty_id = '$wid'`.
  3. Redirects back to `warrantyview.php` with confirmation alert.

---

## 6. Business Intelligence & Reporting

### `Admin/exportexcel.php`
* **File Location:** `c:\wamp64\www\OrchestraOne\Admin\exportexcel.php`
* **Purpose:** Generates and streams downloadable spreadsheet audit reports.
* **Key Logic:**
  1. Extracts date range filters `from` and `to`.
  2. Queries `tbl_cartmaster` joined with `tbl_payment`, `tbl_customer`, and `tbl_deliveryaddress`.
  3. Sends explicit HTTP download headers:
     ```php
     header("Content-Type: application/vnd.ms-excel");
     header("Content-Disposition: attachment; filename=MelodyMart_Sales_Report_" . date('Y-m-d') . ".xls");
     ```
  4. Echoes an HTML table structure containing order references, buyer details, payment dates, and revenue totals, which Microsoft Excel and Google Sheets parse natively into tabular rows.
