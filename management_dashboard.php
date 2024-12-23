<?php
// management_dashboard.php
// A management dashboard for approvals, tracking pending prospects, accessing client records, etc.

session_start();
include 'db_connect.php';
include 'header.php';

// Check if manager (approvals role) is logged in
if (!isset($_SESSION['user_id'])) {
    // if ($_SESSION['role'] !== 'manager') { ... } // if you have role-based checks
    header('Location: login.php');
    exit;
}

$manager_user_id = $_SESSION['user_id'] ?? 'Manager';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Management Dashboard</title>
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
      <h1>Management Dashboard</h1>
      <p>Welcome, <?php echo htmlspecialchars($manager_user_id); ?>!</p>
  </div>
  <div class="container">
      <div class="grid">
          <div class="card">
              <h3>Pending Prospects</h3>
              <p>View all pending prospects awaiting approval.</p>
              <!-- Link to a page listing pending prospects -->
              <a href="pending_prospects.php" class="btn">Pending Prospects</a>
          </div>
          <div class="card">
              <h3>Approve or Reject Requests</h3>
              <p>Manage incoming approval requests.</p>
              <!-- Link to a page where manager can approve/reject -->
              <a href="approve_reject.php" class="btn">Approve/Reject</a>
          </div>
          <div class="card">
              <h3>Client Records</h3>
              <p>Access defendant & cosigner records.</p>
              <!-- Link to a page showing defendants, cosigners, or other client records -->
              <a href="client_records.php" class="btn">View Records</a>
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
