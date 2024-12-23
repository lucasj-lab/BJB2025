<?php
/**
 * view_table.php
 * Returns HTML with the contents of a specified table (or an error message).
 */

// Start the session before any output
session_start();

// Include the DB connection (no echo/print to avoid "headers already sent")
include 'db_connect.php';

// If not logged in as admin, just return an error message (no redirects!)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_code'])) {
    echo "<p style='color: red;'>Error: You are not authorized. Please log in.</p>";
    exit;
}

// Check if a table name was provided
if (!isset($_GET['table']) || empty($_GET['table'])) {
    echo "<p style='color: red;'>Error: No table specified.</p>";
    exit;
}

// Sanitize table name to prevent SQL injection
$table_name = mysqli_real_escape_string($connection, $_GET['table']);

// Attempt to describe the table (to get column names)
$describe_query = "DESCRIBE `$table_name`";
$describe_result = mysqli_query($connection, $describe_query);

if (!$describe_result) {
    echo "<p style='color: red;'>Error describing table '" . htmlspecialchars($table_name) . "': "
         . htmlspecialchars(mysqli_error($connection)) . "</p>";
    exit;
}

// Collect column names
$columns = [];
while ($col = mysqli_fetch_assoc($describe_result)) {
    $columns[] = $col['Field'];
}

// Fetch all data from this table
$data_query = "SELECT * FROM `$table_name`";
$data_result = mysqli_query($connection, $data_query);

if (!$data_result) {
    echo "<p style='color: red;'>Error selecting data from '" . htmlspecialchars($table_name) . "': "
         . htmlspecialchars(mysqli_error($connection)) . "</p>";
    exit;
}

// Optional: define error/success messages if you plan to use them
$error_message = "";
$success_message = "";

/**
 * If you have any POST actions here (like "Reset Password"), you can process them.
 * For example:
 * if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
 *     // do something here...
 * }
 */

// Free up the describe result if you want
mysqli_free_result($describe_result);
// Do NOT free $data_result yet if we want to display it in HTML below.

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Table: <?php echo htmlspecialchars($table_name); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin-top: 0;
            font-size: 1.5em;
        }
        .btn {
            display: inline-block;
            padding: 8px 12px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-delete {
            background-color: #dc3545;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .card {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            table {
                font-size: 0.9em;
            }
            .btn {
                padding: 6px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>View Table: <?php echo htmlspecialchars($table_name); ?></h1>

        <?php if (!empty($error_message)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php elseif (!empty($success_message)): ?>
            <p style="color: green;"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <?php foreach ($columns as $column): ?>
                        <th><?php echo htmlspecialchars($column); ?></th>
                    <?php endforeach; ?>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($data_result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($data_result)): ?>
                        <tr>
                            <?php foreach ($columns as $column): ?>
                                <td><?php echo htmlspecialchars($row[$column]); ?></td>
                            <?php endforeach; ?>
                            <td>
                                <!-- Example form to do some row-based action -->
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" 
                                           value="<?php echo isset($row['user_id']) 
                                               ? htmlspecialchars($row['user_id']) 
                                               : ''; ?>">
                                    <button type="submit" name="reset_password" class="btn">
                                        Reset Password
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?php echo count($columns) + 1; ?>">
                            No data found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
// Now that we've displayed the table in HTML, we can free the data result
mysqli_free_result($data_result);

// Close the DB connection
mysqli_close($connection);
?>
