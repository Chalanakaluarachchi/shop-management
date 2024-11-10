<?php
include('../config.php');

// Add product if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_name'])) {
    $product_name = $_POST['product_name'];
    $buying_price = $_POST['buying_price'];
    $selling_price = $_POST['selling_price'];
    $quarantine = $_POST['quarantine'];

    $stmt = $conn->prepare("INSERT INTO products (product_name, buying_price, selling_price, quarantine, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$product_name, $buying_price, $selling_price, $quarantine]);
    header("Location: manage_products.php");
}

// Fetch products
$products = $conn->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="container mx-auto">
        <h1 class="text-3xl font-bold mb-4">Manage Products</h1>

        <!-- Add Product Form -->
        <form method="POST" action="" class="bg-white p-4 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-semibold mb-2">Add New Product</h2>
            <div class="mb-4">
                <label for="product_name" class="block text-gray-700 font-semibold">Product Name</label>
                <input type="text" id="product_name" name="product_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="buying_price" class="block text-gray-700 font-semibold">Buying Price</label>
                <input type="number" id="buying_price" name="buying_price" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="selling_price" class="block text-gray-700 font-semibold">Selling Price</label>
                <input type="number" id="selling_price" name="selling_price" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="quarantine" class="block text-gray-700 font-semibold">Quarantine</label>
                <input type="text" id="quarantine" name="quarantine" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-700">Add Product</button>
        </form>

        <!-- Products Table -->
        <table class="min-w-full bg-white border rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-200 text-gray-600 text-left text-sm leading-normal">
                    <th class="py-3 px-6">ID</th>
                    <th class="py-3 px-6">Product Name</th>
                    <th class="py-3 px-6">Buying Price</th>
                    <th class="py-3 px-6">Selling Price</th>
                    <th class="py-3 px-6">Quarantine</th>
                    <th class="py-3 px-6">Created At</th>
                    <th class="py-3 px-6">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php foreach ($products as $product): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6"><?= htmlspecialchars($product['id']) ?></td>
                    <td class="py-3 px-6"><?= htmlspecialchars($product['product_name']) ?></td>
                    <td class="py-3 px-6"><?= htmlspecialchars($product['buying_price']) ?></td>
                    <td class="py-3 px-6"><?= htmlspecialchars($product['selling_price']) ?></td>
                    <td class="py-3 px-6"><?= htmlspecialchars($product['quarantine']) ?></td>
                    <td class="py-3 px-6"><?= htmlspecialchars($product['created_at']) ?></td>
                    <td class="py-3 px-6 space-x-4">
                        <a href="edit_product.php?id=<?= $product['id'] ?>" class="text-blue-500 hover:underline">Edit</a>
                        <a href="delete_product.php?id=<?= $product['id'] ?>" class="text-red-500 hover:underline" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
