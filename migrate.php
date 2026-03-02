<?php
/**
 * Migration - Add responses_data column to survey_responses
 */

require_once '../api/db_config.php';

$message = '';
$error = '';

try {
    // Check if column already exists
    $result = $conn->query("SHOW COLUMNS FROM survey_responses LIKE 'responses_data'");
    
    if ($result && $result->num_rows > 0) {
        $message = "✓ responses_data column already exists!";
    } else {
        // Add the column
        $alterSQL = "
        ALTER TABLE survey_responses 
        ADD COLUMN responses_data JSON AFTER purpose
        ";

        if ($conn->query($alterSQL)) {
            $message = "✓ responses_data column added successfully!";
        } else {
            $error = "Error adding column: " . $conn->error;
        }
    }
} catch (Exception $e) {
    $error = "Migration error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Migration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 py-12 px-4">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">
            <i class="fas fa-database text-indigo-600 mr-2"></i> Database Migration
        </h1>

        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                <h2 class="text-lg font-semibold text-red-900 mb-2">
                    <i class="fas fa-exclamation-circle mr-2"></i> Error
                </h2>
                <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
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
                <i class="fas fa-info-circle mr-2"></i> Migration
            </h3>
            <p class="text-sm text-blue-800">
                This adds the responses_data column to store question responses as JSON, allowing flexible question management.
            </p>
        </div>

        <div class="space-y-3">
            <a href="../admin/dashboard.php" class="block w-full bg-indigo-600 text-white py-2 px-4 rounded-lg text-center hover:bg-indigo-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Admin
            </a>
            <a href="index.php" class="block w-full bg-blue-600 text-white py-2 px-4 rounded-lg text-center hover:bg-blue-700">
                <i class="fas fa-file-lines mr-2"></i> Back to Survey
            </a>
        </div>

        <p class="text-xs text-gray-500 mt-6 text-center">
            Database: <code class="bg-gray-100 px-2 py-1 rounded"><?php echo htmlspecialchars($dbname ?? 'library_survey'); ?></code>
        </p>
    </div>
</body>
</html>
