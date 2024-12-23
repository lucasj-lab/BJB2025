<?php
// delete.php
include 'db_connect.php';

if (!isset($_GET['user_id'])) {
    die("<div class='message error'>User ID is required.</div>");
}

$user_id = intval($_GET['user_id']);

// Check if the user exists
$query = "SELECT * FROM Users WHERE user_id = $user_id";
$result = mysqli_query($connection, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    die("<div class='message error'>User not found.</div>");
}

// Delete from Employees table if applicable
$employee_delete_query = "DELETE FROM Employees WHERE user_id = $user_id";
mysqli_query($connection, $employee_delete_query);

// Delete from Users table
$user_delete_query = "DELETE FROM Users WHERE user_id = $user_id";
if (mysqli_query($connection, $user_delete_query)) {
    echo "<div class='message success'>User successfully deleted!</div>";
} else {
    echo "<div class='message error'>Error deleting user: " . mysqli_error($connection) . "</div>";
}

mysqli_close($connection);
?>
