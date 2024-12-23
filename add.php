<?php
// add.php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture and sanitize input
    $first_name = mysqli_real_escape_string($connection, $_POST['first_name']);
    $middle_name = isset($_POST['middle_name']) ? mysqli_real_escape_string($connection, $_POST['middle_name']) : null;
    $last_name = mysqli_real_escape_string($connection, $_POST['last_name']);
    $role = mysqli_real_escape_string($connection, $_POST['role']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $phone = mysqli_real_escape_string($connection, $_POST['phone']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($connection, $_POST['confirm_password']);

    $department_id = isset($_POST['department_id']) ? mysqli_real_escape_string($connection, $_POST['department_id']) : null;
    $employee_id = isset($_POST['employee_id']) ? mysqli_real_escape_string($connection, $_POST['employee_id']) : null;
    $admin_code = isset($_POST['admin_code']) ? mysqli_real_escape_string($connection, $_POST['admin_code']) : null;

    // Validate password match
    if ($password !== $confirm_password) {
        die("<div class='message error'>Passwords do not match. Please try again.</div>");
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Validate role-specific fields
    if ($role === 'Employee' && (empty($department_id) || empty($employee_id))) {
        die("<div class='message error'>Department ID and Employee ID are required for Employee roles.</div>");
    }

    if ($role === 'Admin' && empty($admin_code)) {
        die("<div class='message error'>Admin Code is required for Admin roles.</div>");
    }

    // Insert into the Users table
    $query = "INSERT INTO Users (first_name, middle_name, last_name, role, email, phone_number, password_hash) 
              VALUES ('$first_name', '$middle_name', '$last_name', '$role', '$email', '$phone', '$password_hash')";

    if (mysqli_query($connection, $query)) {
        $user_id = mysqli_insert_id($connection);

        // Role-specific data handling
        if ($role === 'Employee') {
            $employee_query = "INSERT INTO Employees (user_id, department_id, employee_code) 
                               VALUES ('$user_id', '$department_id', '$employee_id')";

            if (!mysqli_query($connection, $employee_query)) {
                echo "<div class='message error'>User created, but failed to save employee details: " . mysqli_error($connection) . "</div>";
                exit;
            }
        } elseif ($role === 'Admin') {
            $admin_query = "INSERT INTO Admins (user_id, admin_code, department_id) 
                            VALUES ('$user_id', '$admin_code', 'Admin_Department')";

            if (!mysqli_query($connection, $admin_query)) {
                echo "<div class='message error'>User created, but failed to save admin details: " . mysqli_error($connection) . "</div>";
                exit;
            }
        }

        echo "<div class='message success'>User successfully added!</div>";
    } else {
        echo "<div class='message error'>Error adding user: " . mysqli_error($connection) . "</div>";
    }

    mysqli_close($connection);
}
?>
