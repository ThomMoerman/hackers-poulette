<?php
// Function to sanitize input data
function sanitize($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate the email address
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to check the file type and size
function validateFile($file)
{
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    if ($file['size'] > $maxSize) {
        return false;
    }

    if (!in_array($file['type'], $allowedTypes)) {
        return false;
    }

    return true;
}

// Initialize variables with empty values
$name = $firstname = $email = $description = '';
$fileError = '';

// Database connection settings
$dbHost = 'localhost';
$dbName = 'hackerspoulette';
$dbUser = 'root';
$dbPass = '';

// Create a PDO instance
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check the 'honeypot' field to prevent spam attack
    if (!empty($_POST['honeypot'])) {
        // Honeypot field is filled, treat it as spam
        echo 'Sorry, this form submission is not allowed.';
        exit;
    }

    // Sanitize and validate the form fields
    $name = sanitize($_POST['name']);
    if (empty($name) || strlen($name) < 2 || strlen($name) > 255) {
        echo 'Please enter a valid name (2-255 characters).';
        exit;
    }

    $firstname = sanitize($_POST['firstname']);
    if (empty($firstname) || strlen($firstname) < 2 || strlen($firstname) > 255) {
        echo 'Please enter a valid first name (2-255 characters).';
        exit;
    }

    $email = sanitize($_POST['email']);
    if (empty($email) || !validateEmail($email)) {
        echo 'Please enter a valid email address.';
        exit;
    }

    $description = sanitize($_POST['description']);
    if (empty($description) || strlen($description) < 2 || strlen($description) > 1000) {
        echo 'Please enter a valid description (2-1000 characters).';
        exit;
    }

    // Validate and process the file upload (optional field)
    if (!empty($_FILES['file']['name'])) {
        if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
            if (validateFile($_FILES['file'])) {
                // File is valid, process it as needed
                $file = $_FILES['file'];
                $fileTempName = $file['tmp_name'];
                $fileName = $file['name'];
                move_uploaded_file($fileTempName, 'uploads/' . $fileName);
            } else {
                $fileError = 'Invalid file. Only JPG, PNG, and GIF files up to 2MB are allowed.';
            }
        } else {
            $fileError = 'Error uploading the file.';
        }
    } else{
        $fileName = NULL;
    }

    // Prepare and execute the SQL statement to insert the form data into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO contact_forms (name, firstname, email, description, file) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $firstname, $email, $description, $fileName]);
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        exit;
    }

    // Respond to the user
    echo 'Thank you for contacting us! We will get back to you soon.';
    echo '<br>';
    echo '<a href="index.php">Retourner au formulaire</a>';
    exit;
}
?>