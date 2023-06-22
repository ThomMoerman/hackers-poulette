<?php
require './vendor/autoload.php';

use Rakit\Validation\Validator;

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

    // Perform validation
    $validation->validate();

    if ($validation->fails()) {
        $errors = $validation->errors();
        $errorString = '';
        foreach ($errors->firstOfAll() as $field => $error) {
            $errorString .= "$field: $error<br>";
        }
        echo '<h2><strong> Form validation error : </strong></h2>';
        echo "<h4>$errorString</h4>";
        exit;
    }

    $recaptchaSecretKey = '6LfRT70mAAAAAKl8fS4l83CNb1FWYRPNfIOQ35VF';
$recaptcha = new \ReCaptcha\ReCaptcha($recaptchaSecretKey);
$recaptchaResponse = $_POST['g-recaptcha-response'];
$remoteIp = $_SERVER['REMOTE_ADDR'];

$recaptchaResult = $recaptcha->verify($recaptchaResponse, $remoteIp);

if (!$recaptchaResult->isSuccess()) {
    // La vérification reCAPTCHA a échoué, gérer l'erreur ici
    $errors = $recaptchaResult->getErrorCodes();
    echo 'reCAPTCHA verification failed. Error codes: ' . implode(', ', $errors);
    exit;
}
    // Validation passes, retrieve the validated data
    $data = $validation->getValidData();

    $name = $data['name'];
    $firstname = $data['firstname'];
    $email = $data['email'];
    $description = $data['description'];

    // Validate and process the file upload (optional field)
    if (!empty($_FILES['file']['name'])) {
        $fileValidation = $validator->make($_FILES, [
            'file' => 'uploaded_file:0,2097152|mimes:jpg,png,gif',
        ]);

        $fileValidation->validate();

        if ($fileValidation->fails()) {
            // File validation failed, retrieve the errors
            $fileErrors = $fileValidation->errors();
            echo "<pre>";
            print_r($fileErrors->firstOfAll());
            echo "</pre>";
            exit;
        }

        // File is valid, process it as needed
        $file = $_FILES['file'];
        $fileTempName = $file['tmp_name'];
        $fileName = $file['name'];
        move_uploaded_file($fileTempName, './uploads/' . $fileName);
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
    $message->setFrom('thomas.moerman.7@gmail.com');
    $message->setTo($email); 
    $message->setBody('Thank you for contacting us! We have received your message.');

    $result = $mailer->send($message);

    // Respond to the user
    include './views/response.php';

    exit;
}
?>
