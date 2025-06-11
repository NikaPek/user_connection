<?php
include 'autoload.php';

$databaseService = new \App\Services\DatabaseService();
$passwordService = new \App\Services\PasswordService();

if (isset($_POST['newUrl']) && isset($_POST['newPassword'])) {
    $newUrl = $_POST['newUrl'];
    $newEmail = $_POST['newEmail'];
    $newPassword = $_POST['newPassword'];
    $createdOn = date('Y-m-d H:i:s');


    // Insert new user data into the database
    if ($databaseService->CreateUserPassword($_GET['user'], $newUrl, $newPassword)) {
        // Redirect to the same page to refresh the user data
        header('Location: /Slaptazodziai.php?user=' . urlencode($_GET['user']), true, 303);
        exit();
    } else {
        // Handle error if insertion fails
        echo "<p class='text-red-500'>Error adding new user password. Please try again.</p>";
    }
}

function generateUserTableHtml(array $users): string
{
    // Start buffering output to capture the HTML
    global $passwordService;
    ob_start();
    ?>
    <!-- Main container for the table card -->
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-4xl m-4 overflow-x-auto">
        <h2 class="text-3xl font-bold text-center mb-6 text-gray-800">User Data</h2>

        <!-- Responsive Table Container -->
        <!-- Use overflow-x-auto to make the table scroll horizontally on small screens -->
        <div class="overflow-x-auto">
            <form id="addUserForm" method="post" action="/Slaptazodziai.php?user=<?php echo $_GET['user'] ?>">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg">
                            <input type="text" id="newUrl" placeholder="New Url" name="newUrl"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="text" id="newEmail" name="newEmail" placeholder="New Email" disabled
                                   value="<?php echo $_GET['user'] ?>"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="text" id="newPassword" name="newPassword" placeholder="New Password"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg">
                            <input type="text" id="newDate" name="newDate" placeholder="New Date"
                                   value="<?php echo date('Y-m-d H:i:s') ?>" disabled
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg">
                            <input type="submit" id="addNewUserPassword" value="Add"
                                    class="w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        </th>
                    </tr>
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg">
                            URL
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Password
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg">
                            Date
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg">

                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    // Loop through the provided user data to generate table rows
                    foreach ($users as $user)
                    {
                        // Default values for placeholder image if not provided
                        $avatarText = isset($user['avatar_text']) ? htmlspecialchars($user['avatar_text']) : substr(htmlspecialchars($user['url']), 0, 2);
                        $avatarBgColor = isset($user['avatar_bg_color']) ? htmlspecialchars($user['avatar_bg_color']) : 'CCCCCC';
                        $avatarTextColor = isset($user['avatar_text_color']) ? htmlspecialchars($user['avatar_text_color']) : '000000';
                        $decodedPassword = $passwordService->DecryptAes($user['password'], $user['key']);

                        // Determine status badge colors
                        $statusClass = 'bg-green-100 text-green-800';
                        ?>
                        <tr class="table-row-hover">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full"
                                             src="https://placehold.co/40x40/<?php echo $avatarBgColor; ?>/<?php echo $avatarTextColor; ?>?text=<?php echo $avatarText; ?>"
                                             alt="<?php echo htmlspecialchars($user['name']); ?> Avatar">
                                    </div>
                                    <div class="ml-4">
                                        <div
                                            class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['url']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($user['name']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $decodedPassword; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($user['createdon']); ?>
                            </span>
                            </td>
                            <td></td>
                        </tr>
                    <?php } // End of foreach loop
                    ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    <?php
    // Get the buffered HTML content and clean the buffer
    return ob_get_clean();
}

$userPasswords = $databaseService->GetUserPasswords($_GET['user']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Page</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Apply Inter font and basic styling from the login page */
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5; /* Light gray background */
        }

        /* Flexbox for the main layout to arrange content and navigation */
        .main-layout {
            display: flex;
            min-height: 100vh; /* Ensure it takes at least the full viewport height */
            padding: 1rem; /* Add some padding around the entire layout */
            gap: 1rem; /* Create space between the main content and navigation */
            flex-direction: column; /* Default to a column layout on small screens (mobile-first) */
        }

        /* Media query for medium screens and up (e.g., tablets and desktops) */
        @media (min-width: 768px) { /* Tailwind's 'md' breakpoint */
            .main-layout {
                flex-direction: row; /* Switch to a row layout on larger screens */
            }
        }
    </style>
</head>
<body>
<!-- Main container for the entire page layout -->
<div class="main-layout">
    <!-- Main Content Area -->
    <!-- This section will grow to take up available space -->
    <main class="flex-grow bg-white p-8 rounded-lg shadow-lg">
        <?php echo generateUserTableHtml($userPasswords); ?>
    </main>

    <!-- Navigation Menu -->
    <!-- This section will be on the right and has a fixed width on larger screens -->
    <nav class="bg-white p-6 rounded-lg shadow-lg w-full md:w-64 flex-shrink-0">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Navigation</h3>
        <ul class="space-y-3">
            <!-- Navigation links with hover effects -->
            <li><a href="/Slaptazodziai.php" class="block text-blue-600 hover:text-blue-800 font-medium py-2 px-3 rounded-md transition duration-150 ease-in-out hover:bg-blue-50">Slaptažodžiai</a></li>
            <li><a href="/login.php" class="block text-blue-600 hover:text-blue-800 font-medium py-2 px-3 rounded-md transition duration-150 ease-in-out hover:bg-blue-50">Atsijungti</a></li>
        </ul>
    </nav>
</div>
</body>
</html>
