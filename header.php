<?php
/**
 * header.php
 * A shared header with navigation links for your application.
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bond James Bond</title>
    <style>
        /* Basic styling for the navbar */
        .navbar-links {
            background-color: #007bff;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            padding: 10px;
        }
        .navbar-links a {
            color: #fff;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 4px;
            background-color: #0069d9;
        }
        .navbar-links a:hover {
            background-color: #005cbf;
        }
        .navbar-links a.active {
            background-color: #0056b3;
        }
        body {
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>

<!-- If you only want the header, end the body tag *after* including this file. 
     Otherwise, if your pages each do <html>, <head>, etc., remove the above HTML 
     and place only the nav links here. -->

<div class="navbar-links">
    <!-- Adjust links according to your role logic or site structure -->
    <a href="admin_dashboard.php">Admin Dashboard</a>
    <a href="cs_dashboard.php">CS Dashboard</a>
    <a href="management_dashboard.php">Management Dashboard</a>
    <a href="start_prospect.php">Start Prospect</a>
    <a href="update_prospect.php">Update Prospect</a>
    <a href="submit_prospect.php">Submit Prospect</a>
    <a href="pending_prospects.php">Pending Prospects</a>
    <a href="approve_reject.php">Approve/Reject</a>
    <a href="client_records.php">Client Records</a>
    <a href="logout.php">Logout</a>
</div>

<!-- The rest of the pages can follow below, or each page can define its own <html> structure -->
