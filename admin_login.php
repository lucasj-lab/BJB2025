<?php
// admin_login.php
include 'db_connect.php';
include 'header.php';
session_start();

// Initialize email variable for form persistence
$email = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    // Check if admin exists
    $query = "SELECT Users.user_id, Users.password_hash, Admins.admin_code 
              FROM Users 
              JOIN Admins ON Users.user_id = Admins.user_id 
              WHERE Users.email = '$email' AND Users.role = 'Admin'";

    $result = mysqli_query($connection, $query);

    // Debugging the query
    if (!$result) {
        die("Query Error: " . mysqli_error($connection));
    } elseif (mysqli_num_rows($result) === 0) {
        $error = "No admin found with this email.";
    }

    if ($result && mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);

        // Verify password
        if (password_verify($password, $admin['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $admin['user_id'];
            $_SESSION['admin_code'] = $admin['admin_code'];

            // Redirect to admin dashboard
            header('Location: admin_dashboard.php');
            exit;
        } else {
            $error = "Invalid password. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        .container {
            max-width: 400px;
            margin: 50px auto;
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
        .error {
            color: #dc3545;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Login</h1>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="admin_login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Log In</button>
        </form>
    </div>
</body>

<?php include 'footer.php'; ?>
</html>
