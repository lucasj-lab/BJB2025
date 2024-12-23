<?php
/**
 * update_prospect.php
 * Displays & updates the "in-progress" or "pending" prospect record, 
 * including Cosigner details. Also allows "Submit for Approval" 
 * which sets status='pending_approval'.
 */

session_start();
include 'db_connect.php';  // $connection
include 'header.php';      // optional common header

// Make sure we have an ID in the URL: update_prospect.php?id=XYZ
if (!isset($_GET['id'])) {
    echo "<p style='color:red;'>No prospect ID specified.</p>";
    exit;
}
$prospectId = intval($_GET['id']);  // basic sanitization

// 1) Fetch the existing prospect from DB
$sql = "SELECT * FROM prospects WHERE id=$prospectId LIMIT 1";
$result = mysqli_query($connection, $sql);
if (!$result || mysqli_num_rows($result) === 0) {
    echo "<p style='color:red;'>Prospect #$prospectId not found.</p>";
    exit;
}
$prospect = mysqli_fetch_assoc($result);
mysqli_free_result($result);

// Compute age from dob (if dob not empty)
$age = "";
if (!empty($prospect['dob']) && $prospect['dob'] !== "0000-00-00") {
    $dobTime = strtotime($prospect['dob']);
    if ($dobTime) {
        $age = floor((time() - $dobTime) / (365.2425 * 24 * 60 * 60)); 
    }
}

