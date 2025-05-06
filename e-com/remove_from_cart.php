<?php
require_once 'config.php';

// Verify CSRF token
verify_csrf_token();

// Check if product_id is provided
if (isset($_POST['product_id']) && is_numeric($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    
    // Remove item from cart
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        $_SESSION['message'] = "Item removed from cart.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Item not found in cart.";
        $_SESSION['message_type'] = "error";
    }
}

// Redirect back to cart
header("Location: cart.php");
exit;