<?php
require_once 'config.php';

// Verify CSRF token
verify_csrf_token();

// Check if product_id and quantity are provided
if (isset($_POST['product_id']) && isset($_POST['quantity']) && 
    is_numeric($_POST['product_id']) && is_numeric($_POST['quantity'])) {
    
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    
    // Validate quantity (must be between 1 and 10)
    if ($quantity < 1) {
        $quantity = 1;
    } elseif ($quantity > 10) {
        $quantity = 10;
    }
    
    // Update quantity if item exists in cart
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        $_SESSION['message'] = "Cart updated successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Item not found in cart.";
        $_SESSION['message_type'] = "error";
    }
}

// Redirect back to cart
header("Location: cart.php");
exit;