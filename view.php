<?php
// view.php
include 'db_connect.php';
include 'header.php';
include 'footer.php';

$role_filter = isset($_GET['role']) ? $_GET['role'] : 'all'; // Get role filter from query parameter
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    <style>
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
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
        .delete-btn {
            background-color: #dc3545;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>View Users</h1>
        <form method="GET" action="">
            <label for="role">Filter by Role:</label>
            <select name="role" id="role" onchange="this.form.submit()">
                <option value="all" <?php if ($role_filter === 'all') echo 'selected'; ?>>All</option>
                <option value="Admin" <?php if ($role_filter === 'Admin') echo 'selected'; ?>>Admin</option>
                <option value="Employee" <?php if ($role_filter === 'Employee') echo 'selected'; ?>>Employee</option>
                <option value="Defendant" <?php if ($role_filter === 'Defendant') echo 'selected'; ?>>Defendant</option>
                <option value="Cosigner" <?php if ($role_filter === 'Cosigner') echo 'selected'; ?>>Cosigner</option>
            </select>
        </form>
        <br>
        <?php
        // Build the query with optional role filtering
        $query = "SELECT Users.*, Employees.department_id, Employees.employee_id 
                  FROM Users 
                  LEFT JOIN Employees ON Users.user_id = Employees.user_id";
        if ($role_filter !== 'all') {
            $query .= " WHERE Users.role = '" . mysqli_real_escape_string($connection, $role_filter) . "'";
        }
        $result = mysqli_query($connection, $query);

        if ($result && mysqli_num_rows($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Department ID</th>
                        <th>Employee ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['user_id']; ?></td>
                            <td><?php echo $row['first_name']; ?></td>
                            <td><?php echo $row['middle_name']; ?></td>
                            <td><?php echo $row['last_name']; ?></td>
                            <td><?php echo $row['role']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['phone_number']; ?></td>
                            <td><?php echo $row['department_id'] ?? 'N/A'; ?></td>
                            <td><?php echo $row['employee_id'] ?? 'N/A'; ?></td>
                            <td>
                                <a href="edit.php?user_id=<?php echo $row['user_id']; ?>" class="btn">Edit</a>
                                <a href="delete.php?user_id=<?php echo $row['user_id']; ?>" class="btn" style="background-color: #dc3545;">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>

        <?php mysqli_close($connection); ?>
        
    </div>
</body>
</html>
