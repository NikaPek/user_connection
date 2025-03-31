<?php
require 'autoload.php';

use App\Core\DbHelper;
use App\Models\Admin;
use App\Models\RegularUser;
use App\Services\AuthService;

$db = new DbHelper();
$connection = $db->connect();

$db->createUser($connection, "Alice", "alice@example.com", "admin123");
$db->createUser($connection, "Bob", "bob@example.com", "user123");
// Create an Admin user
$admin = new Admin("Alice", "alice@example.com", "admin123");
// Create a Regular User
$user = new RegularUser("Bob", "bob@example.com", "user123");
// Create AuthService
$authService = new AuthService();
// Admin Login
echo $authService->authenticate($admin, "alice@example.com", "admin123") . "<br>";
// Regular User Login
echo $authService->authenticate($user, "bob@example.com", "user123") . "<br>";

$adminUser = $db->getUser($connection, "alice@example.com");
$regularUser = $db->getUser($connection, "bob@example.com");

echo $db->userExists($connection, "alice@example.com") ? "Rastas vartotojas duomenu bazeje ".$adminUser["name"]. "</br>" : "Vartotojas Alice nerastas duomenu bazeje" . "</br>";
echo $db->userExists($connection, "bob@example.com") ? "Rastas vartotojas duomenu bazeje ".$regularUser["name"]. "</br>" : "Vartotojas bob nerastas duomenu bazeje" . "</br>";

// Admin Logout
echo $admin->logout();





?>