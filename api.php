<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:3000'); // Allow React
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$jsonFile = 'products.json';
$uploadDir = 'uploads/';

// Read products from JSON
function readProducts() {
    global $jsonFile;
    return json_decode(file_get_contents($jsonFile), true) ?: [];
}

// Write products to JSON
function writeProducts($products) {
    global $jsonFile;
    file_put_contents($jsonFile, json_encode($products, JSON_PRETTY_PRINT));
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Fetch all products
    $products = readProducts();
    echo json_encode($products);
} elseif ($method === 'POST') {
    // Add a new product
    $products = readProducts();

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $imagePath = $uploadDir . $imageName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $input = $_POST; // Get form data
            $input['image_url'] = $imagePath;
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to upload image']);
            exit;
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Image is required']);
        exit;
    }

    // Validate input
    if (empty($input['name']) || empty($input['price'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Name and price are required']);
        exit;
    }

    // Add product
    $product = [
        'id' => count($products) + 1,
        'name' => $input['name'],
        'description' => $input['description'] ?? '',
        'price' => floatval($input['price']),
        'image_url' => $input['image_url'],
        'seller_id' => $input['seller_id'] ?? 1, // Hardcoded for now
    ];
    $products[] = $product;
    writeProducts($products);

    http_response_code(201);
    echo json_encode($product);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>