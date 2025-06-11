<?php

include 'autoload.php';
use App\Services\DatabaseService;
use App\Services\PasswordService;

$registered = isset($_POST['fullName']) && isset($_POST['password']) && isset($_POST['confirmPassword']);
$errorMessages = "";
$databaseService = new DatabaseService();
$passwordService = new PasswordService();

if ($registered) {
    $fullName = $_POST['fullName'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Basic validation
    if ($password !== $confirmPassword) {
        $errorMessages = "<p class='text-red-500'>Passwords do not match.</p>";
    } else {
        if ($databaseService->UserExists($fullName)) {
            $errorMessages = "<p class='text-red-500'>User already exists.</p>";
        } else {
            // Create user
            $hashedPassword = $passwordService->GetHashedPassword($password);
            $key = $passwordService->EncryptAes($passwordService->GenerateAesKey(), $password); // Generate AES key for encryption
            if ($databaseService->CreateUser($fullName, $hashedPassword, $key)) { // Assuming key is not needed for registration
                $fullName = null;
                $password = null;
                $confirmPassword = null;
                header('Location: ' . "/login.php", true, 303);
                die();
            } else {
                $errorMessages = "<p class='text-red-500'>Error creating user. Please try again.</p>";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Apply Inter font and basic styling consistent with other pages */
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f2f5; /* Light gray background */
        }
        /* Custom styles for input focus to mimic Material UI outline */
        .input-material:focus {
            outline: none;
            border-color: #2563eb; /* Blue border on focus */
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3); /* Light blue shadow on focus */
        }
    </style>
</head>
<body>
<!-- Main container for the registration card -->
<div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md m-4">
    <?php echo $errorMessages ?>
    <h2 class="text-3xl font-bold text-center mb-6 text-gray-800">Create Account</h2>

    <!-- Registration Form -->
    <form id="registerForm" class="space-y-6" method="POST" action="register.php">
        <!-- Full Name Input Field -->
        <div>
            <label for="fullName" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input
                type="text"
                id="fullName"
                name="fullName"
                required
                class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm input-material"
                placeholder="John Doe"
            >
        </div>

        <!-- Password Input Field -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                required
                class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm input-material"
                placeholder="********"
            >
        </div>

        <!-- Confirm Password Input Field -->
        <div>
            <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <input
                type="password"
                id="confirmPassword"
                name="confirmPassword"
                required
                class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm input-material"
                placeholder="********"
            >
        </div>

        <!-- Submit Button -->
        <div>
            <button
                type="submit"
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out transform hover:scale-105"
            >
                Register
            </button>
        </div>
    </form>
</div>
</body>
</html>