<?php
// Start a session if none exists
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
session_regenerate_id(true);

// Database configuration
$servername = "localhost"; // Replace with your server name
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$database = "documentation"; // Replace with your database name

// Create connection
$db_connection = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$db_connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to upload file
if (!function_exists('uploadFile')) {
    function uploadFile($file, $category, $filename, $conn) {
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        $fileType = $file['type'];

        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['ppt', 'pptx', 'pdf', 'doc', 'docx'];

        if (in_array($fileExt, $allowed)) {
            if ($fileError === 0) {
                if ($fileSize <= 10485760) { // 10MB
                    // Create category directory if not exists
                    $uploadDir = 'uploads/' . $category;
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    // Generate unique filename to prevent overwriting
                    $newFileName = uniqid() . '_' . $filename;
                    $fileDestination = $uploadDir . '/' . $newFileName;

                    // Move the file to the appropriate category folder
                    if (move_uploaded_file($fileTmpName, $fileDestination)) {
                        // Insert file details into database
                        $filepath = $fileDestination;
                        $sql = "INSERT INTO files (filename, category, filepath) VALUES (?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sss", $filename, $category, $filepath);

                        if ($stmt->execute()) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

// Function to list files by category
if (!function_exists('listFilesByCategory')) {
    function listFilesByCategory($conn, $category) {
        $sql = "SELECT filename, category, filepath FROM files WHERE category = ? ORDER BY uploaded_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();

        $files = [];
        while ($row = $result->fetch_assoc()) {
            $files[] = [
                'name' => $row['filename'],
                'path' => $row['filepath'],
                'category' => $row['category']
            ];
        }

        return $files;
    }
}
?>
