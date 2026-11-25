# PROJECT TECHNICAL GLOSSARY: OrchestraOne (MelodyMart)
**A Comprehensive Reference Guide to Key Technical and Domain Concepts**

---

## 1. Domain & E-Commerce Concepts

* **Catalog Taxonomy:** The hierarchical classification structure used to organize products (in OrchestraOne: Category $\rightarrow$ Subcategory $\rightarrow$ Product).
* **Line Item:** An individual product entry within a shopping cart or invoice, containing product ID, quantity, unit price, and computed total amount.
* **Shopping Cart:** A temporary holding state that preserves items selected by a customer prior to order commitment.
* **Master Order (`tbl_cartmaster`):** The consolidated parent record representing a complete customer purchase, aggregating multiple line items under a single reference ID.
* **Stock Decrement:** The automated business logic that deducts purchased quantities from physical inventory counts upon payment settlement.
* **Duplicate Cart Protection:** An application-level constraint preventing duplicate records of the same product from being inserted into an active cart, ensuring deterministic order quantities.
* **Digital Warranty Claim:** A formal post-sale request filed by a verified buyer seeking repair, exchange, or luthier support for a defective instrument, supported by photographic proof.
* **Luthier:** A specialized craftsman who builds and repairs stringed instruments (e.g., guitars, violins). In OrchestraOne, luthier inspection badges reflect quality assurance.
* **Packing Slip:** A shipping document printed by warehouse staff detailing buyer address, product codes, descriptions, and quantities for physical package assembly.
* **Commission:** In commercial platforms, a calculated percentage (e.g., 10%) representing administrative platform fees or earnings per transaction.

---

## 2. Database & Persistence Concepts

* **RDBMS (Relational Database Management System):** A database program based on the relational model that stores data in structured tables linked by keys (e.g., MySQL).
* **ACID Properties:** The four fundamental guarantees of reliable database transactions:
  * **Atomicity:** All steps of a transaction succeed, or all fail together (no partial commits).
  * **Consistency:** The database transitions from one valid state to another without violating constraints.
  * **Isolation:** Concurrent transactions execute without interfering with one another.
  * **Durability:** Committed data is permanently recorded on disk and survives server crashes.
* **InnoDB:** The default high-performance MySQL storage engine used in OrchestraOne, providing ACID compliance, row-level locking, and foreign key referential integrity.
* **Primary Key (PK):** A unique, non-null attribute identifying each record in a database table (e.g., `product_id`, `customer_id`).
* **Foreign Key (FK):** A column that creates a relationship pointing to the primary key of another table (e.g., `tbl_product.subcat` pointing to `tbl_subcategory.subcategory_id`).
* **Virtual Generated Column:** A column whose value is calculated dynamically from other columns rather than stored on disk (e.g., `cartmaster_id INT GENERATED ALWAYS AS (cartmasterid) VIRTUAL`).
* **Database Index:** A data structure (typically a B-Tree) that improves data retrieval speed on a table at the cost of additional disk writes and storage space.
* **Normalization (1NF, 2NF, 3NF):** The process of organizing table columns to reduce data redundancy and eliminate update/delete anomalies.
* **Denormalization:** The strategic retention of redundant data (e.g., storing `totalamount` directly in `tbl_cart`) to optimize query performance by reducing runtime joins.
* **Collation (`utf8mb4_general_ci`):** The set of rules governing how character strings are compared and sorted, where `ci` denotes case-insensitivity.

---

## 3. Web Architecture & Server Concepts

* **Three-Tier Architecture:** A client-server software architecture comprising:
  * **Presentation Tier:** User interface running in the web browser.
  * **Application / Logic Tier:** Web server running backend PHP business logic.
  * **Data Tier:** Database server persisting application state.
* **Apache HTTP Server:** The open-source web server daemon that receives incoming HTTP network requests and serves web pages.
* **FastCGI / mod_fcgid:** A protocol and module enabling Apache to communicate with persistent PHP interpreter processes with high performance.
* **HTTP Methods (GET vs. POST):**
  * **GET:** Appends parameters to the URL query string; used for safe, idempotent data retrieval (e.g., `ptview.php?productid=3`).
  * **POST:** Transmits parameters inside the HTTP request body; used for data creation, updates, and sensitive credential submission (e.g., `paymentaction.php`).
* **HTTP Redirect Status (302 Found):** An HTTP response header instructing the browser to navigate to a different location (e.g., redirecting to `login.php` or `cartview.php`).
* **Session (`$_SESSION`):** A server-side mechanism for persisting user authentication and state across multiple stateless HTTP page requests.
* **Session Cookie (`PHPSESSID`):** A cryptographically unique cookie stored on the client browser that maps subsequent requests back to the user's server-side session.
* **Page Controller Pattern:** An architectural pattern where each physical script (e.g., `cartaction.php`, `ordersview.php`) handles the request, executes logic, and renders or redirects the response.

---

## 4. Frontend & Scripting Concepts

* **DOM (Document Object Model):** The hierarchical tree representation of an HTML document created by the browser, allowing dynamic programmatic manipulation via JavaScript.
* **AJAX (Asynchronous JavaScript and XML):** A technique for exchanging data with the server and updating parts of a web page asynchronously without reloading the entire page.
* **jQuery:** A fast, lightweight JavaScript library that simplifies DOM traversal, event handling, animation, and AJAX calls across different browsers.
* **Cascading / Dependent Dropdown:** A UI pattern where selecting a value in a parent dropdown (e.g., District) dynamically triggers an AJAX request to filter options in a child dropdown (e.g., Location).
* **Live Stepper:** An interactive UI component with increment (`+`) and decrement (`-`) buttons that allows users to adjust numerical values within defined min/max bounds.
* **Responsive Web Design:** Designing web pages that automatically adapt their layout to varying screen dimensions and device types using CSS flexbox and media queries.
* **CSS Media Query (`@media print`):** A CSS rule applied only when the document is sent to a physical printer or PDF exporter, used in OrchestraOne to hide navigation bars and buttons.
* **Chart.js:** An HTML5 Canvas-based data visualization library that renders responsive charts from JavaScript configuration objects.

---

## 5. Security & Data Integrity Concepts

* **SQL Injection (SQLi):** A security vulnerability where malicious SQL commands are injected into input fields to manipulate database queries.
* **Input Sanitization:** The practice of cleaning and escaping user-supplied data (e.g., using `mysqli_real_escape_string()` or integer casting) before using it in program logic.
* **Path Traversal:** An exploit where attackers inject `../` directory sequences to read or write files outside the intended folder. Prevented in OrchestraOne using filename regex stripping.
* **Bcrypt (`password_hash`):** An adaptive, salted cryptographic hash function recommended for production password storage to protect against dictionary and rainbow table attacks.
* **Session Hijacking:** An attack where an unauthorized user gains access to a session by obtaining the active `PHPSESSID` token.
