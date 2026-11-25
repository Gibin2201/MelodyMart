# SYSTEM ARCHITECTURE & DIAGRAMS: OrchestraOne (MelodyMart)
**Detailed Multi-Tier Architecture & Formal Engineering Diagrams**

---

## 1. Overall System Architecture

OrchestraOne implements a **Classic Three-Tier Web Architecture** combining a presentation layer (Client Browser), application logic layer (Apache 2.4 & PHP 8.5), and data persistence layer (MySQL InnoDB).

```mermaid
flowchart TD
    subgraph ClientLayer["Presentation Layer (Client Browser)"]
        UI_Guest["Guest Views (HTML5 / Bootstrap 5 / Vanilla JS)"]
        UI_Customer["Customer Portal (DM Sans / Stepper JS / AJAX)"]
        UI_Admin["Admin Control Center (Chart.js / DataTables / Print CSS)"]
    end

    subgraph ServerLayer["Application & Controller Layer (Apache & PHP 8.5)"]
        Router["HTTP Request Routing (.php Page Controllers)"]
        AuthModule["Session Manager & Authentication Guard"]
        CartEngine["Cart Engine & Duplicate Protection"]
        OrderProcessor["Checkout & Inventory Synchronization"]
        WarrantyEngine["Warranty Proof Processing & File Handler"]
        DBAL["Database Abstraction Layer (dboperation class)"]
    end

    subgraph DataLayer["Data Layer (MySQL 8 / MariaDB - InnoDB)"]
        DB[("db_orchestraone
        12 Relational Tables")]
        FileSystem[("Local Storage
        c:/wamp64/www/OrchestraOne/uploads/")]
    end

    ClientLayer -->|HTTP / HTTPS Requests| Router
    Router --> AuthModule
    AuthModule --> CartEngine & OrderProcessor & WarrantyEngine
    CartEngine & OrderProcessor & WarrantyEngine --> DBAL
    DBAL -->|TCP / UNIX Socket Port 3306| DB
    WarrantyEngine -->|File I/O move_uploaded_file| FileSystem
```

---

## 2. Request-Response Lifecycle
1. **Client Request:** The user navigates to a URL (e.g., `Customer/ptview.php?productid=3`) via HTTP GET or submits a form via HTTP POST.
2. **Server Dispatching:** Apache receives the request, evaluates the `.php` extension, and forwards it to the PHP 8 interpreter engine.
3. **Session Verification:** The script starts or resumes the user session (`session_start()`), inspecting `$_SESSION['customer_id']` or `$_SESSION['Login_id']`. Unauthenticated requests to protected endpoints are redirected to `Guest/login.php`.
4. **Data Access (DBAL):** The page instantiates `new dboperation()` and executes parameterized/sanitized queries against `db_orchestraone`.
5. **Business Logic Execution:** The server computes totals, validates inventory availability, checks duplicate cart conditions, or handles file uploads.
6. **HTML Response Rendering:** PHP dynamically embeds query results into the HTML/Bootstrap presentation template and streams the HTTP 200 payload back to the browser.

---

## 3. Comprehensive Diagrams (Mermaid Specifications)

### Diagram 1: System Architecture Diagram
```mermaid
flowchart LR
    subgraph Browser["Client Browser Tier"]
        HTML["HTML5 / Semantic CSS3"]
        JS["JavaScript / jQuery 3.6 / AJAX"]
        Charts["Chart.js Analytics Engine"]
    end

    subgraph WebServer["Web Server Tier (Apache)"]
        ApacheDaemon["Apache 2.4 Daemon"]
        ModPHP["PHP 8.5 Module / FastCGI"]
    end

    subgraph AppLogic["Business Logic Tier (PHP Scripts)"]
        Controllers["Page Controllers (*action.php)"]
        AuthFilter["Session Access Filters"]
        FileService["Upload Security & Sanitizer"]
    end

    subgraph Persistence["Persistence Tier"]
        MySQL[("MySQL InnoDB Engine
        (db_orchestraone)")]
        MediaStorage[("Uploads Directory
        (/uploads/)")]
    end

    Browser <-->|HTTP / AJAX JSON| WebServer
    WebServer <--> ModPHP
    ModPHP <--> AppLogic
    AppLogic <-->|SQL Queries| MySQL
    FileService <-->|Binary Storage| MediaStorage
```

