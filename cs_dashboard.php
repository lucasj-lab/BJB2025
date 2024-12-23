<?php
// cs_dashboard.php
// A customer support dashboard for starting, updating, submitting prospects.

// Start session (before any HTML output).
session_start();

// Include DB connection and header, if needed.
include 'db_connect.php';
include 'header.php';

// Check if user is logged in as a customer support role (adjust logic as needed).
if (!isset($_SESSION['user_id'])) {
    // If you also have a specific role code for CS, check it here.
    // For example: if ($_SESSION['role'] !== 'customer_support') { ... }
    header('Location: login.php');
    exit;
}

// (Optional) You might store a 'cs_code' or something in session. 
$cs_user_id = $_SESSION['user_id'] ?? 'Unknown';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Customer Support Dashboard</title>
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

    /* 
      Main container for side-by-side columns (left & right).
      "justify-content: space-between" pushes the two columns to opposite ends.
    */
    .main-container {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin: 20px; /* Some outer spacing */
    }

    /*
      Left and right columns each stack their cards vertically.
      Use half-width (or adjust as needed).
    */
    .column {
      display: flex;
      flex-direction: column;
      gap: 20px; /* space between cards */
      width: 48%;
    }

    .card {
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
      /* On small screens, stack the two columns vertically */
      .main-container {
        flex-direction: column;
        align-items: center;
      }
      .column {
        width: 80%;
      }
    }
  </style>
</head>
<body>
  <div class="navbar">
    <h1>Customer Support Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($cs_user_id); ?>!</p>
  </div>

  <!-- Parent container for left and right columns -->
  <div class="main-container">
    <!-- Left column -->
    <div class="column">
      <div class="card">
        <h3>Start New Prospect</h3>
        <p>Begin collecting data for a new prospect.</p>
        <a href="start_prospect.php" class="btn">Start Prospect</a>
      </div>
      <div class="card">
        <h3>Update Prospect</h3>
        <p>Edit details of an existing prospect.</p>
        <a href="update_prospect.php" class="btn">Update Prospect</a>
      </div>
    </div>

    <!-- Right column -->
    <div class="column">
      <div class="card">
        <h3>Submit Prospects for Approval</h3>
        <p>Send prospect data to manager for approval.</p>
        <a href="submit_prospect.php" class="btn">Submit for Approval</a>
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
