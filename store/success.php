<?php
// success.php
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$sms = isset($_GET['sms']) ? ($_GET['sms'] === '1') : false;

// Get previous page URL safely
$previous_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.html';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Order Success</title>

<!-- Auto redirect after 30 seconds -->
<meta http-equiv="refresh" content="30; url=<?php echo $previous_page; ?>">

<style>
  body {
    font-family: Poppins, sans-serif;
    background: linear-gradient(135deg, #eef2ff, #fff);
    padding: 40px;
  }

  .box {
    max-width: 600px;
    margin: auto;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
  }

  .ok {
    color: green;
    font-weight: 700;
  }

  .info {
    margin-top: 10px;
    padding: 10px;
    background: #e0f2fe;
    color: #0369a1;
    border-radius: 8px;
    font-weight: 600;
    text-align: center;
  }
</style>
</head>

<body>

  <div class="box">
    <h2 class="ok">✅ Order Placed Successfully!</h2>

    <p>Order ID: <strong><?php echo htmlspecialchars($order_id); ?></strong></p>
    <p>SMS Notification Sent: <strong><?php echo $sms ? 'Yes' : 'No'; ?></strong></p>

    <div class="info">
      ⏳ You will be redirected to the <strong>previous page</strong> in 30 seconds...
    </div>

    <p style="margin-top:20px;">
      If not redirected, <a href="medical_store_index.php">click here</a>.
    </p>
  </div>

</body>
</html>
