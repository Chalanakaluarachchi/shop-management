<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

include('../config.php');

// Fetch all invoices
$stmt = $conn->query("SELECT invoices.*, users.username FROM invoices JOIN users ON invoices.user_id = users.id ORDER BY created_at DESC");
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle approval/denial
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoice_id = $_POST['invoice_id'];
    $action = $_POST['action'];
    
    if ($action == 'approve') {
        $stmt = $conn->prepare("UPDATE invoices SET status = 'approved', approved_at = NOW() WHERE id = :invoice_id");
    } elseif ($action == 'deny') {
        $stmt = $conn->prepare("UPDATE invoices SET status = 'denied' WHERE id = :invoice_id");
    }
    $stmt->execute(['invoice_id' => $invoice_id]);

    header("Location: new_invoice.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Invoices</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 p-6">

    <div class="max-w-7xl mx-auto bg-white p-8 rounded-lg shadow-lg">
        <h1 class="text-4xl font-semibold text-gray-800 mb-6 text-center">Manage Invoices</h1>

        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-100 text-gray-600">
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">User</th>
                    <th class="px-4 py-2 text-left">Customer</th>
                    <th class="px-4 py-2 text-left">Product/Service</th>
                    <th class="px-4 py-2 text-left">Discount (%)</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $invoice): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2"><?= $invoice['id'] ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($invoice['username']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($invoice['customer_name']) ?></td>
                        <td class="px-4 py-2"><?= $invoice['product'] ?? 'Service ID: ' . $invoice['service_id'] ?></td>
                        <td class="px-4 py-2"><?= $invoice['discount'] ?>%</td>
                        <td class="px-4 py-2">
                            <span class="<?= $invoice['status'] == 'approved' ? 'text-green-500' : ($invoice['status'] == 'denied' ? 'text-red-500' : 'text-yellow-500') ?>"><?= ucfirst($invoice['status']) ?></span>
                        </td>
                        <td class="px-4 py-2">
                            <?php if ($invoice['status'] == 'pending'): ?>
                                <?php if ($invoice['discount'] > 7): ?>
                                    <form method="POST" class="flex space-x-2">
                                        <input type="hidden" name="invoice_id" value="<?= $invoice['id'] ?>">
                                        <button type="submit" name="action" value="approve" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Approve</button>
                                        <button type="submit" name="action" value="deny" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-700">Deny</button>
                                    </form>
                                <?php else: ?>
                                    <?php
                                    $autoApproveStmt = $conn->prepare("UPDATE invoices SET status = 'approved', approved_at = NOW() WHERE id = :invoice_id");
                                    $autoApproveStmt->execute(['invoice_id' => $invoice['id']]);
                                    ?>
                                    <span class="text-green-500">Auto-Approved</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-gray-400"><?= ucfirst($invoice['status']) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
