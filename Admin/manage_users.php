<?php
include('../config.php'); // Make sure the path to config.php is correct

// Fetch all users using PDO
$stmt = $conn->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all users as an associative array

// Delete user
if (isset($_GET['delete_id'])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bindParam(1, $_GET['delete_id'], PDO::PARAM_INT);
    $stmt->execute();
    header("Location: manage_users.php"); // Refresh the page after deletion
    exit;
}

// Update user information
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id = $_POST['id']; // User ID to update
    $discount_limit = $_POST['discount_limit']; // New discount limit
    $role = $_POST['role']; // New role (either 'admin' or 'user')

    // Prepare the SQL query to update user details
    $stmt = $conn->prepare("UPDATE users SET discount_limit = ?, role = ? WHERE id = ?");
    $stmt->bindParam(1, $discount_limit, PDO::PARAM_INT);
    $stmt->bindParam(2, $role, PDO::PARAM_STR);
    $stmt->bindParam(3, $id, PDO::PARAM_INT);

    // Execute the query
    if ($stmt->execute()) {
        header("Location: manage_users.php"); // Redirect after updating
        exit;
    } else {
        echo "Error updating user.";
    }
}

// Add new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $full_name = $_POST['full_name'];
    $mobile_number = $_POST['mobile_number'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $role = $_POST['role'];

    // Prepare the SQL query to insert a new user
    $stmt = $conn->prepare("INSERT INTO users (full_name, mobile_number, username, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bindParam(1, $full_name, PDO::PARAM_STR);
    $stmt->bindParam(2, $mobile_number, PDO::PARAM_STR);
    $stmt->bindParam(3, $username, PDO::PARAM_STR);
    $stmt->bindParam(4, $password, PDO::PARAM_STR);
    $stmt->bindParam(5, $role, PDO::PARAM_STR);

    // Execute the query
    if ($stmt->execute()) {
        header("Location: manage_users.php"); // Redirect after adding
        exit;
    } else {
        echo "Error adding new user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">

    <!-- Page Header -->
    <h1 class="text-3xl font-bold mb-4">Manage Users</h1>

    <!-- Add New User Form -->
    <form method="POST" class="bg-white p-6 shadow-md rounded-lg mb-6">
        <h2 class="text-2xl font-semibold mb-4">Add New User</h2>
        
        <div class="mb-4">
            <label for="full_name" class="block text-gray-700">Full Name</label>
            <input type="text" name="full_name" id="full_name" class="border px-4 py-2 w-full" required>
        </div>

        <div class="mb-4">
            <label for="mobile_number" class="block text-gray-700">Mobile Number</label>
            <input type="text" name="mobile_number" id="mobile_number" class="border px-4 py-2 w-full" required>
        </div>

        <div class="mb-4">
            <label for="username" class="block text-gray-700">Username</label>
            <input type="text" name="username" id="username" class="border px-4 py-2 w-full" required>
        </div>

        <div class="mb-4">
            <label for="password" class="block text-gray-700">Password</label>
            <input type="password" name="password" id="password" class="border px-4 py-2 w-full" required>
        </div>

        <div class="mb-4">
            <label for="role" class="block text-gray-700">Role</label>
            <select name="role" id="role" class="border px-4 py-2 w-full" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <button type="submit" name="add_user" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Add User</button>
    </form>

    <!-- Users Table -->
    <table class="min-w-full bg-white shadow-md rounded-lg">
        <thead class="bg-gray-200">
            <tr>
                <th class="py-3 px-4 text-left">Full Name</th>
                <th class="py-3 px-4 text-left">Mobile Number</th>
                <th class="py-3 px-4 text-left">Username</th>
                <th class="py-3 px-4 text-left">Discount Limit</th>
                <th class="py-3 px-4 text-left">Role</th>
                <th class="py-3 px-4 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr class="border-b">
                    <td class="py-3 px-4"><?= htmlspecialchars($user['full_name']) ?></td>
                    <td class="py-3 px-4"><?= htmlspecialchars($user['mobile_number']) ?></td>
                    <td class="py-3 px-4"><?= htmlspecialchars($user['username']) ?></td>
                    <td class="py-3 px-4"><?= htmlspecialchars($user['discount_limit']) ?>%</td>
                    <td class="py-3 px-4"><?= ucfirst($user['role']) ?></td>
                    <td class="py-3 px-4 flex items-center space-x-4">
                        <!-- Delete Action -->
                        <a href="manage_users.php?delete_id=<?= $user['id'] ?>" class="text-red-500 hover:text-red-700">Delete</a>

                        <!-- Update Action Form -->
                        <form method="POST" class="flex items-center space-x-2">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <input type="number" name="discount_limit" value="<?= $user['discount_limit'] ?>" class="border px-2 py-1 rounded" min="0" step="0.01">
                            
                            <!-- Role Select Dropdown -->
                            <select name="role" class="border px-2 py-1 rounded">
                                <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>

                            <!-- Submit Button -->
                            <button type="submit" name="update_user" class="text-blue-500 hover:text-blue-700">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
