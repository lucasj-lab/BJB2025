<?php
/**
 * login.php
 * A basic login page that checks user credentials and redirects to the appropriate dashboard.
 */
session_start();

// Include database connection (no HTML output here)
include 'db_connect.php';
include 'header.php';

// Include a common header or remove if you want a minimal login page
// include 'header.php';

$errorMessage = "";

// If the form was submitted, handle the login attempt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve posted form data
    $inputUsername = mysqli_real_escape_string($connection, $_POST['username'] ?? '');
    $inputPassword = mysqli_real_escape_string($connection, $_POST['password'] ?? '');

    // Basic validation
    if (!empty($inputUsername) && !empty($inputPassword)) {
        // Query your users table (adjust to your schema)
        // Example assumes columns: id, username, password, role
        $query = "
            SELECT id, username, password, role
            FROM users
            WHERE username = '$inputUsername'
            LIMIT 1
        ";
        $result = mysqli_query($connection, $query);

        if ($result && mysqli_num_rows($result) === 1) {
            $userRow = mysqli_fetch_assoc($result);

            // Check the password (in real apps, use password_hash and password_verify)
            if ($userRow['password'] === $inputPassword) {
                // Store user info in the session
                $_SESSION['user_id'] = $userRow['id'];
                $_SESSION['username'] = $userRow['username'];
                $_SESSION['role'] = $userRow['role'];

                // Redirect user based on role
                if ($userRow['role'] === 'admin') {
                    $_SESSION['admin_code'] = 'ABC123'; // or retrieve from DB if you store it
                    header('Location: admin_dashboard.php');
                    exit;
                } elseif ($userRow['role'] === 'manager') {
                    header('Location: management_dashboard.php');
                    exit;
                } elseif ($userRow['role'] === 'cs') {
                    // e.g. "cs" for customer support
                    header('Location: cs_dashboard.php');
                    exit;
                } else {
                    // If the role is unrecognized, go to a generic page
                    header('Location: index.php');
                    exit;
                }
            } else {
                // Password does not match
                $errorMessage = "Invalid password. Please try again.";
            }
        } else {
            // Username not found
            $errorMessage = "Invalid username. Please try again.";
        }
    } else {
        $errorMessage = "Please enter both username and password.";
    }
}

// Optionally close the DB connection now, or close at the bottom of the page
// mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bond James Bond - Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0; 
            padding: 0; 
            background-color: #f4f4f4;
        }
        .login-container {
            max-width: 400px;
            margin: 5% auto;
            padding: 20px;
            background: #fff;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            margin-top: 0;
            text-align: center;
        }
        form {
            display: flex; 
            flex-direction: column;
        }
        label {
            margin: 10px 0 5px;
        }
        input[type="text"], 
        input[type="password"] {
            padding: 10px; 
            border: 1px solid #ccc; 
            border-radius: 4px;
        }
        button {
            margin-top: 15px;
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #007bff; 
            color: #fff; 
            font-size: 1em; 
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error-message {
            margin-top: 10px; 
            color: red; 
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>

        <?php if (!empty($errorMessage)): ?>
            <div class="error-message"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" 
                   value="<?php echo isset($inputUsername) ? htmlspecialchars($inputUsername) : ''; ?>" 
                   required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
<?php mysqli_close($connection); ?>
