<?php

require '../vendor/autoload.php';

use Rakit\Validation\Validator;
use Rakit\Validation\ValidationException;

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

$validator = new Validator();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check the 'honeypot' field to prevent spam attack
    if (!empty($_POST['honeypot'])) {
        // Honeypot field is filled, treat it as spam
        echo 'Sorry, this form submission is not allowed.';
        exit;
    }

    // Define validation rules
    $validation = $validator->make($_POST, [
        'name' => 'required|min:2|max:255',
        'firstname' => 'required|min:2|max:255',
        'email' => 'required|email',
        'description' => 'required|min:2|max:1000',
        'file' => 'uploaded_file:0,2097152|mimes:jpg,png,gif',
    ]);

    try {
        // Perform validation
        $validation->validate();
    } catch (ValidationException $e) {
        // Validation failed, retrieve the errors
        $errors = $e->getMessages();
        
        if ($errors->has('name')) {
            echo '<script>alert("Please enter a valid name (2-255 characters).");</script>';
        }

        if ($errors->has('firstname')) {
            echo '<script>alert("Please enter a valid first name (2-255 characters).");</script>';
        }

        if ($errors->has('email')) {
            echo '<script>alert("Please enter a valid email address.");</script>';
        }

        if ($errors->has('description')) {
            echo '<script>alert("Please enter a valid description (2-1000 characters).");</script>';
        }

        if ($errors->has('file')) {
            echo '<script>alert("Invalid file. Only JPG, PNG, and GIF files up to 2MB are allowed.");</script>';
        }

        exit;
    }

    // Retrieve the validated data
    $data = $validation->getValidData();

    $name = $data['name'];
    $firstname = $data['firstname'];
    $email = $data['email'];
    $description = $data['description'];

    // Validate and process the file upload (optional field)
    if (!empty($_FILES['file']['name'])) {
        try {
            // Perform file validation
            $fileValidation = $validator->make($_FILES, [
                'file' => 'uploaded_file:0,2097152|mimes:jpg,png,gif',
            ]);
    
            $fileValidation->validate();
        } catch (ValidationException $e) {
            // File validation failed, retrieve the errors
            $fileErrors = $e->getMessages();
            
            if ($fileErrors->has('file')) {
                echo '<script>alert("Invalid file. Only JPG, PNG, and GIF files up to 2MB are allowed.");</script>';
            }
            
            exit;
        }

        // File is valid, process it as needed
        $file = $_FILES['file'];
        $fileTempName = $file['tmp_name'];
        $fileName = $file['name'];
        move_uploaded_file($fileTempName, '../uploads/' . $fileName);
    } else {
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

    // Send confirmation email
    $transport = new Swift_SmtpTransport('smtp-relay.sendinblue.com', 587);
    $transport->setUsername('thomas.moerman.7@gmail.com');
    $transport->setPassword('Z5hMxygDYOGzEAn3');

    $mailer = new Swift_Mailer($transport);

    $message = new Swift_Message('Confirmation Email');
    $message->setFrom($email);
    $message->setTo(['thomas.moerman.7@gmail.com' => 'Recipient']); 
    $message->setBody('Thank you for contacting us! We have received your message.');

    $result = $mailer->send($message);

    // Respond to the user
    include '../views/response.php';
    
    exit;
}
?>