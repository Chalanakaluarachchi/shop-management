<?php
include('../config.php');

// Initialize the upload status message
$upload_status = "";

// Handle file upload and insert into the database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['contract_file'])) {
    $file_name = $_FILES['contract_file']['name'];
    $file_tmp = $_FILES['contract_file']['tmp_name'];
    $file_size = $_FILES['contract_file']['size'];
    $file_type = pathinfo($file_name, PATHINFO_EXTENSION);

    // Ensure file is a PDF
    if ($file_type != 'pdf') {
        $upload_status = "Only PDF files are allowed!";
    } else if ($file_size > 10000000) {  // Max size 10MB
        $upload_status = "File size exceeds the 10MB limit.";
    } else {
        $target_dir = "uploads/contracts/";
        $target_file = $target_dir . basename($file_name);

        // Check if the file already exists in the directory
        if (file_exists($target_file)) {
            $upload_status = "This file has already been uploaded.";
        } else {
            // Ensure the directory exists
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);  // Create directory if not exists
            }

            // Move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                // Insert file info into database (file name and path)
                $stmt = $conn->prepare("INSERT INTO contracts (file_name, file_path, created_at) VALUES (?, ?, NOW())");
                $stmt->execute([$file_name, $target_file]);

                $upload_status = "File uploaded and saved to database successfully.";
            } else {
                $upload_status = "Sorry, there was an error uploading your file.";
            }
        }
    }
}

// Fetch the list of uploaded contracts from the database
$contracts = $conn->query("SELECT * FROM contracts")->fetchAll(PDO::FETCH_ASSOC);
?>

<head>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Include jQuery for AJAX, if you want smooth page refreshing and alert display -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- JavaScript to auto-refresh page, show alert, and clear the form -->
    <script>
        $(document).ready(function() {
            // Check if there is a success or error message
            var uploadStatus = "<?php echo $upload_status; ?>";

            // If there's a message, show it as an alert
            if (uploadStatus) {
                alert(uploadStatus);  // Show the alert

                // Clear the file input and reset the form
                $("input[type='file']").val('');

                // Reload the page after 2 seconds to show updated contracts
                setTimeout(function() {
                    location.reload();
                }, 2000);
            }
        });
    </script>
</head>

<!-- HTML Form to upload contract -->
<div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-lg mt-10">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Upload Contract</h2>
    <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <div class="flex flex-col">
            <label for="contract_file" class="text-lg font-medium text-gray-700">Choose Contract File (PDF only)</label>
            <input type="file" name="contract_file" class="border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">Upload</button>
    </form>
</div>

<!-- Table to display list of uploaded contracts -->
<div class="max-w-6xl mx-auto mt-12 bg-white p-8 rounded-lg shadow-lg">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Uploaded Contracts</h2>
    <table class="min-w-full bg-white border border-gray-300 rounded-lg overflow-hidden">
        <thead>
            <tr class="bg-blue-100 text-gray-700 text-left text-sm">
                <th class="py-3 px-6">ID</th>
                <th class="py-3 px-6">File Name</th>
                <th class="py-3 px-6">Download Link</th>
                <th class="py-3 px-6">Uploaded At</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 text-sm">
            <?php foreach ($contracts as $contract): ?>
            <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-150">
                <td class="py-3 px-6"><?= htmlspecialchars($contract['id']) ?></td>
                <td class="py-3 px-6"><?= htmlspecialchars($contract['file_name']) ?></td>
                <td class="py-3 px-6">
                    <a href="<?= htmlspecialchars($contract['file_path']) ?>" class="text-blue-500 hover:text-blue-700 underline" download>Download</a>
                </td>
                <td class="py-3 px-6"><?= htmlspecialchars($contract['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
