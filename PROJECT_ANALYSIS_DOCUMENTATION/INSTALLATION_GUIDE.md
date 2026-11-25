# INSTALLATION & EXECUTION GUIDE: OrchestraOne (MelodyMart)
**Complete Step-by-Step Setup, Database Deployment, & Troubleshooting Manual**

---

## 1. Prerequisites & System Requirements

### Hardware Requirements
* **Processor:** Intel Core i3 / AMD Ryzen 3 or higher.
* **RAM:** Minimum 4 GB (8 GB recommended for running WAMP Server with IDE).
* **Storage:** 500 MB free hard disk space for code, database, and uploaded instrument images.

### Software Requirements
* **Operating System:** Windows 10 / 11 (64-bit), Linux (Ubuntu 20.04+), or macOS.
* **Web Server:** Apache 2.4.x.
* **PHP Runtime:** PHP 8.0 or higher (PHP 8.2 / 8.5 recommended).
  * Required PHP Extensions: `mysqli`, `session`, `json`, `mbstring`, `fileinfo`.
* **Database Server:** MySQL Server 8.0+ or MariaDB 10.4+.
* **Web Browser:** Google Chrome, Mozilla Firefox, Microsoft Edge, or Safari.

---

## 2. Step-by-Step Installation Procedure (Windows WAMP Server)

### Step 1: Install WAMP Server
1. Download and install **WAMP Server 64-bit** from the official portal (`https://www.wampserver.com/`).
2. Ensure required Visual C++ Redistributable packages (2012, 2015-2022) are installed.
3. Start WAMP Server and ensure the tray icon turns **Green** (indicating Apache and MySQL services are active).

---

### Step 2: Deploy Project Files
1. Navigate to your web server document root directory:
   ```
   C:\wamp64\www\
   ```
2. Place the project folder inside `www`:
   ```
   C:\wamp64\www\OrchestraOne
   ```
3. Verify that directory write permissions are enabled on the uploads directory:
   ```
   C:\wamp64\www\OrchestraOne\uploads\
   ```
   *(This directory holds uploaded instrument photos, category banners, warranty receipts, and defect photos).*

---

### Step 3: Database Initialization
1. Open your web browser and navigate to **phpMyAdmin**:
   ```
   http://localhost/phpmyadmin/
   ```
2. Log in with your database credentials (default: username `root`, password `""` [blank]).
3. In the **SQL** tab, execute the following command to create the database:
   ```sql
   CREATE DATABASE IF NOT EXISTS `db_orchestraone`
   DEFAULT CHARACTER SET utf8mb4 
   COLLATE utf8mb4_general_ci;
   ```

---

### Step 4: Import Database Schema & Seed Data
1. Select the newly created database `db_orchestraone` in phpMyAdmin.
2. In the **SQL** tab, paste and execute the complete deployment script below:

