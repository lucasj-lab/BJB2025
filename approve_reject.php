<?php
session_start();
include 'db_connect.php';

// Select all prospects with status='pending_approval'
$sql = "SELECT id, first_name, last_name, dob FROM prospects 
        WHERE status='pending_approval' 
        ORDER BY id DESC";
$res = mysqli_query($connection, $sql);

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Approve or Reject Prospects</title>
</head>
<body>
  <h1>Prospects Pending Approval</h1>
  <?php if ($res && mysqli_num_rows($res) > 0): ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Defendant Name</th>
          <th>Date of Birth</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($row = mysqli_fetch_assoc($res)): ?>
        <tr>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
          <td><?php echo htmlspecialchars($row['dob']); ?></td>
          <td>
            <!-- Manager can approve or reject, e.g. linking to separate logic -->
            <a href="approve_reject_action.php?id=<?php echo $row['id']; ?>&action=approve">Approve</a>
            <a href="approve_reject_action.php?id=<?php echo $row['id']; ?>&action=reject">Reject</a>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No prospects currently pending approval.</p>
  <?php endif; ?>

</body>
</html>
<?php
mysqli_free_result($res);
mysqli_close($connection);
?>
