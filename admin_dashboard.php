<?php
// admin_dashboard.php
include 'db_connect.php';
include 'header.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_code'])) {
    header('Location: admin_login.php');
    exit;
}

$admin_code = $_SESSION['admin_code'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .navbar {
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-align: center;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .card {
            flex: 1 1 calc(33.333% - 20px);
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .card h3 {
            margin-bottom: 10px;
        }
        .card p {
            font-size: 0.9em;
            color: #555;
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
        @media (max-width: 768px) {
            .card {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Admin Dashboard</h1>
        <p>Welcome, Admin! Your Admin Code: <?php echo htmlspecialchars($admin_code); ?></p>
    </div>
    <div class="container">
        <div class="grid">
            <div class="card">
                <h3>Manage Database</h3>
                <p>View, edit, or delete user accounts.</p>
                <a href="admin_db.php" class="btn">Manage Database</a>
            </div>
            <div class="card">
                <h3>System Settings</h3>
                <p>Configure system preferences and settings.</p>
                <a href="system_settings.php" class="btn">Settings</a>
            </div>
            <div class="card">
                <h3>Reports</h3>
                <p>Generate and view system reports.</p>
                <a href="reports.php" class="btn">View Reports</a>
            </div>
            <div class="card">
                <h3>Logout</h3>
                <p>End your current session.</p>
                <a href="logout.php" class="btn">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>