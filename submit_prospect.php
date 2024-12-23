<?php
/**
 * submit_prospect.php
 * Page to submit a prospect record for manager approval.
 */
session_start();
include 'db_connect.php';
include 'header.php';

// Optional: Check role, etc.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prospectId = mysqli_real_escape_string($connection, $_POST['prospect_id'] ?? '');
    // TODO: Update the prospect status to 'pending_approval' or something
    // $updateQuery = "UPDATE prospects SET status='pending_approval' WHERE id='$prospectId'";
    // mysqli_query($connection, $updateQuery);

    echo "<p>Prospect with ID '$prospectId' submitted for approval (placeholder).</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Prospect for Approval</title>
</head>
<body>
    <div class="container">
        <h1>Submit Prospect for Approval</h1>
        <form method="POST" action="">
            <label for="prospect_id">Prospect ID:</label>
            <input type="text" id="prospect_id" name="prospect_id" required>

            <button type="submit">Submit Prospect</button>
        </form>
    </div>
</body>
</html>
<?php mysqli_close($connection); ?>
