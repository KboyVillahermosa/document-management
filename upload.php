<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['login_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']) && isset($_POST['category']) && isset($_POST['filename'])) {
    $file = $_FILES['file'];
    $category = $_POST['category'];
    $filename = $_POST['filename'];

    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['ppt', 'pptx', 'pdf', 'doc', 'docx'];

    if (in_array($fileExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize <= 10485760) { // 10MB
                // Adjust the upload directory path
                $uploadDir = '../uploads/' . $category;
                
                // Create the directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Generate a unique filename to avoid overwriting
                $uniqueFilename = uniqid($filename . '_', true) . '.' . $fileExt;

                // Move the file to the appropriate category folder
                $fileDestination = $uploadDir . '/' . $uniqueFilename;
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    // Insert the file info into the database
                    $insert_query = "INSERT INTO files (filename, category, filepath, uploaded_at) 
                                    VALUES (?, ?, ?, NOW())";
                    $stmt = $db_connection->prepare($insert_query);
                    $stmt->bind_param("sss", $filename, $category, $fileDestination);
                    
                    if ($stmt->execute()) {
                        echo 'File uploaded and categorized successfully!';
                    } else {
                        echo 'Failed to upload file to database.';
                    }
                } else {
                    echo 'Failed to upload file.';
                }
            } else {
                echo 'Your file is too big!';
            }
        } else {
            echo 'There was an error uploading your file!';
        }
    } else {
        echo 'You cannot upload files of this type!';
    }
}
?>
