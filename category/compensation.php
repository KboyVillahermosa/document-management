<?php
session_start();
require '../config.php';

// Check if user is logged in
if (!isset($_SESSION['login_id'])) {
    header('Location: login.php');
    exit;
}

$category = 'Compensation and Benefits';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $filename = basename($_FILES['file']['name']);
    if (uploadFile($_FILES['file'], $category, $filename, $db_connection)) {
        echo "File uploaded successfully.";
    } else {
        echo "Failed to upload file.";
    }
}

// Fetch user data based on the session login ID
$id = $_SESSION['login_id'];
$get_user = mysqli_query($db_connection, "SELECT * FROM `user` WHERE `id`='$id' OR `google_id`='$id'");

if (mysqli_num_rows($get_user) > 0) {
    $user = mysqli_fetch_assoc($get_user);
} else {
    header('Location: logout.php');
    exit;
}

// List files in the category
$files = listFilesByCategory($db_connection, $category);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../style/file.css">
    <title>Recruitment and Staffing Files</title>
</head>
<body>
    <style>
   
        .recruite-content{
            width: 100%;
        }
    </style>
    <?php include_once ('../navbar.php'); ?>
    <section class="header-sec p-4 sm:ml-64">
        <div class="recruite-header">
            <div class="recruite-content">
            <h1 class="mb-4 text-3xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-4xl dark:text-white uppercase">Employee Relations</h1>
                <ul class="file-list">
                    <?php foreach ($files as $file): ?>
                        <li
                            class="file-item bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                            <div class="file-info text-gray-900 dark:text-white">
                                <div class="file-name text-gray-900 dark:text-white uppercase">
                                    <?php echo htmlspecialchars($file['name']); ?></div>
                                <div class="text-gray-900 dark:text-white">
                                    <?php echo htmlspecialchars($file['category']); ?></div>
                            </div>
                            <div class="file-actions p-3">
                                <a href="../<?php echo htmlspecialchars($file['path']); ?>"
                                    class="text-white bg-gradient-to-r from-purple-500 to-pink-500 hover:bg-gradient-to-l focus:ring-4 focus:outline-none focus:ring-purple-200 dark:focus:ring-purple-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2"
                                    download>Download</a>
                                <a href="#" onclick="openModal('../<?php echo htmlspecialchars($file['path']); ?>')"
                                    data-modal-target="default-modal" data-modal-toggle="default-modal"
                                    class="text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">View</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>


            </div>
        </div>
        <!-- Modal for Viewing Documentation -->
        <div id="viewModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="modal-close" onclick="closeModal()">&times;</span>
                    <h2>View Documentation</h2>
                </div>
                <div class="modal-body">
                    <iframe id="viewFrame" style="width: 100%; height: 400px; border: none;"></iframe>
                </div>
            </div>
        </div>
    </section>

    <script>
        function openModal(path) {
            var modal = document.getElementById('viewModal');
            var viewFrame = document.getElementById('viewFrame');
            viewFrame.src = path;
            modal.style.display = 'block';
        }

        function closeModal() {
            var modal = document.getElementById('viewModal');
            var viewFrame = document.getElementById('viewFrame');
            viewFrame.src = '';
            modal.style.display = 'none';
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>

</html>