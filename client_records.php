<?php
/**
 * client_records.php
 * Shows a list of defendants or cosigners with basic info.
 */
session_start();
include 'db_connect.php';
include 'header.php';

// Optional: Check manager role, etc.

// Example: SELECT from table `clients` or `defendants` or something
$query = "SELECT id, client_type, first_name, last_name FROM clients"; 
$result = mysqli_query($connection, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Records</title>
</head>
<body>
    <div class="container">
        <h1>Client Records</h1>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['client_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No client records found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
if ($result) {
    mysqli_free_result($result);
}
mysqli_close($connection);
?>
