<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

// ── SESSION FUNCTIONS ──────────────────────────────────────────────────────

/**
 * Check if current user is admin
 */
function isAdmin(): bool {
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'admin';
}
/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_name']);
}

/**
 * Login user - set session variables
 */
function loginUser(array $user): void {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'] ?? 'customer';
}

/**
 * Logout user - clear all session variables
 */
function logoutUser(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Create a new user
 */
function createUser(string $firstName, string $lastName, string $email, string $password, string $phone = '', string $address = ''): ?int {
    global $conn;
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("
        INSERT INTO users (first_name, last_name, email, password, phone, address, role) 
        VALUES (?, ?, ?, ?, ?, ?, 'customer')
    ");
    $stmt->bind_param("ssssss", $firstName, $lastName, $email, $hashedPassword, $phone, $address);
    
    if ($stmt->execute()) {
        return $conn->insert_id;
    }
    return null;
}

/**
 * Redirect to a URL
 */
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

/**
 * Get user by email
 */
function getUserByEmail(string $email): ?array {
    global $conn;
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, phone, address, role, created_at FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Get user by ID
 */
function getUserById(int $id): ?array {
    global $conn;
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, phone, address, role, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Get user's full name
 */
function getUserFullName(array $user): string {
    return trim($user['first_name'] . ' ' . $user['last_name']);
}

/**
 * Get user's orders
 */
function getUserOrders(int $userId): array {
    global $conn;
    $stmt = $conn->prepare("
        SELECT * FROM orders 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get products with their categories
 */
function getProducts(): array {
    global $conn;
    $sql = "
        SELECT 
            products.id,
            products.name,
            products.description,
            products.image,
            products.tag,
            products.status,
            products.category_id,
            categories.name AS category_name
        FROM products
        LEFT JOIN categories ON products.category_id = categories.id
        WHERE products.status = 'available'
        ORDER BY products.id ASC
    ";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get product sizes and prices
 */
function getProductSizes(int $productId): array {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM product_sizes WHERE product_id = ? ORDER BY price ASC");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get categories
 */
function getCategories(): array {
    global $conn;
    $result = $conn->query("SELECT id, name FROM categories ORDER BY id ASC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Require login - redirect to login if not logged in
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        setFlash('error', 'Please log in to access this page.');
        redirect('login.php');
    }
}

/**
 * Require admin role - redirect if not admin
 */
function requireAdmin(): void {
    requireLogin();
    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        setFlash('error', 'You do not have permission to access this page.');
        redirect('../index.php');
    }
}

// ── FLASH MESSAGE FUNCTIONS ─────────────────────────────────────────────────

/**
 * Set a flash message
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// ── CART FUNCTIONS ──────────────────────────────────────────────────────────

/**
 * Initialize cart session
 */
function initCart(): void {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

/**
 * Add item to cart
 */
function addToCart(int $productId, string $productName, int $sizeId, string $size, float $price, int $quantity = 1): void {
    // Make sure cart session exists and is an array
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Check if item already exists in cart
    $found = false;
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['product_id'] == $productId && $item['size_id'] == $sizeId) {
            $_SESSION['cart'][$key]['quantity'] += $quantity;
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $_SESSION['cart'][] = [
            'product_id' => $productId,
            'product_name' => $productName,
            'size_id' => $sizeId,
            'size' => $size,
            'price' => $price,
            'quantity' => $quantity
        ];
    }
}

/**
 * Get cart items
 */
function getCartItems(): array {
    // Make sure cart session exists and is an array
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    return $_SESSION['cart'];
}

/**
 * Get cart total
 */
function getCartTotal(): float {
    // Make sure cart session exists and is an array
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
        return 0;
    }
    
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

/**
 * Get cart item count
 */
function getCartCount(): int {
    // Make sure cart session exists and is an array
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
        return 0;
    }
    
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

/**
 * Remove item from cart
 */
function removeFromCart(int $index): void {
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

/**
 * Update cart item quantity
 */
function updateCartQuantity(int $index, int $quantity): void {
    if (isset($_SESSION['cart'][$index])) {
        if ($quantity <= 0) {
            removeFromCart($index);
        } else {
            $_SESSION['cart'][$index]['quantity'] = $quantity;
        }
    }
}

/**
 * Clear cart
 */
function clearCart(): void {
    $_SESSION['cart'] = [];
}

/**
 * Create order in database
 */
function createOrder(int $userId, array $cartItems, float $subtotal, float $deliveryFee, float $total, string $paymentMethod, string $deliveryAddress): ?int {
    global $conn;
    
    // Generate order number
    $orderNumber = 'CT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    
    // Insert order
    $stmt = $conn->prepare("
        INSERT INTO orders (
            user_id, 
            order_number, 
            subtotal, 
            delivery_fee, 
            total_amount, 
            payment_method, 
            order_status, 
            delivery_address, 
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, NOW())
    ");
    $stmt->bind_param("isddsss", $userId, $orderNumber, $subtotal, $deliveryFee, $total, $paymentMethod, $deliveryAddress);
    
    if (!$stmt->execute()) {
        error_log("Order creation failed: " . $stmt->error);
        return null;
    }
    
    $orderId = $conn->insert_id;
    
    // Insert order items
    foreach ($cartItems as $item) {
        $itemSubtotal = $item['price'] * $item['quantity'];
        $stmt2 = $conn->prepare("
            INSERT INTO order_items (
                order_id, 
                product_id, 
                product_name, 
                size, 
                quantity, 
                unit_price
            ) VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt2->bind_param("iissid", $orderId, $item['product_id'], $item['product_name'], $item['size'], $item['quantity'], $item['price']);
        $stmt2->execute();
    }
    
    return $orderId;
}

/**
 * Get order by ID
 */
function getOrderById(int $orderId): ?array {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Get order items by order ID
 */
function getOrderItems(int $orderId): array {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Create order in database with delivery type
 */
function createOrderWithType(int $userId, array $cartItems, float $subtotal, float $deliveryFee, float $total, string $paymentMethod, string $deliveryAddress, string $deliveryType = 'delivery'): ?int {
    global $conn;
    
    // Generate order number
    $orderNumber = 'CT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    
    // Insert order with delivery_type
    $stmt = $conn->prepare("
        INSERT INTO orders (
            user_id, 
            order_number, 
            subtotal, 
            delivery_fee, 
            total_amount, 
            payment_method, 
            order_status, 
            delivery_address,
            delivery_type,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, ?, NOW())
    ");
    $stmt->bind_param("isddssss", $userId, $orderNumber, $subtotal, $deliveryFee, $total, $paymentMethod, $deliveryAddress, $deliveryType);
    
    if (!$stmt->execute()) {
        error_log("Order creation failed: " . $stmt->error);
        return null;
    }
    
    $orderId = $conn->insert_id;
    
    // Insert order items
    foreach ($cartItems as $item) {
        $itemSubtotal = $item['price'] * $item['quantity'];
        $stmt2 = $conn->prepare("
            INSERT INTO order_items (
                order_id, 
                product_id, 
                product_name, 
                size, 
                quantity, 
                unit_price
            ) VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt2->bind_param("iissid", $orderId, $item['product_id'], $item['product_name'], $item['size'], $item['quantity'], $item['price']);
        $stmt2->execute();
    }
    
    return $orderId;
}

?>