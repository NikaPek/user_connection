<?php
include 'autoload.php';

$errorMessage = "";

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Create an instance of the AuthService
    $authService = new \App\Services\AuthService();
    $databaseService = new \App\Services\DatabaseService();

    // Attempt to authenticate the user
    if ($databaseService->LoginUser($email, $password)) {
        // Redirect to the dashboard or home page on successful login
        header('Location: /Slaptazodziai.php?user='.$email, true, 303);
        exit();
    } else {
        // Handle login failure (e.g., show an error message)
        $errorMessage = "Invalid email or password.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Apply Inter font and basic styling */
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
<!-- Main container for the login card -->
<div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm m-4">
    <?php echo $errorMessage ?>
    <h2 class="text-3xl font-bold text-center mb-6 text-gray-800">Login</h2>

    <!-- Login Form -->
    <form id="loginForm" class="space-y-6" method="POST" action="login.php">
        <!-- Email Input Field -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
            <input
                type="email"
                id="email"
                name="email"
                required
                class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm input-material"
                placeholder="you@example.com"
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

        <!-- Remember Me and Forgot Password -->
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input
                    id="remember-me"
                    name="remember-me"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                >
                <label for="remember-me" class="ml-2 block text-sm text-gray-900">Remember me</label>
            </div>
            <div class="text-sm">
                <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Forgot your password?</a>
            </div>
        </div>

        <!-- Submit Button -->
        <div>
            <button
                type="submit"
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out transform hover:scale-105"
            >
                Log in
            </button>
        </div>
    </form>

    <!-- Sign Up Link -->
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
            <a href="/register.php" class="font-medium text-blue-600 hover:text-blue-500">Register</a>
        </p>
    </div>
</div>
</body>
</html>