```sql
-- 1. Admin Authentication Table
CREATE TABLE IF NOT EXISTS `tbl_adminlogin` (
  `loginid` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`loginid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_adminlogin` (`loginid`, `username`, `password`) VALUES
(1, 'admin', 'admin')
ON DUPLICATE KEY UPDATE `password` = 'admin';

-- 2. Geographic Master Tables
CREATE TABLE IF NOT EXISTS `tbl_district` (
  `district_id` int NOT NULL AUTO_INCREMENT,
  `district_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tbl_location` (
  `location_id` int NOT NULL AUTO_INCREMENT,
  `location_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `district_id` int NOT NULL,
  PRIMARY KEY (`location_id`),
  KEY `idx_location_district` (`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Customer Profile Table
CREATE TABLE IF NOT EXISTS `tbl_customer` (
  `customer_id` int NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `location_id` int NOT NULL,
  `phone_no` bigint NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `reg_date` date NOT NULL,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `unique_customer_username` (`username`),
  KEY `idx_customer_location` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_customer` (`customer_id`, `customer_name`, `location_id`, `phone_no`, `email`, `username`, `password`, `reg_date`) VALUES
(1, 'Demo Customer', 1, 9876543210, 'customer@melody.com', 'customer', 'customer', CURDATE())
ON DUPLICATE KEY UPDATE `password` = 'customer';

-- 4. Catalog Taxonomy Master Tables
CREATE TABLE IF NOT EXISTS `tbl_category` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `category_image` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tbl_subcategory` (
  `subcategory_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `category_id` int NOT NULL,
  PRIMARY KEY (`subcategory_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `fk_subcat_category` FOREIGN KEY (`category_id`) REFERENCES `tbl_category` (`category_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Product Catalog Table
CREATE TABLE IF NOT EXISTS `tbl_product` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `product_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `subcat` int NOT NULL,
  `subcategory_id` int GENERATED ALWAYS AS (`subcat`) VIRTUAL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `image` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `stock` int NOT NULL,
  `pstatus` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'available',
  `brand` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`product_id`),
  KEY `idx_product_subcat` (`subcat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6. Shopping Cart Table
CREATE TABLE IF NOT EXISTS `tbl_cart` (
  `cart_id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `totalamount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `customer_id` int NOT NULL,
  `cart_date` date NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'cart',
  `cartmaster_id` int DEFAULT NULL,
  PRIMARY KEY (`cart_id`),
  KEY `idx_cart_product` (`product_id`),
  KEY `idx_cart_customer` (`customer_id`),
  KEY `idx_cart_cartmaster` (`cartmaster_id`),
  KEY `idx_cart_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 7. Order Master Table
CREATE TABLE IF NOT EXISTS `tbl_cartmaster` (
  `cartmaster_id` int NOT NULL AUTO_INCREMENT,
  `grandtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `customer_id` int NOT NULL,
  `cmstatus` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'paid',
  PRIMARY KEY (`cartmaster_id`),
  KEY `idx_cartmaster_customer` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 8. Delivery Logistics Table
CREATE TABLE IF NOT EXISTS `tbl_deliveryaddress` (
  `deliveryaddressid` int NOT NULL AUTO_INCREMENT,
  `personname` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `mobno` bigint NOT NULL,
  `address` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `pincode` int NOT NULL,
  `cartmasterid` int NOT NULL,
  `cartmaster_id` int GENERATED ALWAYS AS (`cartmasterid`) VIRTUAL,
  PRIMARY KEY (`deliveryaddressid`),
  KEY `idx_delivery_cartmaster` (`cartmasterid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 9. Payment Settlement Table
CREATE TABLE IF NOT EXISTS `tbl_payment` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `cartmaster_id` int NOT NULL,
  `payment_date` date NOT NULL,
  `pamount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `customer_id` int NOT NULL,
  `pstatus` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `commission` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`payment_id`),
  KEY `idx_payment_cartmaster` (`cartmaster_id`),
  KEY `idx_payment_customer` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 10. Digital Warranty Claims Table
CREATE TABLE IF NOT EXISTS `tbl_warranty` (
  `warranty_id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `cart_id` int NOT NULL,
  `applydate` date NOT NULL,
  `wimg` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `issuedesc` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `prod_img` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `wstatus` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `responsedate` date DEFAULT NULL,
  PRIMARY KEY (`warranty_id`),
  KEY `idx_warranty_cart` (`cart_id`),
  KEY `idx_warranty_customer` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

---

### Step 5: Database Connection Configuration
Open `c:\wamp64\www\OrchestraOne\dboperation.php` and verify the connection parameters:
```php
<?php
if (!class_exists('dboperation')) {
    class dboperation
    {
        public $con, $res;

        function __construct()
        {
            // Host, Username, Password, Database Name
            $this->con = mysqli_connect("localhost", "root", "", "db_orchestraone");
            if (!$this->con) {
                die("Connection failed: " . mysqli_connect_error());
            }
        }

        public function executequery($sql)
        {
            $this->res = mysqli_query($this->con, $sql);
            return $this->res;
        }
    }
}
?>
```
*(If your MySQL server has a password set on the `root` account, replace `""` with your actual password).*

---

## 3. Running & Accessing the Application

1. Open your web browser.
2. Access the project home entry point:
   ```
   http://localhost/OrchestraOne/
   ```
   *(This automatically routes to `Guest/index.php`).*

### Default Test Login Credentials

| Role | Portal URL | Username | Password | Access Description |
| :--- | :--- | :--- | :--- | :--- |
| **Administrator** | `http://localhost/OrchestraOne/Guest/login.php` | `admin` | `admin` | Master dashboard, inventory control, invoice printing, warranty claims approval. |
| **Customer** | `http://localhost/OrchestraOne/Guest/login.php` | `customer` | `customer` | Instrument specifications, shopping cart, checkout, orders history, warranty filing. |

---

## 4. Troubleshooting & Common Issues

| Issue / Error Symptom | Cause | Solution |
| :--- | :--- | :--- |
| **"Connection failed: Access denied for user 'root'@'localhost'"** | MySQL root password mismatch. | Open `dboperation.php` and verify the password string matches your local MySQL setup. |
| **"404 Not Found"** | Project folder placed in incorrect directory. | Ensure the folder is placed in `C:\wamp64\www\OrchestraOne` (exact case matching). |
| **WAMP Icon remains Orange** | Port 80 or Port 3306 conflict (often Skype or IIS). | In WAMP menu, choose *Apache $\rightarrow$ Service administration $\rightarrow$ Test Port 80*, or change Apache port to 8080 in `httpd.conf`. |
| **Uploaded instrument photos do not appear** | Missing write permissions or missing folder. | Ensure the folder `C:\wamp64\www\OrchestraOne\uploads\` exists and is writable. |
| **"Item is already in cart" alert appears** | Normal expected duplicate protection behavior. | Adjust the instrument quantity directly in your cart (`cartview.php`) instead of adding it again from the catalog. |
