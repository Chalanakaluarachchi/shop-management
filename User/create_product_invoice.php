<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: user_login.php");
    exit;
}

include('../config.php');

// Handle form submission for creating product invoice
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $customer_name = $_POST['customer_name'];
    $mobile_number = $_POST['mobile_number'];
    $product = $_POST['product'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];
    $discount = $_POST['discount'];
    $total_price = ($quantity * $unit_price) * (1 - $discount / 100);

    // Insert the product invoice into the database
    $stmt = $conn->prepare("INSERT INTO invoices (user_id, customer_name, mobile_number, product, quantity, unit_price, total_price, discount, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->execute([$user_id, $customer_name, $mobile_number, $product, $quantity, $unit_price, $total_price, $discount]);

    // Redirect to avoid form resubmission
    header("Location: create_product_invoice.php");
    exit;
}

// Fetch only product invoices
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM invoices WHERE user_id = ? AND product IS NOT NULL ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$invoices = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Product Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 items-center justify-center min-h-screen">
    <div class="flex justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-lg">
            <h2 class="text-3xl font-semibold text-center text-gray-700 mb-6">Create Product Invoice</h2>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-gray-600 text-sm font-semibold" for="customer_name">Customer Name</label>
                    <input type="text" name="customer_name" id="customer_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label class="block text-gray-600 text-sm font-semibold" for="mobile_number">Mobile Number</label>
                    <input type="text" name="mobile_number" id="mobile_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label class="block text-gray-600 text-sm font-semibold" for="product">Product</label>
                    <input type="text" name="product" id="product" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="flex space-x-4">
                    <div class="w-1/2">
                        <label class="block text-gray-600 text-sm font-semibold" for="quantity">Quantity</label>
                        <input type="number" name="quantity" id="quantity" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div class="w-1/2">
                        <label class="block text-gray-600 text-sm font-semibold" for="unit_price">Unit Price</label>
                        <input type="number" name="unit_price" id="unit_price" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-600 text-sm font-semibold" for="discount">Discount (%)</label>
                    <input type="number" name="discount" id="discount" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="w-full py-2 px-4 bg-blue-500 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Submit Invoice</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Invoices History Table -->
    <div class="flex justify-center">
        <div class="bg-white mt-12 p-8 rounded-lg shadow-lg w-full max-w-2xl">
            <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Invoices History</h2>

            <table class="min-w-full bg-white border">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Customer Name</th>
                        <th class="py-2 px-4 border-b">Product</th>
                        <th class="py-2 px-4 border-b">Total Price</th>
                        <th class="py-2 px-4 border-b">Status</th>
                        <th class="py-2 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $invoice): ?>
                        <tr>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($invoice['customer_name']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($invoice['product']); ?></td>
                            <td class="py-2 px-4 border-b">$<?php echo number_format($invoice['total_price'], 2); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($invoice['status']); ?></td>
                            <td class="py-2 px-4 border-b">
                                <a href="download_invoice_image.php?id=<?php echo $invoice['id']; ?>" class="text-blue-500 hover:underline">Print</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
