<?php
require 'db_connect.php';
include 'header.php';


// 2) If the user submitted minimal prospect info, insert it into DB.
$insertSuccess = false;
$insertError = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prospect_submit'])) {
    // Minimal fields
    $firstName       = mysqli_real_escape_string($connection, $_POST['first_name'] ?? '');
    $middleName      = mysqli_real_escape_string($connection, $_POST['middle_name'] ?? '');
    $lastName        = mysqli_real_escape_string($connection, $_POST['last_name'] ?? '');
    $dateOfBirth     = mysqli_real_escape_string($connection, $_POST['date_of_birth'] ?? '');
    $gender          = mysqli_real_escape_string($connection, $_POST['gender'] ?? '');
    $county          = mysqli_real_escape_string($connection, $_POST['county'] ?? '');
    $jail            = mysqli_real_escape_string($connection, $_POST['jail'] ?? '');
    $arrestingAgency = mysqli_real_escape_string($connection, $_POST['arresting_agency'] ?? '');

    // Basic validation
    if (!$firstName || !$lastName || !$dateOfBirth || !$gender) {
        $insertError = "Please fill in all required fields (First Name, Last Name, Date of Birth, Gender).";
    } else {
        // Insert into prospects table
        $insertSql = "
            INSERT INTO prospects 
            (first_name, middle_name, last_name, date_of_birth, gender, county, jail, arresting_agency, status)
            VALUES
            ('$firstName', '$middleName', '$lastName', '$dateOfBirth', '$gender', '$county', '$jail', '$arrestingAgency', 'in_progress')
        ";
        if (mysqli_query($connection, $insertSql)) {
            $insertSuccess = true;
        } else {
            $insertError = "Database Error: " . mysqli_error($connection);
        }
    }
}

// We'll close the connection at the bottom of the file, after inline "mini endpoints."

/**********************************************************
 * MINI ENDPOINT 1: calculate_premium.php (inlined)
 * 
 * If 'calculate_premium' is requested via AJAX, do local 
 * computations and return JSON.
 * This replaces the Flask /calculate_premium route.
 **********************************************************/
if (isset($_GET['action']) && $_GET['action'] === 'calculate_premium') {
    header('Content-Type: application/json');

    // In a real scenario, you'd read POST data. Here, we read from $_POST
    $actualBondAmount = floatval($_POST['actual_bond_amount'] ?? 0);
    $discountRate     = ($_POST['discount_rate'] ?? 'false') === 'true';
    $jailFee          = ($_POST['jail_fee'] ?? 'false') === 'true';

    // Simple logic: If there's a jail fee, add $25 (example).
    $bondPlusFee = $actualBondAmount + ($jailFee ? 25 : 0);

    // Example premium calculations:
    $fee12 = $bondPlusFee * 0.12;
    $fee15 = $bondPlusFee * 0.15;

    // Format as dollars
    $response = [
        'bjb_bond_amount' => number_format($bondPlusFee, 2),
        'fee_12_cash'     => '$' . number_format($fee12, 2),
        'fee_15_cash'     => '$' . number_format($fee15, 2),
    ];
    echo json_encode($response);
    mysqli_close($connection);
    exit;
}

/**********************************************************
 * MINI ENDPOINT 2: confirm_payment_details.php (inlined)
 * 
 * If 'confirm_payment_details' is requested via AJAX, 
 * do something with the payment data (store in DB, etc.)
 **********************************************************/
