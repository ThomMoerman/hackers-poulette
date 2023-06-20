<?php
 include '../backend/functions.php'
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Support Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-neutral-50 font-mono">
    <?php
        include '../views/header.php'
    ?>
    <div class="text-center">
    <h2 class="text-4xl my-6">Contact Support</h2>
    <?php
        include 'form.php'
    ?>
    <script src="../backend/validation.js"></script>
</body>
</html>