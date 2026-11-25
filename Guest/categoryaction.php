<?php
/**
 * Category Navigation Action for Guests
 * Checks if the user is authenticated as a customer:
 * - If logged in: redirects to Customer/subcatview.php with the selected category ID
 * - If not logged in: redirects to Guest/login.php with an alert
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cid = isset($_GET['cid']) ? (int)$_GET['cid'] : 0;

if (isset($_SESSION['customer_id']) && !empty($_SESSION['customer_id'])) {
    // Authenticated customer: redirect to subcategories of this category
    if ($cid > 0) {
        header("Location: ../Customer/subcatview.php?categoryid=" . $cid);
    } else {
        header("Location: ../Customer/catview.php");
    }
    exit();
} else {
    // Guest not logged in: move directly to login page without popup
    header("Location: login.php");
    exit();
}
