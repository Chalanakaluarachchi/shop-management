<?php
session_start();
include('../config.php'); // Make sure the path to config.php is correct

// Fetch the current user (logged-in user)
$current_user_id = $_SESSION['user_id'];

// Fetch all activity logs, ordered by timestamp in descending order
$stmt = $conn->prepare("SELECT * FROM activity_logs ORDER BY timestamp DESC");
$stmt->execute();
$activity_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Activity Log</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">

    <!-- Page Header -->
    <h1 class="text-3xl font-bold mb-4">User Activity Log</h1>

    <!-- Table to display Activity Logs -->
    <table class="min-w-full bg-white shadow-md rounded-lg">
        <thead class="bg-gray-200">
            <tr>
                <th class="py-3 px-4 text-left">User</th>
                <th class="py-3 px-4 text-left">Action</th>
                <th class="py-3 px-4 text-left">Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($activity_logs as $log): ?>
                <tr class="border-b">
                    <td class="py-3 px-4">
                        <?php
                        // Display the user name with a green circle if the log is for the current logged-in user
                        $is_current_user = ($log['user_id'] == $current_user_id) ? 'bg-green-500 text-white' : '';
                        ?>
                        <span class="inline-block w-6 h-6 rounded-full <?= $is_current_user ?>"></span>
                        <?= htmlspecialchars($log['user_id']) ?>
                    </td>
                    <td class="py-3 px-4"><?= htmlspecialchars($log['action']) ?></td>
                    <td class="py-3 px-4"><?= htmlspecialchars($log['timestamp']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
