<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order Medicine by Prescription</title>
  <style>
    :root {
      --bg1: #0f172a;
      --bg2: #1e293b;
      --primary: #6366f1;
      --accent: #06b6d4;
      --success: #22c55e;
      --text-light: #f1f5f9;
      --muted: #94a3b8;
    }

    * {
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }

    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, var(--bg1), var(--bg2));
      color: var(--text-light);
      padding: 20px;
    }

    .container {
      width: 95%;
      max-width: 750px;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 20px;
      padding: 30px;
      backdrop-filter: blur(10px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
      animation: fadeIn 1s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    h1 {
      text-align: center;
      color: var(--primary);
      margin-bottom: 10px;
    }

    p {
      text-align: center;
      color: var(--muted);
      margin-bottom: 25px;
      font-size: 14px;
    }

    form {
      display: grid;
      gap: 20px;
    }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 6px;
    }

    input[type="file"],
    input[type="text"],
    input[type="tel"],
    input[type="number"],
    textarea {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: none;
      outline: none;
      background: rgba(255, 255, 255, 0.1);
      color: var(--text-light);
      font-size: 15px;
    }

    ::file-selector-button {
      background: var(--primary);
      border: none;
      color: white;
      padding: 8px 12px;
      border-radius: 6px;
      cursor: pointer;
      margin-right: 10px;
      font-weight: 500;
    }

    textarea { resize: none; height: 80px; }

    .preview { text-align: center; margin-top: 10px; }

    .preview img {
      width: 140px;
      height: 120px;
      object-fit: cover;
      border-radius: 10px;
      border: 2px solid rgba(255, 255, 255, 0.2);
    }

    button {
      padding: 12px;
      font-size: 16px;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      background: linear-gradient(90deg, var(--primary), var(--accent));
      color: white;
      font-weight: 600;
      transition: transform 0.2s, box-shadow 0.2s;
    }

    button:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
    }

    .success {
      text-align: center;
      color: var(--success);
      font-weight: 600;
      margin-top: 15px;
    }

    .price-box {
      background: rgba(255, 255, 255, 0.1);
      padding: 12px;
      border-radius: 10px;
      text-align: center;
      margin-top: 10px;
      display: none;
    }

    .price-box span {
      font-size: 20px;
      color: var(--accent);
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Order Medicine</h1>
    <p>Upload your prescription and enter delivery details</p>

    <form id="orderForm">
      
      <label>Full Name</label>
      <input type="text" id="fullname" required />

      <label>Mobile Number</label>
      <input type="tel" id="mobile" pattern="[0-9]{10}" required />

      <label>Full Address</label>
      <textarea id="address" required></textarea>

      <label>City</label>
      <input type="text" id="city" required />

      <label>State</label>
      <input type="text" id="state" required />

      <label>Pincode</label>
      <input type="number" id="pincode" required />

      <label>Upload Prescription</label>
      <input type="file" id="prescription" accept="image/*" required />
      <div class="preview" id="preview"></div>

      <!-- PRICE BOX -->
      <div class="price-box" id="priceBox">
        Estimated Price: <span id="priceValue"></span> ₹
      </div>

      <label>Quantity (Tablets)</label>
      <input type="number" id="quantity" min="1" required />

      <!-- ONLY CASH ON DELIVERY -->
      <label>Payment Method</label>
      <div style="background: rgba(255,255,255,0.1); padding:10px; border-radius:10px;">
        <input type="radio" name="payment" value="Cash on Delivery" checked /> Cash on Delivery
      </div>

      <button type="submit">Place Order</button>
      <div class="success" id="successMsg"></div>
    </form>
  </div>

  <script>
    const preview = document.getElementById("preview");
    const priceBox = document.getElementById("priceBox");
    const priceValue = document.getElementById("priceValue");

    // Show image + Random price
    document.getElementById("prescription").addEventListener("change", (e) => {
      const file = e.target.files[0];
      if (file) {
        // Image preview
        const img = document.createElement("img");
        img.src = URL.createObjectURL(file);
        preview.innerHTML = "";
        preview.appendChild(img);

        // Generate random estimated price
        const price = Math.floor(Math.random() * (699 - 199 + 1)) + 199;
        priceValue.textContent = price;
        priceBox.style.display = "block";
      }
    });

    // Generate Order ID
    function generateOrderID() {
      const date = new Date().toISOString().slice(2, 10).replace(/-/g, "");
      const rand = Math.random().toString(36).substring(2, 6).toUpperCase();
      return `ORD-${date}-${rand}`;
    }

    // Form submit
    document.getElementById("orderForm").addEventListener("submit", function(e) {
      e.preventDefault();

      const orderID = generateOrderID();
      document.getElementById("successMsg").innerHTML =
        `✅ Order <b>${orderID}</b> placed successfully!`;

      this.reset();
      preview.innerHTML = "";
      priceBox.style.display = "none";
    });
  </script>

</body>
</html>
