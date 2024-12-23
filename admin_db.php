<?php
/**
 * admin_db.php
 * Allows the admin to manage tables (list, create, view, delete).
 */

// Start the session before any HTML is sent (avoids "headers already sent" warnings)
session_start();

// Include DB connection and any header (making sure these do not produce output themselves)
include 'db_connect.php';
include 'header.php';

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_code'])) {
    header('Location: admin_login.php');
    exit;
}

// Default message
$message = "";

// Fetch all tables in the database
$query = "SHOW TABLES";
$result = mysqli_query($connection, $query);
if (!$result) {
    // In case of an error, set a message (and possibly log it)
    $message = "Error fetching tables: " . mysqli_error($connection);
}

// Handle table creation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_table'])) {
    $tableName = "";
    $tableColumns = "";

    if (isset($_POST['table_name'])) {
        $tableName = mysqli_real_escape_string($connection, $_POST['table_name']);
    }
    if (isset($_POST['table_columns'])) {
        $tableColumns = mysqli_real_escape_string($connection, $_POST['table_columns']);
    }

    if (!empty($tableName) && !empty($tableColumns)) {
        $createQuery = "CREATE TABLE `$tableName` ($tableColumns)";
        $createResult = mysqli_query($connection, $createQuery);
        if ($createResult) {
            $message = "Table '$tableName' created successfully!";
        } else {
            $message = "Error creating table: " . mysqli_error($connection);
        }
    } else {
        $message = "Please provide both table name and columns.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Database Management</title>
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
        .card {
            margin-bottom: 20px;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 5px 10px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            text-decoration: none;
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

        /* Form and messages */
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        p {
            margin: 10px 0;
        }

        /* Table styling */
        .table-wrapper {
            width: 100%;
            overflow-x: auto; /* Allows horizontal scroll if the table is too wide */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed; /* Ensures columns don't exceed container width */
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
            word-wrap: break-word; /* Force wrapping of overly long text */
        }
        th {
            background-color: #f4f4f4;
        }

        /* Dynamic container for AJAX content */
        .dynamic-container {
            display: none; 
        }
        .dynamic-td {
            background-color: #f9f9f9;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Responsive adjustments */
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
    <script>
        async function toggleTableDetails(tableName) {
            // The hidden row
            const dynamicRow = document.getElementById(`details-${tableName}`);
            // The single cell inside that row
            const dynamicCell = dynamicRow.querySelector('.dynamic-td');

            if (dynamicRow.style.display === "none" || dynamicRow.style.display === "") {
                try {
                    const response = await fetch(`view_table.php?table=${encodeURIComponent(tableName)}`);
                    if (!response.ok) {
                        throw new Error('Failed to fetch data. Server responded with ' + response.status);
                    }
                    const data = await response.text();
                    // Insert the fetched HTML into the cell
                    dynamicCell.innerHTML = data;
                    // Reveal the row
                    dynamicRow.style.display = "table-row";
                } catch (error) {
                    console.error('Error fetching table details:', error);
                    dynamicCell.innerHTML = `<p style="color: red;">Error: ${error.message}</p>`;
                    dynamicRow.style.display = "table-row";
                }
            } else {
                // Hide the row again
                dynamicRow.style.display = "none";
                // Optionally clear the old data
                dynamicCell.innerHTML = "";
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="card">
            <a href="admin_dashboard.php" class="btn">Back to Dashboard</a>
        </div>

        <h1>Database Management</h1>
        <?php if (!empty($message)): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <h2>Existing Tables</h2>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Table Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_row($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row[0]); ?></td>
                                <td>
                                    <!-- "View" triggers an AJAX call to view_table.php -->
                                    <button class="btn" onclick="toggleTableDetails('<?php echo htmlspecialchars($row[0]); ?>')">
                                        View
                                    </button>
                                    <!-- Normal link to delete the table -->
                                    <a href="delete_table.php?table=<?php echo urlencode($row[0]); ?>"
                                       class="btn btn-delete">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                            <!-- This row is hidden by default; it holds the fetched table details. -->
                            <tr id="details-<?php echo htmlspecialchars($row[0]); ?>" class="dynamic-container">
                                <td class="dynamic-td" colspan="2"></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No tables found.</p>
        <?php endif; ?>

        <h2>Create New Table</h2>
        <form method="POST">
            <label for="table_name">Table Name:</label>
            <input type="text" id="table_name" name="table_name" required>
            
            <label for="table_columns">Columns (e.g., <i>id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(50)</i>):</label>
            <textarea id="table_columns" name="table_columns" rows="4" required></textarea>
            
            <button type="submit" name="create_table" class="btn">Create Table</button>
        </form>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
