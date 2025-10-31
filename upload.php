<?php
header('Content-Type: application/json');

// Simple security check: Ensure this script is accessed via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Check if a file was uploaded
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400); // Bad Request
    $errorMessage = 'No file uploaded or an error occurred during upload.';
    if (isset($_FILES['image']['error'])) {
        // Provide a more specific error message if available
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
            UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
        ];
        $errorCode = $_FILES['image']['error'];
        $errorMessage = $uploadErrors[$errorCode] ?? 'An unknown upload error occurred.';
    }
    echo json_encode(['success' => false, 'message' => $errorMessage]);
    exit;
}

// --- Configuration ---
// The directory where uploaded images will be stored.
// It must be writable by the web server.
$uploadDir = 'images/uploads/';
// The public URL path to the uploads directory.
$uploadUrlPath = '/images/uploads/';

// --- Directory and Path Setup ---
// Get the document root from the server configuration.
$documentRoot = $_SERVER['DOCUMENT_ROOT'];
// Create the full, absolute path for the upload directory.
$absoluteUploadDir = $documentRoot . DIRECTORY_SEPARATOR . $uploadDir;

// Create the upload directory if it doesn't exist.
if (!is_dir($absoluteUploadDir)) {
    if (!mkdir($absoluteUploadDir, 0755, true)) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Failed to create the upload directory.']);
        exit;
    }
}

// --- File Validation ---
$file = $_FILES['image'];
$fileTmpName = $file['tmp_name'];
$fileName = $file['name'];
$fileSize = $file['size'];

// 1. Check file size (e.g., max 5MB)
$maxFileSize = 5 * 1024 * 1024;
if ($fileSize > $maxFileSize) {
    http_response_code(413); // Payload Too Large
    echo json_encode(['success' => false, 'message' => 'File is too large. Maximum size is 5MB.']);
    exit;
}

// 2. Check if it's a valid image
$imageInfo = getimagesize($fileTmpName);
if ($imageInfo === false) {
    http_response_code(415); // Unsupported Media Type
    echo json_encode(['success' => false, 'message' => 'Invalid image file.']);
    exit;
}

// 3. Check MIME type against a whitelist
$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($imageInfo['mime'], $allowedMimeTypes)) {
    http_response_code(415);
    echo json_encode(['success' => false, 'message' => 'Unsupported image type. Only JPG, PNG, GIF, and WEBP are allowed.']);
    exit;
}

// --- Generate a New, Secure Filename ---
// Get the file extension from the original filename.
$fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
// Generate a unique filename to prevent overwriting files and for security.
$newFileName = uniqid('img_', true) . '.' . strtolower($fileExtension);
// Construct the full path for the new file.
$newFilePath = $absoluteUploadDir . $newFileName;

// --- Move the File ---
if (move_uploaded_file($fileTmpName, $newFilePath)) {
    // Determine the protocol (http or https)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    // Get the server host name
    $host = $_SERVER['HTTP_HOST'];
    // Construct the full public URL of the uploaded image
    $fileUrl = $protocol . $host . $uploadUrlPath . $newFileName;

    // --- Success ---
    echo json_encode([
        'success' => true,
        'message' => 'Image uploaded successfully.',
        'url' => $fileUrl
    ]);
} else {
    // --- Failure ---
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to move the uploaded file.']);
}

?>