---

### Diagram 2: Use Case Diagram
```mermaid
flowchart TD
    Guest((Guest))
    Customer((Registered Customer))
    Admin((Store Administrator))

    subgraph MelodyMartSystem["OrchestraOne E-Commerce Platform"]
        UC1[Browse Instrument Catalog]
        UC2[Register Customer Account]
        UC3[Login / Authenticate]
        UC4[View Instrument Profile & Specs]
        UC5[Manage Cart Items & Quantities]
        UC6[Checkout & Shipping Entry]
        UC7[Complete Payment Settlement]
        UC8[View Past Orders & Invoices]
        UC9[File Digital Warranty Claim]
        
        UC10[Manage Categories & Subcategories]
        UC11[Manage Instrument Inventory & Stock]
        UC12[Review & Print Customer Invoices]
        UC13[Adjudicate Warranty Claims]
        UC14[View Monthly Sales & Revenue Charts]
    end

    Guest --> UC1
    Guest --> UC2
    Guest --> UC3

    Customer --> UC3
    Customer --> UC4
    Customer --> UC5
    Customer --> UC6
    Customer --> UC7
    Customer --> UC8
    Customer --> UC9

    Admin --> UC3
    Admin --> UC10
    Admin --> UC11
    Admin --> UC12
    Admin --> UC13
    Admin --> UC14
```

---

### Diagram 3: Entity-Relationship (ER) Diagram
```mermaid
erDiagram
    TBL_DISTRICT {
        int district_id PK
        varchar district_name
    }

    TBL_LOCATION {
        int location_id PK
        varchar location_name
        int district_id FK
    }

    TBL_CUSTOMER {
        int customer_id PK
        varchar customer_name
        int location_id FK
        bigint phone_no
        varchar email
        varchar username UK
        varchar password
        date reg_date
    }

    TBL_CATEGORY {
        int category_id PK
        varchar category_name
        varchar category_image
    }

    TBL_SUBCATEGORY {
        int subcategory_id PK
        varchar name
        varchar image
        text description
        int category_id FK
    }

    TBL_PRODUCT {
        int product_id PK
        varchar product_name
        int subcat FK
        decimal amount
        varchar image
        int stock
        varchar pstatus
        varchar brand
        text description
    }

    TBL_CART {
        int cart_id PK
        int product_id FK
        int quantity
        decimal totalamount
        int customer_id FK
        date cart_date
        varchar status
        int cartmaster_id FK
    }

    TBL_CARTMASTER {
        int cartmaster_id PK
        decimal grandtotal
        int customer_id FK
        varchar cmstatus
    }

    TBL_DELIVERYADDRESS {
        int deliveryaddressid PK
        varchar personname
        bigint mobno
        varchar address
        int pincode
        int cartmasterid FK
    }

    TBL_PAYMENT {
        int payment_id PK
        int cartmaster_id FK
        date payment_date
        decimal pamount
        int customer_id FK
        varchar pstatus
        decimal commission
    }

    TBL_WARRANTY {
        int warranty_id PK
        int customer_id FK
        int cart_id FK
        date applydate
        varchar wimg
        varchar issuedesc
        varchar prod_img
        varchar wstatus
        date responsedate
    }

    TBL_ADMINLOGIN {
        int loginid PK
        varchar username
        varchar password
    }

    TBL_DISTRICT ||--o{ TBL_LOCATION : contains
    TBL_LOCATION ||--o{ TBL_CUSTOMER : accommodates
    TBL_CATEGORY ||--o{ TBL_SUBCATEGORY : partitions
    TBL_SUBCATEGORY ||--o{ TBL_PRODUCT : groups
    TBL_CUSTOMER ||--o{ TBL_CART : selects
    TBL_PRODUCT ||--o{ TBL_CART : listed_in
    TBL_CUSTOMER ||--o{ TBL_CARTMASTER : initiates
    TBL_CARTMASTER ||--o{ TBL_CART : encapsulates
    TBL_CARTMASTER ||--|| TBL_DELIVERYADDRESS : destinations_to
    TBL_CARTMASTER ||--|| TBL_PAYMENT : settles
    TBL_CART ||--o{ TBL_WARRANTY : claims
    TBL_CUSTOMER ||--o{ TBL_WARRANTY : logs
```