// 2) If the form is submitted, we do an UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the user clicked "Submit for Approval" 
    // or just a normal "Update" button
    $isSubmitForApproval = (isset($_POST['submit_approval']) && $_POST['submit_approval'] === 'yes');

    // Collect all fields from POST
    $firstName = mysqli_real_escape_string($connection, $_POST['first_name'] ?? '');
    $lastName  = mysqli_real_escape_string($connection, $_POST['last_name'] ?? '');
    
    // We'll store the user-input DOB (in "YYYY-MM-DD" format)
    $dob       = mysqli_real_escape_string($connection, $_POST['dob'] ?? '');  // actual date field

    // The rest of your fields...
    $city            = mysqli_real_escape_string($connection, $_POST['city'] ?? '');
    $state           = mysqli_real_escape_string($connection, $_POST['state'] ?? '');
    $gaResidence     = mysqli_real_escape_string($connection, $_POST['ga_residence_status'] ?? '');
    $livingSituation = mysqli_real_escape_string($connection, $_POST['living_situation'] ?? '');
    $sex             = mysqli_real_escape_string($connection, $_POST['sex'] ?? '');
    $existingClient  = isset($_POST['existing_client_status']) ? 1 : 0;

    $countyOffense   = mysqli_real_escape_string($connection, $_POST['county_of_offense'] ?? '');
    $jail            = mysqli_real_escape_string($connection, $_POST['jail'] ?? '');
    $arrestingAgency = mysqli_real_escape_string($connection, $_POST['arresting_agency'] ?? '');
    $criminalCharges = mysqli_real_escape_string($connection, $_POST['criminal_charges'] ?? '');
    $bailBondAmount  = floatval($_POST['bail_bond_amount'] ?? 0);
    $premiumQuoted   = floatval($_POST['premium_quoted'] ?? 0);
    $discountRate    = isset($_POST['discount_rate']) ? 1 : 0;
    $amountDown      = floatval($_POST['amount_down'] ?? 0);

    // Payment plan = 1 if amount_down < premium_quoted
    $paymentPlan     = ($amountDown < $premiumQuoted) ? 1 : 0;

    // Cosigner fields
    $cosFirstName      = mysqli_real_escape_string($connection, $_POST['cos_first_name'] ?? '');
    $cosLastName       = mysqli_real_escape_string($connection, $_POST['cos_last_name'] ?? '');
    $cosCity           = mysqli_real_escape_string($connection, $_POST['cos_city'] ?? '');
    $cosState          = mysqli_real_escape_string($connection, $_POST['cos_state'] ?? '');
    $cosHomeStatus     = mysqli_real_escape_string($connection, $_POST['cos_home_status'] ?? '');
    $cosHowLongAddress = mysqli_real_escape_string($connection, $_POST['cos_how_long_address'] ?? '');
    $cosRelationship   = mysqli_real_escape_string($connection, $_POST['cos_relationship'] ?? '');
    $cosRelDuration    = mysqli_real_escape_string($connection, $_POST['cos_relationship_duration'] ?? '');
    $cosEmployer       = mysqli_real_escape_string($connection, $_POST['cos_employer'] ?? '');
    $cosOccupation     = mysqli_real_escape_string($connection, $_POST['cos_occupation'] ?? '');
    $cosEmpDuration    = mysqli_real_escape_string($connection, $_POST['cos_employment_duration'] ?? '');
    $cosReason         = mysqli_real_escape_string($connection, $_POST['cos_reason'] ?? '');

    // Decide on status
    // If the user clicked "Submit for Approval," set status to 'pending_approval'
    // Otherwise, keep the old status. (In production, you may want an "else" that keeps it in_progress.)
    $newStatus = $prospect['status']; // by default, keep the old status
    if ($isSubmitForApproval) {
        $newStatus = 'pending_approval';
    }

    $updateSql = "
      UPDATE prospects
      SET
        first_name = '$firstName',
        last_name  = '$lastName',
        dob        = '$dob',        -- store actual date
        city       = '$city',
        state      = '$state',
        ga_residence_status  = '$gaResidence',
        living_situation     = '$livingSituation',
        sex                  = '$sex',
        existing_client_status = $existingClient,

        county_of_offense    = '$countyOffense',
        jail                 = '$jail',
        arresting_agency     = '$arrestingAgency',
        criminal_charges     = '$criminalCharges',
        bail_bond_amount     = $bailBondAmount,
        premium_quoted       = $premiumQuoted,
        discount_rate        = $discountRate,
        amount_down          = $amountDown,
        payment_plan         = $paymentPlan,

        cos_first_name       = '$cosFirstName',
        cos_last_name        = '$cosLastName',
        cos_city             = '$cosCity',
        cos_state            = '$cosState',
        cos_home_status      = '$cosHomeStatus',
        cos_how_long_address = '$cosHowLongAddress',
        cos_relationship     = '$cosRelationship',
        cos_relationship_duration = '$cosRelDuration',
        cos_employer         = '$cosEmployer',
        cos_occupation       = '$cosOccupation',
        cos_employment_duration = '$cosEmpDuration',
        cos_reason           = '$cosReason',

        status               = '$newStatus'
      WHERE id = $prospectId
      LIMIT 1
    ";

    if (mysqli_query($connection, $updateSql)) {
        echo "<p style='color:green;'>Prospect #$prospectId updated successfully!</p>";
        // If we just changed status to 'pending_approval', let user know
        if ($isSubmitForApproval) {
            echo "<p style='color:blue;'>Prospect has been submitted for approval!</p>";
        }
        // Refresh $prospect from DB (so form shows new changes)
        $refetch = mysqli_query($connection, "SELECT * FROM prospects WHERE id=$prospectId LIMIT 1");
        $prospect = mysqli_fetch_assoc($refetch);
        mysqli_free_result($refetch);

        // Recompute the Age from new dob if changed
        $age = "";
        if (!empty($prospect['dob']) && $prospect['dob'] !== "0000-00-00") {
            $dobTime = strtotime($prospect['dob']);
            if ($dobTime) {
                $age = floor((time() - $dobTime) / (365.2425 * 24 * 60 * 60)); 
            }
        }
    } else {
        echo "<p style='color:red;'>Update error: " . mysqli_error($connection) . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Update Prospect #<?php echo $prospectId; ?></title>
  <script>
  // Ask for confirmation if "Submit for Approval" is clicked
  function confirmApproval() {
    return confirm("Are you sure you want to submit this prospect for approval?");
  }
  </script>
</head>
<body>
  <div class="container">
    <h1>Update Prospect #<?php echo htmlspecialchars($prospectId); ?></h1>
    <form method="POST" action="?id=<?php echo $prospectId; ?>">

      <label for="first_name">Defendant First Name:</label>
      <input type="text" id="first_name" name="first_name" 
             value="<?php echo htmlspecialchars($prospect['first_name']); ?>" required><br>

      <label for="last_name">Defendant Last Name:</label>
      <input type="text" id="last_name" name="last_name" 
             value="<?php echo htmlspecialchars($prospect['last_name']); ?>" required><br>

      <!-- We store `dob` in DB, but show an "Age:" read-only field 
           computed from the existing dob. If you want the user to 
           update the actual date of birth, let them edit "dob" 
           in a real date picker. -->
      <label for="dob">Date of Birth (Stored in DB):</label>
      <input type="date" id="dob" name="dob"
             value="<?php echo htmlspecialchars($prospect['dob']); ?>"><br>

      <label for="age_display">Age (computed from DOB):</label>
      <input type="number" id="age_display" name="age_display"
             value="<?php echo htmlspecialchars($age); ?>" readonly><br>

      <label for="city">City of Residence:</label>
      <input type="text" id="city" name="city"
             value="<?php echo htmlspecialchars($prospect['city']); ?>"><br>

      <label for="state">State of Residence:</label>
      <input type="text" id="state" name="state"
             value="<?php echo htmlspecialchars($prospect['state']); ?>"><br>

      <label for="ga_residence_status">GA Residence Status:</label>
      <input type="text" id="ga_residence_status" name="ga_residence_status"
             value="<?php echo htmlspecialchars($prospect['ga_residence_status']); ?>"><br>

      <label for="living_situation">Living Situation (rent/own/etc.):</label>
      <input type="text" id="living_situation" name="living_situation"
             value="<?php echo htmlspecialchars($prospect['living_situation']); ?>"><br>

      <label for="sex">Sex (M/F):</label>
      <input type="text" id="sex" name="sex"
             value="<?php echo htmlspecialchars($prospect['sex']); ?>"><br>

      <label>Existing Client?</label>
      <input type="checkbox" id="existing_client_status" name="existing_client_status" 
         <?php if ($prospect['existing_client_status']) echo 'checked'; ?>><br>

      <hr>

      <label for="county_of_offense">County of Offense:</label>
      <input type="text" id="county_of_offense" name="county_of_offense"
             value="<?php echo htmlspecialchars($prospect['county_of_offense']); ?>"><br>

      <label for="jail">Jail:</label>
      <input type="text" id="jail" name="jail"
             value="<?php echo htmlspecialchars($prospect['jail']); ?>"><br>

      <label for="arresting_agency">Arresting Agency:</label>
      <input type="text" id="arresting_agency" name="arresting_agency"
             value="<?php echo htmlspecialchars($prospect['arresting_agency']); ?>"><br>

      <label for="criminal_charges">Criminal Charges:</label>
      <input type="text" id="criminal_charges" name="criminal_charges"
             value="<?php echo htmlspecialchars($prospect['criminal_charges']); ?>"><br>

      <label for="bail_bond_amount">Bail Bond Amount:</label>
      <input type="number" step="0.01" id="bail_bond_amount" name="bail_bond_amount"
             value="<?php echo htmlspecialchars($prospect['bail_bond_amount']); ?>"><br>

      <label for="premium_quoted">Premium Quoted:</label>
      <input type="number" step="0.01" id="premium_quoted" name="premium_quoted"
             value="<?php echo htmlspecialchars($prospect['premium_quoted']); ?>"><br>

      <label for="discount_rate">Discount Rate? (Check if yes):</label>
      <input type="checkbox" id="discount_rate" name="discount_rate"
        <?php if ($prospect['discount_rate'] == 1) echo 'checked'; ?>><br>

      <label for="amount_down">Amount Down:</label>
      <input type="number" step="0.01" id="amount_down" name="amount_down"
             value="<?php echo htmlspecialchars($prospect['amount_down']); ?>"><br>

      <hr>
      <!-- Cosigner fields -->
      <h3>Cosigner Details</h3>
      <label for="cos_first_name">Cosigner First Name:</label>
      <input type="text" id="cos_first_name" name="cos_first_name"
             value="<?php echo htmlspecialchars($prospect['cos_first_name']); ?>"><br>

      <label for="cos_last_name">Cosigner Last Name:</label>
      <input type="text" id="cos_last_name" name="cos_last_name"
             value="<?php echo htmlspecialchars($prospect['cos_last_name']); ?>"><br>

      <label for="cos_city">City of Residence:</label>
      <input type="text" id="cos_city" name="cos_city"
             value="<?php echo htmlspecialchars($prospect['cos_city']); ?>"><br>

      <label for="cos_state">State of Residence:</label>
      <input type="text" id="cos_state" name="cos_state"
             value="<?php echo htmlspecialchars($prospect['cos_state']); ?>"><br>

      <label for="cos_home_status">Own/rent? House/Apt?:</label>
      <input type="text" id="cos_home_status" name="cos_home_status"
             value="<?php echo htmlspecialchars($prospect['cos_home_status']); ?>"><br>

      <label for="cos_how_long_address">How long at current address?</label>
      <input type="text" id="cos_how_long_address" name="cos_how_long_address"
             value="<?php echo htmlspecialchars($prospect['cos_how_long_address']); ?>"><br>

      <label for="cos_relationship">Relationship to Defendant:</label>
      <input type="text" id="cos_relationship" name="cos_relationship"
             value="<?php echo htmlspecialchars($prospect['cos_relationship']); ?>"><br>

      <label for="cos_relationship_duration">Duration of Relationship:</label>
      <input type="text" id="cos_relationship_duration" name="cos_relationship_duration"
             value="<?php echo htmlspecialchars($prospect['cos_relationship_duration']); ?>"><br>

      <label for="cos_employer">Cosigner Employer:</label>
      <input type="text" id="cos_employer" name="cos_employer"
             value="<?php echo htmlspecialchars($prospect['cos_employer']); ?>"><br>

      <label for="cos_occupation">Occupation/Position:</label>
      <input type="text" id="cos_occupation" name="cos_occupation"
             value="<?php echo htmlspecialchars($prospect['cos_occupation']); ?>"><br>

      <label for="cos_employment_duration">Employment Duration:</label>
      <input type="text" id="cos_employment_duration" name="cos_employment_duration"
             value="<?php echo htmlspecialchars($prospect['cos_employment_duration']); ?>"><br>

      <label for="cos_reason">Reason (optional):</label>
      <input type="text" id="cos_reason" name="cos_reason"
             value="<?php echo htmlspecialchars($prospect['cos_reason']); ?>"><br>

      <br>
      <!-- Normal "Update" button -->
      <button type="submit">Update Prospect</button>

      <!-- "Submit for Approval" button - triggers confirmation in JS -->
      <button type="submit" name="submit_approval" value="yes"
              onclick="return confirmApproval();"
              style="background-color:orange; margin-left: 20px;">
        Submit Prospect for Approval
      </button>
    </form>
  </div>
</body>
</html>
<?php
mysqli_close($connection);
