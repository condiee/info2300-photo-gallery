<?php include("includes/init.php");
$title = "PRICING & ACCOUNTS";

// Default to form
$show_form = TRUE;

// Default to no feedback
$show_account_feedback = FALSE;
$show_email_feedback = FALSE;
$show_phone_feedback = FALSE;
$show_date_feedback = FALSE;
$show_pickup_feedback = FALSE;
$show_request_feedback = FALSE;

// default values
$account = '';
$email = '';
$phone = '';
$date = '';
$pickup = '';
$request='';

// Filter Input
$account = filter_input(INPUT_POST, "account", FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
$phone = filter_input(INPUT_POST, "phone", FILTER_SANITIZE_STRING);
$date = filter_input(INPUT_POST, "date", FILTER_SANITIZE_STRING);
$pickup = filter_input(INPUT_POST, "time", FILTER_SANITIZE_STRING);
$request = filter_input(INPUT_POST, "orderDetails", FILTER_SANITIZE_STRING);

// Escape Output
htmlspecialchars($account);
htmlspecialchars($email);
htmlspecialchars($phone);
htmlspecialchars($date);
htmlspecialchars($pickup);
htmlspecialchars($request);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $is_order_valid = TRUE;

    $account = trim($_POST['account']);
    if (empty($account) || $account == '') {
      $is_order_valid = FALSE;
      $show_account_feedback = TRUE;
    }

    $email = trim($_POST['contactEmail']);
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $is_order_valid = FALSE;
      $show_email_feedback = TRUE;
    }

    $phone = trim($_POST['contactPhone']);
    if (empty($phone) || !filter_var($phone, FILTER_VALIDATE_INT)) {
      $is_order_valid = FALSE;
      $show_phone_feedback = TRUE;
    }

    $date = trim($_POST['date']);
    if (empty($date)) {
      $is_order_valid = FALSE;
      $show_date_feedback = TRUE;
    }

    $pickup = trim($_POST['time']);
    if (empty($pickup)) {
      $is_order_valid = FALSE;
      $show_pickup_feedback = TRUE;
    }

    $request = trim($_POST['orderDetails']);
    if (empty($request)) {
      $is_order_valid = FALSE;
      $show_request_feedback = TRUE;
    }

    //if order is valid, show form is false, confirmation page will come up
    $show_form = !$is_order_valid;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include("includes/head.php");?>
<body>
    <?php include("includes/header.php");?>
    <?php include("includes/nav.php");?>

    <section>
    <h2>Ingredient Pricing</h2>
        <p>All of our ingredients come from sustainable local vendors, and we do our best to compost, recycle, promote the use of our reusable mugs, dishes, and utensils, and reduce our impact on the environment as best we can. Our pricing reflects the quality of our ingredients–we are a non-profit, and we charge only what our products merit.</p>

        <p>If you have any suggestions for how we can do better at this,  do let us know!</p>
    </section>

    <section>
    <h2>Payment Options</h2>
        <p>
            We take cash, credit (Apple Pay accepted), and Cornell Card. No BRBs or MealChoice–sorry! We wish we could. We do offer discounts to grad students and faculty, and to anyone who brings their own mug or bowl–so BYOB!
        </p>

        <p>If you would like to start an account for large orders of coffee and/or pastries to be placed ahead of time, email us at templeofzeus@cornell.edu and we'll help you set one up, or place an order below and select "Create New Account."" Those eligible to create an account are academic departments and recognized student organizations. Scroll down to find the order form for your convenience!</p>
    </section>

    <section>
    <h2>Order Ahead</h2>
        <?php if ($show_form) { ?>
            <h3>Place an account order here:</h3>
                <p>For use by academic departments and student organizations.</p>
                <p>Want to start an account to order coffee or pastries from Zeus for your meetings or events?</p>
                <p>Email us at templeofzeus@cornell.edu or fill out the order form below and we'll help you set up an account to do just that!</p>
                <form id="OrderForm" class="general" method="post" novalidate>

                <div>
                    <label for="account">Account: </label>
                        <select name="account" id="account" required>
                            <option value="<?php echo htmlspecialchars($account); ?>"><?php echo htmlspecialchars($account); ?></option>
                            <option value="Create New Account">--Create New Account--</option>
                            <option value="American Studies Department">American Studies Department</option>
                            <option value="Comparative Literature Department">Comparative Literature Department</option>
                            <option value="English Department">English Department</option>
                            <option value="Ezra Cornell Club">Ezra Cornell Club</option>
                            <option value="French Department">French  Department</option>
                            <option value="Philosophy Department">Philosophy Department</option>
                        </select>
                    <p class="form_feedback  <?php echo ($show_account_feedback) ? '' : 'hidden'; ?>">Please select an account to be charged, or select "Create New Account."</p>
                </div>

                <div>
                    <label for="contactEmail">Email: </label>
                    <input type="email" id="contactEmail" name="contactEmail" placeholder="full address" value="<?php echo htmlspecialchars($email); ?>" required/>
                    <p class="form_feedback <?php echo ($show_email_feedback) ? '' : 'hidden'; ?>">Please enter a valid email address.</p>
                </div>

                <div>
                    <label for="contactPhone">Phone Number: </label>
                    <input id="contactPhone" name="contactPhone" placeholder="XXXXXXXXXX" value="<?php echo htmlspecialchars($phone); ?>" required/>
                    <p class="form_feedback <?php echo ($show_phone_feedback) ? '' : 'hidden'; ?>">Please enter a phone number in case we need to contact you (no dashes please).</p>
                </div>

                <div>
                    <label for="date">Date: </label>
                    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" required>
                    <p class="form_feedback <?php echo ($show_date_feedback) ? '' : 'hidden'; ?>">Please select the date for when the order should be filled.</p>
                </div>

                <div>
                    <label for="time">Pickup Time: </label>
                    <input type="time" id="time" name="time"
                            min="07:30" max="18:00" value="<?php echo htmlspecialchars($pickup); ?>" required>
                    <p class="form_feedback <?php echo ($show_pickup_feedback) ? '' : 'hidden'; ?>">Please select a time during open hours (7:30 AM-6:00 PM).</p>
                </div>

                <div>
                    <label for="orderDetails">Order Details: </label>
                    <input type="text" id="orderDetails" name="orderDetails"
                            value="<?php echo htmlspecialchars($request); ?>" required>
                    <p class="form_feedback <?php echo ($show_request_feedback) ? '' : 'hidden'; ?>">Please let us know what you'd like to order.</p>
                </div>

                <div>
                    <button type="submit" class="submit">Submit Order</button>
                </div>
                </form>

            <?php } else { ?>
                <h2>Order placed for <?php echo htmlspecialchars($account) ?> </h2>
                <ul>
                    <li>Email: <?php echo htmlspecialchars($email)?></li>
                    <li>Phone: <?php echo htmlspecialchars($phone)?></li>
                    <li>Date: <?php echo htmlspecialchars($date)?></li>
                    <li>Pickup Time: <?php echo htmlspecialchars($pickup)?></li>
                    <li>Order Details: <?php echo htmlspecialchars($request)?></li>
                </ul>
            <?php } ?>
        </section>

    <?php include("includes/footer.php");?>

</body>
</html>
