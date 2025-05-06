<?php
require_once 'config.php';

// Verify CSRF token
verify_csrf_token();

// Check if product_id is provided and is a valid integer
if (isset($_POST['product_id']) && is_numeric($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    
    // Fetch product details from database
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if ($product) {
            // Check if product is already in cart
            if (isset($_SESSION['cart'][$product_id])) {
                // Increment quantity (with a reasonable limit)
                if ($_SESSION['cart'][$product_id]['quantity'] < 10) {
                    $_SESSION['cart'][$product_id]['quantity']++;
                }
            } else {
                // Add product to cart
                $_SESSION['cart'][$product_id] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image' => $product['image'],
                    'quantity' => 1
                ];
            }
            
            // Set a success message
            $_SESSION['message'] = "Product added to cart successfully.";
            $_SESSION['message_type'] = "success";
            
            // Redirect back to products or to cart
            header("Location: cart.php");
            exit;
        } else {
            $_SESSION['message'] = "Product not found.";
            $_SESSION['message_type'] = "error";
        }
    } catch(PDOException $e) {
        error_log("Error adding to cart: " . $e->getMessage());
        $_SESSION['message'] = "Could not add product to cart. Please try again.";
        $_SESSION['message_type'] = "error";
    }
}

// If something went wrong, redirect to index
header("Location: index.php");
exit;