<?php
/**
 * Setup Survey Responses Table - AUTO INITIALIZATION
 */

// Don't require auth for setup - this runs on first installation
require_once '../api/db_config.php';

$message = '';
$error = '';
$debug = '';

try {
    // Check if table already exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'survey_responses'");
    
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $message = "✓ Survey Responses table already exists!";
    } else {
        // Table doesn't exist - create it
        $createTableSQL = "
        CREATE TABLE IF NOT EXISTS survey_responses (
            id INT PRIMARY KEY AUTO_INCREMENT,
            email VARCHAR(255) NOT NULL,
            user_id INT,
            responses_data JSON,
            submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_submitted (submitted_at)
        )
        ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";

        if ($conn->query($createTableSQL) === TRUE) {
            $message = "✓ Survey Responses table created successfully!";
        } else {
            $error = "Error creating table: " . $conn->error;
            $debug = "SQL: " . $createTableSQL;
        }
    }
} catch (Exception $e) {
    $error = "Setup error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Survey Responses</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 py-12 px-4">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">
            <i class="fas fa-database text-indigo-600 mr-2"></i> Setup Survey Responses
        </h1>

        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                <h2 class="text-lg font-semibold text-red-900 mb-2">
                    <i class="fas fa-exclamation-circle mr-2"></i> Error
                </h2>
                <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
                <?php if ($debug): ?>
                    <div class="mt-3 bg-red-100 rounded p-2 text-xs text-red-800 font-mono overflow-auto">
                        <?php echo htmlspecialchars($debug); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif ($message): ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                <h2 class="text-lg font-semibold text-green-900 mb-2">
                    <i class="fas fa-check-circle mr-2"></i> Success
                </h2>
                <p class="text-green-700"><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-blue-900 mb-2">
                <i class="fas fa-info-circle mr-2"></i> About Survey Responses
            </h3>
            <p class="text-sm text-blue-800">
                This table stores all survey responses submitted through the public survey form. 
                Each response includes the email address and the answers provided.
            </p>
        </div>

        <div class="space-y-3">
            <a href="dashboard.php" class="block w-full bg-indigo-600 text-white py-2 px-4 rounded-lg text-center hover:bg-indigo-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
            <a href="responses.php" class="block w-full bg-blue-600 text-white py-2 px-4 rounded-lg text-center hover:bg-blue-700">
                <i class="fas fa-file-lines mr-2"></i> View Responses
            </a>
        </div>

        <p class="text-xs text-gray-500 mt-6 text-center">
            Database: <code class="bg-gray-100 px-2 py-1 rounded"><?php echo htmlspecialchars($dbname ?? 'library_survey'); ?></code>
        </p>
    </div>
</body>
</html>
