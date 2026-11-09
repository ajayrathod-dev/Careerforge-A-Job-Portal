<?php
session_start();
include("connection.php");
include("admin_functions.php");

// Check if user is logged in as admin
if (!isAdminLoggedIn()) {
    header('HTTP/1.0 403 Forbidden');
    exit('Access denied');
}

if (isset($_GET['file']) && isset($_GET['type'])) {
    $file_path = $_GET['file'];
    $file_type = $_GET['type'];
    
    // Security: Validate file path and ensure it's within uploads directory
    $base_upload_dir = 'uploads/portfolios/';
    $real_base_path = realpath($base_upload_dir);
    $real_file_path = realpath($file_path);
    
    // Check if file exists and is within the allowed directory
    if ($real_file_path && strpos($real_file_path, $real_base_path) === 0 && file_exists($file_path)) {
        
        // Get file info
        $file_name = basename($file_path);
        $file_size = filesize($file_path);
        $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        
        // Set appropriate headers based on file type
        switch($file_type) {
            case 'resume':
            case 'portfolio':
                if (in_array($file_extension, ['pdf', 'doc', 'docx'])) {
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . $file_name . '"');
                } else {
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . $file_name . '"');
                }
                break;
            case 'certificate':
                if (in_array($file_extension, ['jpg', 'jpeg', 'png'])) {
                    header('Content-Type: image/' . $file_extension);
                    header('Content-Disposition: inline; filename="' . $file_name . '"');
                } else {
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: inline; filename="' . $file_name . '"');
                }
                break;
            default:
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $file_name . '"');
        }
        
        header('Content-Length: ' . $file_size);
        header('Cache-Control: private');
        header('Pragma: private');
        header('Expires: 0');
        
        // Clear output buffer and send file
        ob_clean();
        flush();
        readfile($file_path);
        exit;
        
    } else {
        // File not found or invalid path
        header('HTTP/1.0 404 Not Found');
        echo 'File not found.';
        exit;
    }
} else {
    header('HTTP/1.0 400 Bad Request');
    echo 'Invalid request.';
    exit;
}
?>