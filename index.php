<?php
// Include necessary files
include 'db_connect.php';
include 'header.php';

// Handle adding a user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = mysqli_real_escape_string($connection, $_POST['first_name']);
    $middle_name = mysqli_real_escape_string($connection, $_POST['middle_name']);
    $last_name = mysqli_real_escape_string($connection, $_POST['last_name']);
    $role = mysqli_real_escape_string($connection, $_POST['role']);
    $department = mysqli_real_escape_string($connection, $_POST['department']);
    $position = mysqli_real_escape_string($connection, $_POST['position']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $phone = mysqli_real_escape_string($connection, $_POST['phone']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($connection, $_POST['confirm_password']);

    // Ensure email is unique
    $email_check_query = "SELECT * FROM Users WHERE email = '$email'";
    $email_check_result = mysqli_query($connection, $email_check_query);

    if (mysqli_num_rows($email_check_result) > 0) {
        echo "<div class='message error'>Email already exists. Please use a different email.</div>";
        return;
    }

    if ($password !== $confirm_password) {
        echo "<div class='message error'>Passwords do not match.</div>";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert user into database
        $insert_user_query = "INSERT INTO Users (first_name, middle_name, last_name, email, phone_number, password_hash, role) 
                              VALUES ('$first_name', '$middle_name', '$last_name', '$email', '$phone', '$hashed_password', '$role')";

        if (mysqli_query($connection, $insert_user_query)) {
            $user_id = mysqli_insert_id($connection); // Get the last inserted user ID

            // Insert employee record if role is Employee
            if ($role === 'Employee') {
                $insert_employee_query = "INSERT INTO Employees (user_id, department, position) 
                                         VALUES ('$user_id', '$department', '$position')";
                if (!mysqli_query($connection, $insert_employee_query)) {
                    echo "<div class='message error'>Error adding employee details: " . mysqli_error($connection) . "</div>";
                } else {
                    echo "<div class='message success'>User and employee details added successfully!</div>";
                }
            } else {
                echo "<div class='message success'>User added successfully!</div>";
            }
        } else {
            echo "<div class='message error'>Error adding user: " . mysqli_error($connection) . "</div>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <style>
        .container {
            max-width: 90%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .form-group {
            flex: 1 1 calc(50% - 10px);
            display: flex;
            flex-direction: column;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleDropdown = document.getElementById('role');
            const departmentDropdown = document.getElementById('department');
            const positionDropdown = document.getElementById('position');

            roleDropdown.addEventListener('change', function() {
                const adminFields = document.getElementById('admin-fields');
                const roleSpecific = document.getElementById('role-specific-fields');

                if (roleDropdown.value === 'Employee') {
                    adminFields.style.display = 'block';
                    roleSpecific.style.display = 'block';
                } else {
                    adminFields.style.display = 'none';
                    roleSpecific.style.display = 'none';
                }
            });

            departmentDropdown.addEventListener('change', function() {
                const selectedDepartment = departmentDropdown.value;
                positionDropdown.innerHTML = ''; // Clear existing options

                if (selectedDepartment === 'Finance') {
                    positionDropdown.innerHTML = `
                        <option value="Accounts Receivable">Accounts Receivable</option>
                        <option value="Accounts Payable">Accounts Payable</option>
                        <option value="Controller">Controller</option>
                    `;
                } else if (selectedDepartment === 'Customer Support') {
                    positionDropdown.innerHTML = `
                        <option value="Call Center Specialist">Call Center Specialist</option>
                        <option value="Office Administrator">Office Administrator</option>
                        <option value="Agent Signature">Agent Signature</option>
                    `;
                } else if (selectedDepartment === 'Management') {
                    positionDropdown.innerHTML = `
                        <option value="Risk Reduction">Risk Reduction</option>
                        <option value="Quality Assurance">Quality Assurance</option>
                        <option value="Administrative Assistant">Administrative Assistant</option>
                        <option value="Employee Relations">Employee Relations</option>
                    `;
                } else {
                    positionDropdown.innerHTML = '<option value="">No Position Needed</option>';
                }
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h2>Add User</h2>
        <form action="" method="POST">
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
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="Employee">Employee</option>
                    <option value="Defendant">Defendant</option>
                    <option value="Cosigner">Cosigner</option>
                    <option value="Bounty Hunter">Bounty Hunter</option>
                </select>
            </div>

            <div id="admin-fields" style="display:none;">
                <div class="form-group">
                    <label for="department">Department</label>
                    <select id="department" name="department">
                        <option value="">Select Department</option>
                        <option value="Finance">Finance</option>
                        <option value="Customer Support">Customer Support</option>
                        <option value="Management">Management</option>
                    </select>
                </div>
                <div class="form-group" id="role-specific-fields" style="display:none;">
                    <label for="position">Position</label>
                    <select id="position" name="position">
                        <option value="">Select Position</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn">Add User</button>
        </form>
    </div>
</body>
</html>
