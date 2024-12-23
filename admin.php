<?php
// admin.php
include 'db_connect.php';
include 'header.php';

// Generate Admin Code Function
function generateAdminCode() {
    return 'ADM' . strtoupper(substr(uniqid(), -6));
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = mysqli_real_escape_string($connection, $_POST['first_name']);
    $middle_name = isset($_POST['middle_name']) ? mysqli_real_escape_string($connection, $_POST['middle_name']) : null;
    $last_name = mysqli_real_escape_string($connection, $_POST['last_name']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($connection, $_POST['confirm_password']);

    // Generate a unique admin code
    $admin_code = generateAdminCode();

    // Validate Password Match
    if ($password !== $confirm_password) {
        die("<div class='message error'>Passwords do not match. Please try again.</div>");
    }

    // Hash the Password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Insert into Users Table
    $user_query = "INSERT INTO Users (first_name, middle_name, last_name, role, email, password_hash) 
                   VALUES ('$first_name', '$middle_name', '$last_name', 'Admin', '$email', '$password_hash')";

    if (mysqli_query($connection, $user_query)) {
        $user_id = mysqli_insert_id($connection);

        // Insert into Admins Table
        $admin_query = "INSERT INTO Admins (user_id, admin_code, department_id) 
                        VALUES ('$user_id', '$admin_code', 'Admin_Department')";

        if (mysqli_query($connection, $admin_query)) {
            header('Location: admin_login.php');

            // Insert into Employees Table
    $employee_query = "INSERT INTO Employees (user_id, department_id, employee_code) 
    VALUES ('$user_id', 'Admin_Department', '$admin_code')";
mysqli_query($connection, $employee_query);
            exit;
        } else {
            echo "<div class='message error'>Error adding admin details: " . mysqli_error($connection) . "</div>";
        }
    } else {
        echo "<div class='message error'>Error creating user: " . mysqli_error($connection) . "</div>";
    }
}
include 'footer.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <style>
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            margin-bottom: 5px;
        }
        .form-group input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            padding: 10px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            text-align: center;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Register Admin</h1>
        <form action="admin.php" method="POST">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="middle_name">Middle Name</label>
                <input type="text" id="middle_name" name="middle_name">
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn">Register Admin</button>
        </form>
    </div>
</body>
</html>
