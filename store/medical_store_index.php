<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>MedEase Store - Order Medicines</title>
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background: linear-gradient(135deg, #e8ecff, #f8faff);
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 100vh;
    }

    header {
      width: 100%;
      background: #4f46e5;
      color: white;
      text-align: center;
      padding: 30px 0;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    header h1 {
      font-size: 2.2em;
      margin: 0;
      letter-spacing: 1px;
    }

    .main-container {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      flex-wrap: wrap;
      gap: 30px;
      margin: 50px auto;
      padding: 0 20px;
      max-width: 1200px;
    }

    .card {
      background: white;
      border-radius: 18px;
      width: 380px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      text-align: center;
      padding-bottom: 25px;
    }

    .card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.18);
    }

    .card img {
      width: 100%;
      height: 220px;
      object-fit: cover;
    }

    .card-content {
      padding: 22px;
    }

    .card h2 {
      font-size: 1.4em;
      color: #1f2937;
      margin-bottom: 12px;
    }

    .card p {
      color: #4b5563;
      font-size: 0.96em;
      margin-bottom: 20px;
      line-height: 1.6;
    }

    .btn {
      display: inline-block;
      padding: 12px 20px;
      background: #4f46e5;
      color: white;
      text-decoration: none;
      font-weight: 600;
      border-radius: 8px;
      transition: background 0.3s ease;
      margin: 7px;
      width: 70%;
    }

    .btn:hover {
      background: #3730a3;
    }

    footer {
      text-align: center;
      color: #6b7280;
      margin-top: 40px;
      padding-bottom: 20px;
      font-size: 0.9em;
    }

    @media (max-width: 1100px) {
      .main-container {
        flex-direction: column;
        align-items: center;
      }
    }
  </style>
</head>
<body>

  <header>
    <h1>💊 Welcome to MedEase Store</h1>
    <p>Your one-stop solution for ordering medicines online</p>
  </header>

  <div class="main-container">

    <!-- Combined Single Category Card -->
    <div class="card">
      <img src="img/medicine 1.png" alt="Order Medicine">
      <div class="card-content">
        <h2>🛒 Order Your Medicines</h2>
        <p>
          Search and order medicines instantly or upload your doctor’s prescription. 
          Fast, easy & secure—everything in one place.
        </p>

        <!-- Buttons -->
        <a href="order.php" class="btn">🔍 Search & Order Medicine</a>
        <a href="uploadmedicineimg.php" class="btn">📸 Upload Prescription</a>
      </div>
    </div>

  </div>

  <footer>
    &copy; 2025 MedEase Store | All Rights Reserved
  </footer>

</body>
</html>