---

### Diagram 4: Data Flow Diagram (Level 0 - Context Level)
```mermaid
flowchart TD
    GuestUser["Guest User"]
    CustomerUser["Customer User"]
    AdminUser["Store Administrator"]
    
    System["(0.0)
    OrchestraOne
    E-Commerce & Warranty System"]

    GuestUser -->|Registration & Login Requests| System
    System -->|Catalog Views & Auth Tokens| GuestUser

    CustomerUser -->|Orders, Cart Updates & Warranty Claims| System
    System -->|Invoices, Order Confirmations & Claim Status| CustomerUser

    AdminUser -->|Product Updates, Pricing & Warranty Decisions| System
    System -->|Revenue Analytics, Dispatch Slips & Audit Tables| AdminUser
```

---

### Diagram 5: Data Flow Diagram (Level 1 - Core Subsystems)
```mermaid
flowchart TD
    Customer((Customer))
    Admin((Administrator))

    subgraph Subsystems
        P1["1.0 User Authentication & Onboarding"]
        P2["2.0 Catalog & Search Engine"]
        P3["3.0 Shopping Cart & Integrity Check"]
        P4["4.0 Order Settlement & Inventory Sync"]
        P5["5.0 Warranty Lifecycle Engine"]
        P6["6.0 Admin Management & Analytics"]
    end

    subgraph DataStores
        D1[("tbl_customer & tbl_adminlogin")]
        D2[("tbl_category, tbl_subcategory & tbl_product")]
        D3[("tbl_cart")]
        D4[("tbl_cartmaster, tbl_payment & tbl_deliveryaddress")]
        D5[("tbl_warranty")]
    end

    Customer -->|Sign Up / Sign In| P1
    P1 <--> D1
    
    Customer -->|Browse Gear| P2
    P2 <--> D2

    Customer -->|Add to Cart / Change Qty| P3
    P3 <--> D3
    P3 <--> D2

    Customer -->|Checkout & Submit Payment| P4
    P4 -->|Write Order & Address| D4
    P4 -->|Update Cart Status to Paid| D3
    P4 -->|Decrement Stock| D2

    Customer -->|Submit Claim & Photos| P5
    P5 <--> D5
    P5 <--> D3

    Admin -->|CRUD Products & Stock| P6
    Admin -->|Adjudicate Warranty| P6
    Admin -->|Review Sales Reports| P6
    P6 <--> D2
    P6 <--> D4
    P6 <--> D5
```

---

### Diagram 6: Customer Activity Diagram (Purchase Workflow)
```mermaid
stateDiagram-v2
    [*] --> BrowseCatalog
    BrowseCatalog --> SelectInstrument
    SelectInstrument --> CheckCartExistence
    
    state CheckCartExistence <<choice>>
    CheckCartExistence --> ShowAlreadyInCartAlert : Item in Cart
    CheckCartExistence --> AddNewCartItem : Item not in Cart
    
    ShowAlreadyInCartAlert --> ViewCart
    AddNewCartItem --> ViewCart
    
    ViewCart --> AdjustQuantities
    AdjustQuantities --> ProceedToCheckout
    ProceedToCheckout --> EnterDeliveryAddress
    EnterDeliveryAddress --> EnterPaymentDetails
    
    state PaymentValidation <<choice>>
    PaymentValidation --> FailureAlert : Invalid Card / Form
    PaymentValidation --> ProcessSettlement : Valid Credentials
    
    FailureAlert --> EnterPaymentDetails
    ProcessSettlement --> PersistOrderMaster
    PersistOrderMaster --> LogPayment
    LogPayment --> DecrementProductStock
    DecrementProductStock --> MarkCartItemsPaid
    MarkCartItemsPaid --> DisplayPrintableInvoice
    DisplayPrintableInvoice --> [*]
```

