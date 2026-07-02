<?php
// order.php
// Put this file in htdocs/your_project/order.php
// It shows the form (GET) and handles submission (POST).

require_once 'db.php'; // include DB connection

// === MSG91 CONFIG - EDIT THESE ===
$MSG91_AUTHKEY    = "Samrtmeet0627mmec1234";  // <-- REPLACE with your MSG91 auth key
$MSG91_SENDERID   = "MEDSMS";               // approved sender (for fallback v2)
$MSG91_TEMPLATE_ID = "";                    // optional: set if using v5 template method (like "TPL_XXXXX")
// =================================

$storePhone = "8277670758"; // medical store phone (per your request)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect & validate POST values
    $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $phone    = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $address  = isset($_POST['address']) ? trim($_POST['address']) : '';
    $medicine = isset($_POST['medicine']) ? trim($_POST['medicine']) : '';
    $price    = isset($_POST['price']) ? trim($_POST['price']) : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $store    = isset($_POST['store']) ? trim($_POST['store']) : '';

    // Basic server-side validation
    $errors = [];
    if ($fullname === '') $errors[] = "Full name required.";
    if ($phone === '')    $errors[] = "Phone required.";
    if ($address === '')  $errors[] = "Address required.";
    if ($medicine === '') $errors[] = "Medicine required.";
    if ($price === '')    $errors[] = "Price required.";
    if ($quantity <= 0)   $errors[] = "Quantity must be at least 1.";
    if ($store === '')    $errors[] = "Store required.";

    if (!empty($errors)) {
        // Show first error (you could show all).
        $errMsg = htmlspecialchars($errors[0]);
        echo "<script>alert('Error: $errMsg'); window.history.back();</script>";
        exit;
    }

    // 1) Save order into DB (prepared statement)
    $stmt = $mysqli->prepare("INSERT INTO orders (fullname, phone, address, medicine, price, quantity, store, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }
    $stmt->bind_param("ssssdis", $fullname, $phone, $address, $medicine, $price, $quantity, $store);
    $ok = $stmt->execute();
    if (!$ok) {
        // DB error
        $stmt->close();
        die("Database insert failed: " . $mysqli->error);
    }
    $order_id = $stmt->insert_id;
    $stmt->close();

    // 2) Prepare SMS text (include order id so store can track)
    $smsText = "New Order #$order_id
Name: $fullname
Phone: $phone
Address: $address
Medicine: $medicine
Price: Rs $price
Qty: $quantity
Store: $store";

    // 3) Send SMS via MSG91
    // We try v5 template method if you provided a TEMPLATE ID (recommended), else fallback to v2 style send.
    $sms_sent = false;
    $sms_error = '';

    // --------- v5 template method (recommended if you use templates on MSG91) ----------
    if (!empty($MSG91_TEMPLATE_ID) && !empty($MSG91_AUTHKEY)) {
        // v5 endpoint expects template_id and variables or you can send custom message with "unicode" param as well.
        // Here we send using "sms" list with plain message; MSG91 v5 may require template usage depending on your account.
        $payload = array(
            "sender" => $MSG91_SENDERID,
            "route" => "4",
            "country" => "91",
            "sms" => array(
                array(
                    "to" => array($phone, $storePhone),
                    "message" => $smsText
                )
            )
        );
        $ch = curl_init("https://control.msg91.com/api/v5/sms/send");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "authkey: $MSG91_AUTHKEY",
            "content-type: application/json"
        ));
        $resp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            $sms_error = $err;
        } else {
            $sms_sent = true; // we got a response; you may parse $resp for exact status
        }
    } elseif (!empty($MSG91_AUTHKEY)) {
        // --------- v2 fallback (older style) ----------
        $postData = array(
            "sender" => $MSG91_SENDERID,
            "route" => "4",
            "country" => "91",
            "sms" => array(
                array(
                    "message" => $smsText,
                    "to" => array($phone, $storePhone)
                )
            )
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => "https://api.msg91.com/api/v2/sendsms",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array(
                "authkey: $MSG91_AUTHKEY",
                "content-type: application/json"
            ),
        ));
        $resp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            $sms_error = $err;
        } else {
            $sms_sent = true;
        }
    } else {
        $sms_error = "MSG91 auth key not configured in order.php";
    }

    // You can log $resp or $sms_error into DB if needed.

    // 4) Redirect to success page. We pass short params (order id) so success page can show info.
    // Use header redirect
    header("Location: success.php?order_id=" . urlencode($order_id) . "&sms=" . ($sms_sent ? '1' : '0'));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Search & Order Medicine | MedEase Store</title>
<style>
  body{font-family:Poppins, sans-serif;background:linear-gradient(135deg,#edf2ff,#e0e7ff);padding:30px}
  .order-container{background:#fff;max-width:700px;margin:auto;padding:30px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,0.08)}
  input,select,textarea{width:100%;padding:10px;margin-top:8px;border-radius:8px;border:1px solid #d1d5db;font-size:15px}
  .btn{background:#4f46e5;color:#fff;padding:12px;border-radius:8px;border:none;margin-top:15px;cursor:pointer;font-weight:600}
</style>
</head>
<body>

<div class="order-container">
  <h1>🔍 Search & Order Specific Medicine</h1>
  <p>Fill details — we'll save the order and notify the store by SMS.</p>

  <form method="post" action="">
    <label>Full Name</label>
    <input type="text" name="fullname" required>

    <label>Phone Number</label>
    <input type="tel" name="phone" required placeholder="827xxxxxxx">

    <label>Full Address</label>
    <textarea name="address" rows="3" required></textarea>

    <label>Select Medicine</label>
    <select name="medicine" id="medicineSelect" required>
      <option value="">-- Choose Medicine --</option>
      <option value="Paracetamol 500mg" data-price="45">Paracetamol 500mg (₹45)</option>
      <option value="Ibuprofen 400mg" data-price="30">Ibuprofen 400mg (₹30)</option>
      <option value="Vitamin C 1000mg" data-price="50">Vitamin C 1000mg (₹50)</option>
      <option value="Amoxicillin 250mg" data-price="80">Amoxicillin 250mg (₹80)</option>
      <option value="Cetirizine" data-price="20">Cetirizine (₹20)</option>
    </select>

    <label>Price (₹)</label>
    <input type="text" id="priceBox" name="price" readonly required>

    <label>Quantity</label>
    <input type="number" name="quantity" min="1" value="1" required>

    <label>Select Medical Store</label>
    <select name="store" required>
      <option value="">-- Select Medical Store --</option>
      <option value="Apollo Pharmacy">Apollo Pharmacy</option>
      <option value="MedPlus">MedPlus</option>
      <option value="Wellness Forever">Wellness Forever</option>
      <option value="Local Medical Store">Local Medical Store</option>
    </select>

    <button type="submit" class="btn">🛒 Place Order</button>
  </form>
</div>

<script>
  // Auto price update
  const medSelect = document.getElementById('medicineSelect');
  const priceBox = document.getElementById('priceBox');
  medSelect.addEventListener('change', function(){
    const p = this.options[this.selectedIndex].getAttribute('data-price');
    priceBox.value = p ? p : '';
  });
</script>

</body>
</html>