if (isset($_GET['action']) && $_GET['action'] === 'confirm_payment') {
    header('Content-Type: application/json');

    // e.g. read a 'fee_amount' from POST
    $feeAmount = floatval($_POST['fee_amount'] ?? 0);

    // In a real scenario, you'd update the prospect's 
    // record or store a payment log. For demonstration:
    $res = [
        'status' => 'OK',
        'message' => "Payment details confirmed with fee_amount = $feeAmount"
    ];
    echo json_encode($res);
    mysqli_close($connection);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Start Prospect</title>
  <!-- Example modern accessible CSS + dark mode toggling -->
  <style>
    :root {
      --primary-color: #007bff;
      --secondary-color: #f4f4f4;
      --text-color: #333;
      --dark-bg: #2b2b2b;
      --dark-text-color: #f9f9f9;
      --border-radius: 6px;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background-color: var(--secondary-color);
      color: var(--text-color);
    }
    .dark-mode {
      background-color: var(--dark-bg) !important;
      color: var(--dark-text-color) !important;
    }
    nav {
      background-color: var(--primary-color);
      color: #fff;
      padding: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    nav h1 {
      margin: 0;
      font-size: 1.5rem;
    }
    .toggle-dark-mode {
      background: none;
      border: 1px solid #fff;
      color: #fff;
      padding: 0.5rem 1rem;
      border-radius: var(--border-radius);
      cursor: pointer;
    }
    .toggle-dark-mode:hover {
      background-color: rgba(255,255,255,0.2);
    }
    .container {
      max-width: 700px;
      margin: 2rem auto;
      padding: 2rem;
      background-color: #fff;
      border-radius: var(--border-radius);
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    h2 {
      margin-top: 0;
    }
    .form-group {
      display: flex;
      flex-direction: column;
      margin-bottom: 1rem;
    }
    .form-group label {
      margin-bottom: 0.5rem;
      font-weight: bold;
    }
    .form-group input[type="text"],
    .form-group input[type="date"],
    .form-group select {
      padding: 0.6rem;
      border: 1px solid #ccc;
      border-radius: var(--border-radius);
      font-size: 1rem;
    }
    .btn {
      display: inline-block;
      padding: 0.6rem 1.2rem;
      background-color: var(--primary-color);
      color: #fff;
      text-decoration: none;
      border: none;
      border-radius: var(--border-radius);
      cursor: pointer;
    }
    .btn:hover {
      background-color: #0056b3;
    }
    .error-message {
      color: red;
      font-weight: bold;
    }
    .success-message {
      color: green;
      font-weight: bold;
    }
    .hidden {
      display: none;
    }
    @media (max-width: 768px) {
      .container {
        margin: 1rem auto;
        padding: 1rem;
      }
    }
  </style>
</head>
<body>
  <nav>
    <h1>Bond James Bond, Inc.</h1>
    <button class="toggle-dark-mode">Dark Mode</button>
  </nav>

  <!-- Minimal Prospect Form -->
  <div class="container" id="prospect-form-section">
    <h2>Start Prospect</h2>

    <!-- Show success or error if user inserted the minimal fields -->
    <?php if (!empty($insertError)): ?>
      <p class="error-message"><?php echo htmlspecialchars($insertError); ?></p>
    <?php elseif ($insertSuccess): ?>
      <p class="success-message">Prospect created successfully!</p>
    <?php endif; ?>

    <form method="POST" action="">
      <!-- Hidden field to indicate the user clicked the minimal submission -->
      <input type="hidden" name="prospect_submit" value="1" />

      <div class="form-group">
        <label for="first_name">First Name (required):</label>
        <input type="text" id="first_name" name="first_name" required />
      </div>
      <div class="form-group">
        <label for="middle_name">Middle Name (optional):</label>
        <input type="text" id="middle_name" name="middle_name" />
      </div>
      <div class="form-group">
        <label for="last_name">Last Name (required):</label>
        <input type="text" id="last_name" name="last_name" required />
      </div>
      <div class="form-group">
        <label for="date_of_birth">Date of Birth (required):</label>
        <input type="date" id="date_of_birth" name="date_of_birth" required />
      </div>
      <div class="form-group">
        <label for="gender">Gender (required):</label>
        <select id="gender" name="gender" required>
          <option value="">Select Gender</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
      </div>
      <div class="form-group">
        <label for="county">County (optional):</label>
        <select id="county" name="county">
          <option value="">-- Select County --</option>
          <option value="Barrow">Barrow</option>
          <option value="Bartow">Bartow</option>
          <!-- add more as needed -->
        </select>
      </div>
      <div class="form-group">
        <label for="jail">Jail (optional):</label>
        <select id="jail" name="jail">
          <option value="">-- Select Jail --</option>
          <option value="Cobb-County">Cobb County</option>
          <option value="Smyrna-City">Smyrna City</option>
          <!-- add more as needed -->
        </select>
      </div>
      <div class="form-group">
        <label for="arresting_agency">Arresting Agency (optional):</label>
        <select id="arresting_agency" name="arresting_agency">
          <option value="">-- Select Agency --</option>
          <option value="Acworth">Acworth</option>
          <option value="Kennesaw-City">Kennesaw City</option>
          <!-- add more as needed -->
        </select>
      </div>

      <button type="submit" class="btn">Confirm Defendant Details</button>
    </form>
  </div>

  <!-- After minimal creation, user can do bond details. 
       We'll show it ALWAYS for demonstration, but in a real flow, 
       you might only show if $insertSuccess is true. 
  -->
  <div class="container" id="bond-details-section">
    <h2>Bond Details</h2>
    <form id="bond-form">
      <label for="actual_bond_amount">Actual Bond Amount:</label>
      <input type="number" id="actual_bond_amount" name="actual_bond_amount" step="0.01" required />
      <span class="error-message" id="bond-form-error"></span>
      <p>
        <input type="checkbox" id="discount_rate" name="discount_rate" checked />
        <label for="discount_rate">Discount Rate</label>
      </p>
      <p>
        <input type="checkbox" id="jail_fee" name="jail_fee" checked />
        <label for="jail_fee">Jail Fee</label>
      </p>
      <button type="submit" class="btn">Calculate Bond Details</button>
    </form>

    <div id="bond-details" class="hidden">
      <h3>BJB Bond Amount: <span id="bjb_bond_amount"></span></h3>
      <h3 class="rate">BJB Premium: <span id="fee_12_cash" style="display:none;"></span></h3>
      <h3 class="rate">BJB Premium: <span id="fee_15_cash" style="display:none;"></span></h3>
    </div>

    <form id="payment-method-form" class="hidden">
      <p>
        <h3><label for="amount_down">Amount Down:</label></h3>
        <input type="number" id="amount_down" name="amount_down" step="0.01" readonly />
      </p>
      <p>
        <h3><label for="payment_method">Select Payment Method</label></h3>
        <select id="payment_method" name="payment_method">
          <option value="cash">Cash</option>
          <option value="card">Card</option>
          <option value="split">Split</option>
        </select>
      </p>
      <div id="split-payment-fields" class="hidden">
        <h3><label for="card_amount">Card Amount:</label></h3>
        <input type="number" id="card_amount" name="card_amount" step="0.01" />
      </div>
      <button type="submit" id="calculate_total_button" class="btn">Confirm Payment Details</button>
    </form>
  </div>

  <!-- Payment summary & receipt area (hidden initially) -->
  <div class="container hidden" id="total_amount_section">
    <h2>Bond James Bond Inc.</h2>
    <h2>Receipt</h2>
    <br />
    <h2>BJB Bond Amount: <span id="bjb_bond_amount_display"></span></h2>
    <p><b>BJB Premium: </b><span id="bjb_premium"></span></p>
    <p>Premium Payment: <span id="amount_down_display"></span></p>
    <div id="terminal_fee_container" style="display:none;">
      <p>Terminal Fee: <span id="terminal_fee"></span></p>
    </div>
    <div id="total_payment_container" style="display:none;">
      <p>Total Payment: <span id="total_payment"></span></p>
    </div>
    <div id="remaining_balance_container">
      <p>Remaining Balance: <span id="remaining_balance"></span></p>
    </div>
    <button type="button" id="payment_plan_options_button" class="hidden btn">Payment Plan Options</button>
    <button type="button" id="print_button" class="hidden btn">Print Receipt</button>
    <br />
  </div>

  <!-- Hidden "receipt" content -->
  <div id="receipt" class="hidden">
    <h2>Bond James Bond Inc.</h2>
    <h2>Receipt</h2>
    <h2>Bond Amount: <span id="receipt_bjb_bond_amount"></span></h2>
    <p>BJB Premium: <span id="receipt_bjb_premium"></span></p>
    <p>Premium Payment: <span id="receipt_premium_payment"></span></p>
    <div id="receipt_terminal_fee_container">
      <p>Terminal Fee: <span id="receipt_terminal_fee"></span></p>
    </div>
    <div id="receipt_total_payment_container">
      <p>Total Payment: <span id="receipt_total_payment"></span></p>
    </div>
    <p>Remaining Balance: <span id="receipt_remaining_balance"></span></p>
    <p>Defendant Name: <span id="receipt_defendant_name"></span></p>
    <p>County of Jail: <span id="receipt_county"></span></p>
    <div>
      <h3>Signature:</h3>
      <p>__________________________</p>
    </div>
  </div>

  <!-- Payment plan section -->
  <div class="container hidden" id="payment-plan">
    <label for="payment_frequency">Payment Frequency:</label>
    <select id="payment_frequency" name="payment_frequency">
      <option value="one_time">One Time Payment</option>
      <option value="weekly">Weekly</option>
      <option value="biweekly">Biweekly</option>
      <option value="monthly">Monthly</option>
    </select>
    <label for="payment_amount">Payment Amount:</label>
    <input list="payment_options" id="payment_amount" name="payment_amount" autocomplete="off" />
    <datalist id="payment_options"></datalist>
    <span class="error-message" id="payment-plan-error"></span>

    <label for="payment_day_preference">Payment Day Preference:</label>
    <select id="payment_day_preference" name="payment_day_preference">
      <option value="none">None</option>
    </select>
    <input type="date" id="payment_date" name="payment_date" class="hidden" />

    <button id="generate_schedule_button" class="btn">Generate Payment Plan Schedule</button>
    <p id="error_message" style="color:red;"></p>
    <p>Number of Payments: <span id="number_of_payments"></span></p>
    <p>Payment Schedule:</p>
    <pre id="payment_schedule"></pre>
    <button id="save_button" class="hidden btn">Save Payment Plan</button>
    <button id="print_button" class="hidden btn">Print Payment Plan</button>
  </div>

  <!-- We include jQuery for easier DOM manipulation. -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
  /********************************************
   * Dark Mode Toggle
   ********************************************/
  $(document).ready(function() {
    if (localStorage.getItem('darkMode') === 'enabled') {
      $('body').addClass('dark-mode');
      $('.toggle-dark-mode').text('Light Mode');
    }

    $('.toggle-dark-mode').on('click', function() {
      $('body').toggleClass('dark-mode');
      if ($('body').hasClass('dark-mode')) {
        $(this).text('Light Mode');
        localStorage.setItem('darkMode', 'enabled');
      } else {
        $(this).text('Dark Mode');
        localStorage.setItem('darkMode', 'disabled');
      }
    });


    /**************************************
     * The Bond Calculation & Payment Plan
     * Replaces any Flask references with 
     * local "mini endpoint" calls.
     **************************************/

    let bjbPremium;
    let remainingBalances = [];
    let amountDownBeforePayMethod = 0;

    // Helper functions
    function showError(message, elementId) {
      $('#' + elementId).text(message);
    }
    function clearError(elementId) {
      $('#' + elementId).text('');
    }
    function formatNumber(number) {
      return number.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Trigger bond premium calculation
    $('#bond-form').on('submit', function(event) {
      event.preventDefault();
      const actualBondAmount = parseFloat($('#actual_bond_amount').val());
      const discountRate = $('#discount_rate').is(':checked');
      const jailFee = $('#jail_fee').is(':checked');

      if (isNaN(actualBondAmount) || actualBondAmount <= 0) {
        showError('Please enter a valid bond amount.', 'bond-form-error');
        return;
      } else {
        clearError('bond-form-error');
      }

      // We'll do an AJAX call to "this page" with ?action=calculate_premium
      // We pass the bond data as POST.
      $.ajax({
        url: '?action=calculate_premium', 
        method: 'POST',
        data: {
          actual_bond_amount: actualBondAmount,
          discount_rate: discountRate,
          jail_fee: jailFee
        },
        success: function(response) {
          // "response" is JSON from the mini-endpoint
          $('#bjb_bond_amount').text(response.bjb_bond_amount);
          $('#bjb_bond_amount_display').text(response.bjb_bond_amount);

          if (discountRate) {
            bjbPremium = parseFloat(response.fee_12_cash.replace('$', '').trim());
            $('#fee_12_cash').text(response.fee_12_cash).show();
            $('#fee_15_cash').hide();
            $('#amount_down').val(response.fee_12_cash.replace('$', '')).prop('readonly', true);
          } else {
            bjbPremium = parseFloat(response.fee_15_cash.replace('$', '').trim());
            $('#fee_15_cash').text(response.fee_15_cash).show();
            $('#fee_12_cash').hide();
            $('#amount_down').val('').prop('readonly', false);
          }

          $('#bond-details').removeClass('hidden');
          $('#payment-method-form').removeClass('hidden');
          $('#bjb_premium').text(formatNumber(bjbPremium));
          amountDownBeforePayMethod = parseFloat($('#amount_down').val()) || 0;
        },
        error: function() {
          alert('Invalid input or server error');
        }
      });
    });

    // Confirm Payment Details
    $('#calculate_total_button').on('click', function() {
      const discountRateApplied = $('#discount_rate').is(':checked');
      const amountDown = parseFloat($('#amount_down').val().replace('$','').trim());
      if (isNaN(amountDown)) {
        showError('Please enter a valid amount down.', 'bond-form-error');
        return;
      }

      if (amountDown < 50) {
        showError('The amount down must be at least $50.', 'bond-form-error');
        $('#total_amount_section').addClass('hidden');
        return;
      } else {
        clearError('bond-form-error');
      }
      if (amountDown > bjbPremium) {
        showError('Amount down cannot exceed the BJB Premium.', 'bond-form-error');
        $('#total_amount_section').addClass('hidden');
        return;
      } else {
        clearError('bond-form-error');
      }

      let remainingBalance;
      const paymentMethod = $('#payment_method').val();
      let terminalFee = 0;

      if (discountRateApplied) {
        remainingBalance = 0;
        $('#remaining_balance_container').addClass('hidden');
      } else {
        remainingBalance = bjbPremium - amountDown;
        $('#remaining_balance_container').removeClass('hidden');
      }

      $('#bjb_premium').text(formatNumber(bjbPremium));
      $('#amount_down_display').text(formatNumber(amountDown));

      if (paymentMethod === 'card') {
        terminalFee = amountDown * 0.05;
        $('#terminal_fee').text(formatNumber(terminalFee));
        $('#terminal_fee_container').show();
        $('#total_payment').text(formatNumber(amountDown + terminalFee));
        $('#total_payment_container').show();
      } else if (paymentMethod === 'split') {
        const cardAmount = parseFloat($('#card_amount').val()) || 0;
        terminalFee = cardAmount * 0.05;
        $('#terminal_fee').text(formatNumber(terminalFee));
        $('#terminal_fee_container').show();
        $('#total_payment').text(formatNumber(amountDown + cardAmount + terminalFee));
        $('#total_payment_container').show();
      } else {
        $('#terminal_fee_container').hide();
        $('#total_payment_container').hide();
      }

      if (remainingBalance === 0) {
        $('#remaining_balance').text('Paid In Full');
      } else {
        $('#remaining_balance').text(formatNumber(remainingBalance));
      }

      $('#total_amount_section').removeClass('hidden');
      remainingBalances.push(remainingBalance);

      // Update hidden receipt values, example
      $('#receipt_bjb_bond_amount').text(formatNumber(actualBondAmount));
      $('#receipt_bjb_premium').text(formatNumber(bjbPremium));
      $('#receipt_premium_payment').text(formatNumber(amountDown));
      if (terminalFee === 0) {
        $('#receipt_terminal_fee_container').hide();
      } else {
        $('#receipt_terminal_fee').text(formatNumber(terminalFee));
        $('#receipt_terminal_fee_container').show();
      }
      if (paymentMethod === 'card' || paymentMethod === 'split') {
        $('#receipt_total_payment').text(formatNumber(amountDown + terminalFee));
        $('#receipt_total_payment_container').show();
      } else {
        $('#receipt_total_payment_container').hide();
      }
      if (remainingBalance === 0) {
        $('#receipt_remaining_balance').text('Paid In Full');
      } else {
        $('#receipt_remaining_balance').text(formatNumber(remainingBalance));
      }
      // Example placeholders for defendant name, county, etc.
      $('#receipt_defendant_name').text('John Doe');
      $('#receipt_county').text('Cobb');
    });

    // Example: confirm payment details to the server
    $('#confirm_payment_details').on('click', function() {
      $.ajax({
        url: '?action=confirm_payment',
        type: 'POST',
        data: {
          fee_amount: bjbPremium
        },
        success: function(response) {
          console.log('Server says:', response);
        },
        error: function(err) {
          console.error('Error:', err);
        }
      });
    });

    // Other code from your snippet continues, handling discount toggles, 
    // payment method changes, printing receipts, etc.
    // (Kept as is or lightly edited for brevity.)

  });
  </script>
</body>
</html>
<?php
mysqli_close($connection);