---

### Diagram 7: Admin Sequence Diagram (Order Fulfillment)
```mermaid
sequenceDiagram
    autonumber
    actor Admin as Store Administrator
    participant Browser as Admin UI
    participant Server as Admin Page Controller
    participant DB as MySQL Database

    Admin->>Browser: Opens Customer Orders (ordersview.php)
    Browser->>Server: HTTP GET ordersview.php
    Server->>DB: SELECT cm.*, p.*, cus.* FROM tbl_cartmaster cm ...
    DB-->>Server: Return active customer orders
    Server-->>Browser: Render orders table with badges
    
    Admin->>Browser: Clicks specific Order #MM-ORD-XXXXX
    Browser->>Server: HTTP GET orderdetail.php?order_id=X
    Server->>DB: Query master order, buyer details, delivery address
    Server->>DB: Query itemized purchased products from tbl_cart
    DB-->>Server: Return consolidated order details
    Server-->>Browser: Render itemized packing slip & invoice
    
    Admin->>Browser: Clicks "Print Packing Slip"
    Browser->>Admin: Triggers browser print dialog (window.print())
```

---

### Diagram 8: User Sequence Diagram (Add to Cart & Duplicate Guard)
```mermaid
sequenceDiagram
    autonumber
    actor Customer as Musician / Customer
    participant Browser as Product View (ptview.php)
    participant CartController as cartaction.php
    participant DB as MySQL Database
    participant CartView as cartview.php

    Customer->>Browser: Selects quantity and clicks "Add to Cart"
    Browser->>CartController: HTTP POST productid, quantity, totalamount
    CartController->>CartController: Validate active customer session
    CartController->>DB: SELECT * FROM tbl_cart WHERE product_id=X AND customer_id=Y AND status='cart'
    
    alt Item Already Exists in Cart
        DB-->>CartController: Found 1 matching row
        CartController-->>Browser: JavaScript Alert ("Item is already in cart")
        CartController-->>CartView: 302 Redirect to cartview.php
    else Item Not Yet in Cart
        DB-->>CartController: Found 0 matching rows
        CartController->>DB: INSERT INTO tbl_cart (product_id, quantity, totalamount, ...)
        DB-->>CartController: Insert OK (cart_id generated)
        CartController-->>CartView: 302 Redirect to cartview.php
    end
```

---

### Diagram 9: Class Diagram (Database Model & Controllers)
```mermaid
classDiagram
    class dboperation {
        +mysqli con
        +mysqli_result res
        +__construct()
        +executequery(string sql) mysqli_result
    }

    class CustomerController {
        +login(string user, string pass)
        +register(array data)
        +addToCart(int pid, int qty)
        +updateCart(int cartid, string action)
        +settlePayment(array orderData)
        +submitWarranty(int cartid, string issue, array files)
    }

    class AdminController {
        +manageCategory(string action, array data)
        +manageSubcategory(string action, array data)
        +manageProduct(string action, array data)
        +quickStockUpdate(int pid, int stock, string status)
        +adjudicateWarranty(int wid, string decision)
        +exportSalesReport(string from, string to)
    }

    class tbl_product {
        +int product_id
        +string product_name
        +int subcat
        +decimal amount
        +string image
        +int stock
        +string pstatus
        +string brand
        +string description
    }

    class tbl_cart {
        +int cart_id
        +int product_id
        +int quantity
        +decimal totalamount
        +int customer_id
        +date cart_date
        +string status
        +int cartmaster_id
    }

    class tbl_cartmaster {
        +int cartmaster_id
        +decimal grandtotal
        +int customer_id
        +string cmstatus
    }

    CustomerController ..> dboperation : uses
    AdminController ..> dboperation : uses
    tbl_cart --> tbl_product : references
    tbl_cart --> tbl_cartmaster : grouped_into
```
