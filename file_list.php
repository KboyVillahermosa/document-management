<?php
include_once('config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css"  rel="stylesheet" />
    <link rel="stylesheet" href="./style/file.css">
    <title>File List</title>
</head>
<body>
<?php include_once('navbar.php'); ?>

<section class="header-sec p-4 sm:ml-64">
    <h2>List of Downloaded Files</h2>

    <ul class="file-list">
        <?php
        // Function to list files
        $files = listFiles($db_connection);

        // Display files
        foreach ($files as $file) {
            echo '<li class="file-item">';
            echo '<div class="file-info">';
            echo '<div class="file-name">' . htmlspecialchars($file['name']) . '</div>';
            echo '<div class="file-category">' . htmlspecialchars($file['category']) . '</div>';
            echo '</div>';
            echo '<div class="file-actions">';
            echo '<a href="' . htmlspecialchars($file['path']) . '" download>Download</a>';
            echo ' | ';
            echo '<a href="#" onclick="openModal(\'' . htmlspecialchars($file['path']) . '\')" data-modal-target="default-modal" data-modal-toggle="default-modal">View</a>';
            echo '</div>';
            echo '</li>';
        }
        ?>
    </ul>

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
