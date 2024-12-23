<?php
// edit.php
include 'db_connect.php';
include 'header.php';

if (!isset($_GET['user_id'])) {
    die("<div class='message error'>User ID is required.</div>");
}

$user_id = intval($_GET['user_id']);
$query = "SELECT Users.*, Employees.department_id, Employees.employee_id 
          FROM Users 
          LEFT JOIN Employees ON Users.user_id = Employees.user_id 
          WHERE Users.user_id = $user_id";
$result = mysqli_query($connection, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    die("<div class='message error'>User not found.</div>");
}

$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture and sanitize input
    $first_name = mysqli_real_escape_string($connection, $_POST['first_name']);
    $middle_name = mysqli_real_escape_string($connection, $_POST['middle_name']);
    $last_name = mysqli_real_escape_string($connection, $_POST['last_name']);
    $role = mysqli_real_escape_string($connection, $_POST['role']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $phone = mysqli_real_escape_string($connection, $_POST['phone']);

    $department_id = isset($_POST['department_id']) ? mysqli_real_escape_string($connection, $_POST['department_id']) : null;
    $employee_id = isset($_POST['employee_id']) ? mysqli_real_escape_string($connection, $_POST['employee_id']) : null;

    // Update Users table
    $update_user_query = "UPDATE Users SET 
                          first_name = '$first_name', 
                          middle_name = '$middle_name', 
                          last_name = '$last_name', 
                          role = '$role', 
                          email = '$email', 
                          phone_number = '$phone' 
                          WHERE user_id = $user_id";

    if (mysqli_query($connection, $update_user_query)) {
        // Update or insert into Employees table if role is Admin or Employee
        if (in_array($role, ['Admin', 'Employee'])) {
            $check_employee_query = "SELECT * FROM Employees WHERE user_id = $user_id";
            $employee_result = mysqli_query($connection, $check_employee_query);

            if ($employee_result && mysqli_num_rows($employee_result) > 0) {
                // Update existing employee record
                $update_employee_query = "UPDATE Employees SET 
                                          department_id = '$department_id', 
                                          employee_id = '$employee_id' 
                                          WHERE user_id = $user_id";
                mysqli_query($connection, $update_employee_query);
            } else {
                // Insert new employee record
                $insert_employee_query = "INSERT INTO Employees (user_id, department_id, employee_id) 
                                         VALUES ('$user_id', '$department_id', '$employee_id')";
                mysqli_query($connection, $insert_employee_query);
            }
        }

        echo "<div class='message success'>User successfully updated!</div>";
    } else {
        echo "<div class='message error'>Error updating user: " . mysqli_error($connection) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            text-align: center;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit User</h1>
        <form action="" method="POST">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo $user['first_name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="middle_name">Middle Name</label>
                <input type="text" id="middle_name" name="middle_name" value="<?php echo $user['middle_name']; ?>">
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo $user['last_name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="Admin" <?php if ($user['role'] === 'Admin') echo 'selected'; ?>>Admin</option>
                    <option value="Employee" <?php if ($user['role'] === 'Employee') echo 'selected'; ?>>Employee</option>
                    <option value="Defendant" <?php if ($user['role'] === 'Defendant') echo 'selected'; ?>>Defendant</option>
                    <option value="Cosigner" <?php if ($user['role'] === 'Cosigner') echo 'selected'; ?>>Cosigner</option>
                    <option value="Bounty Hunter" <?php if ($user['role'] === 'Bounty Hunter') echo 'selected'; ?>>Bounty Hunter</option>
                    <option value="Attorney" <?php if ($user['role'] === 'Attorney') echo 'selected'; ?>>Attorney</option>
                    <option value="Collection Agency" <?php if ($user['role'] === 'Collection Agency') echo 'selected'; ?>>Collection Agency</option>
                </select>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" value="<?php echo $user['phone_number']; ?>">
            </div>
            <?php if (in_array($user['role'], ['Admin', 'Employee'])): ?>
                <div class="form-group">
                    <label for="department_id">Department ID</label>
                    <input type="text" id="department_id" name="department_id" value="<?php echo $user['department_id']; ?>">
                </div>
                <div class="form-group">
                    <label for="employee_id">Employee ID</label>
                    <input type="text" id="employee_id" name="employee_id" value="<?php echo $user['employee_id']; ?>">
                </div>
            <?php endif; ?>
            <button type="submit" class="btn">Update User</button>
        </form>
    </div>
</body>
</html>
