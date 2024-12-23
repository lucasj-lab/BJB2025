<?php
/**
 * pending_prospects.php
 * Shows a table of all prospects that are not fully approved yet.
 */

session_start();
include 'db_connect.php';  // $connection
include 'header.php';      // if you have a common header or navbar

// Decide which status is considered "pending." 
// You could do status='in_progress' or status='pending_approval', or both.
$query = "SELECT id, first_name, last_name, status FROM prospects 
          WHERE status IN ('in_progress','pending_approval')";
$result = mysqli_query($connection, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pending Prospects</title>
</head>
<body>
  <div class="container">
    <h1>Pending Prospects</h1>
    <?php if ($result && mysqli_num_rows($result) > 0): ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Defendant Name</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['id']); ?></td>
              <td>
                <?php 
                  // Combine first_name + last_name
                  $defName = $row['first_name'] . ' ' . $row['last_name'];
                  echo htmlspecialchars($defName);
                ?>
              </td>
              <td><?php echo htmlspecialchars($row['status']); ?></td>
              <td>
                <!-- Link to update_prospect with the prospect's ID -->
                <a href="update_prospect.php?id=<?php echo $row['id']; ?>">
                  Update
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No pending prospects found.</p>
    <?php endif; ?>
  </div>
</body>
</html>
<?php
mysqli_free_result($result);
mysqli_close($connection);
?>
