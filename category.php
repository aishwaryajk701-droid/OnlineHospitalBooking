 include("connection");
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Category</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        table {
            margin: auto; /* center table */
            border-collapse: collapse;
        }
        th, td {
            padding: 20px;
        }
        .select-btn {
            background-color: #f2f2f2;
            color: #333;
            border: none;
            padding: 15px 40px;
            font-size: 1.1em;
            cursor: pointer;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .select-btn.selected {
            background-color: #1976d2; /* blue */
            color: #fff;
        }
    </style>
</head>
<body>
    include("connection");
    <h2>Select Your User Type</h2>
    <table>
        <tr>
            <td>
                <button class="select-btn" onclick="selectType(this, 'doctor')">Doctor</button>
            </td>
            <td>
                <button class="select-btn" onclick="selectType(this, 'patient')">Patient</button>
            </td>
            <td>
                <button class="select-btn" onclick="selectType(this, 'admin')">Admin</button>
            </td>
        </tr>
    </table>
    <script>
        function selectType(btn, type) {
            // Remove .selected from all buttons
            document.querySelectorAll('.select-btn').forEach(b => b.classList.remove('selected'));
            // Add .selected to clicked button
            btn.classList.add('selected');
            // Redirect to login page after selection (optional)
            // window.location.href = 'login.php?type=' + type;
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Category</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        table {
            margin: auto; /* center table */
            border-collapse: collapse;
        }
        th, td {
            padding: 20px;
        }
        .select-btn {
            background-color: #f2f2f2;
            color: #333;
            border: none;
            padding: 15px 40px;
            font-size: 1.1em;
            cursor: pointer;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .select-btn.selected {
            background-color: #1976d2; /* blue */
            color: #fff;
        }
    </style>
</head>
<body>
    <h2>Select Your User Type</h2>
    <table>
        <tr>
            <td>
                <button class="select-btn" onclick="selectType(this, 'doctor')">Doctor</button>
            </td>
            <td>
                <button class="select-btn" onclick="selectType(this, 'patient')">Patient</button>
            </td>
            <td>
                <button class="select-btn" onclick="selectType(this, 'admin')">Admin</button>
            </td>
        </tr>
    </table>
    <script>
        function selectType(btn, type) {
            // Remove .selected from all buttons
            document.querySelectorAll('.select-btn').forEach(b => b.classList.remove('selected'));
            // Add .selected to clicked button
            btn.classList.add('selected');
            // Redirect to login page after selection (optional)
            // window.location.href = 'login.php?type=' + type;
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Category</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        table {
            margin: auto; /* center table */
            border-collapse: collapse;
        }
        th, td {
            padding: 20px;
        }
        .select-btn {
            background-color: #f2f2f2;
            color: #333;
            border: none;
            padding: 15px 40px;
            font-size: 1.1em;
            cursor: pointer;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .select-btn.selected {
            background-color: #1976d2; /* blue */
            color: #fff;
        }
    </style>
</head>
<body>
    <h2>Select Your User Type</h2>
    <table>
        <tr>
            <td>
                <button class="select-btn" onclick="selectType(this, 'doctor')">Doctor</button>
            </td>
            <td>
                <button class="select-btn" onclick="selectType(this, 'patient')">Patient</button>
            </td>
            <td>
                <button class="select-btn" onclick="selectType(this, 'admin')">Admin</button>
            </td>
        </tr>
    </table>
    <script>
        function selectType(btn, type) {
            // Remove .selected from all buttons
            document.querySelectorAll('.select-btn').forEach(b => b.classList.remove('selected'));
            // Add .selected to clicked button
            btn.classList.add('selected');
            // Redirect to login page after selection (optional)
            // window.location.href = 'login.php?type=' + type;
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Category</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        table {
            margin: auto; /* center table */
            border-collapse: collapse;
        }
        th, td {
            padding: 20px;
        }
        .select-btn {
            background-color: #f2f2f2;
            color: #333;
            border: none;
            padding: 15px 40px;
            font-size: 1.1em;
            cursor: pointer;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .select-btn.selected {
            background-color: #1976d2; /* blue */
            color: #fff;
        }
    </style>
</head>
<body>
    <h2>Select Your User Type</h2>
    <table>
        <tr>
            <td>
                <button class="select-btn" onclick="selectType(this, 'doctor')">Doctor</button>
            </td>
            <td>
                <button class="select-btn" onclick="selectType(this, 'patient')">Patient</button>
            </td>
            <td>
                <button class="select-btn" onclick="selectType(this, 'admin')">Admin</button>
            </td>
        </tr>
    </table>
    <script>
        function selectType(btn, type) {
            // Remove .selected from all buttons
            document.querySelectorAll('.select-btn').forEach(b => b.classList.remove('selected'));
            // Add .selected to clicked button
            btn.classList.add('selected');
            // Redirect to login page after selection (optional)
            // window.location.href = 'login.php?type=' + type;
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Category</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        table {
            margin: auto; /* center table */
            border-collapse: collapse;
        }
        th, td {
            padding: 20px;
        }
        .select-btn {
            background-color: #f2f2f2;
            color: #333;
            border: none;
            padding: 15px 40px;
            font-size: 1.1em;
            cursor: pointer;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .select-btn.selected {
            background-color: #1976d2; /* blue */
            color: #fff;
        }
    </style>
</head>
<body>
    <h2>Select Your User Type</h2>
    <table>
        <tr>
            <td>
                <button class="select-btn" onclick="selectType(this, 'doctor')">Doctor</button>
            </td>
            <td>
                <button class="select-btn" onclick="selectType(this, 'patient')">Patient</button>
            </td>
            <td>
                <button class="select-btn" onclick="selectType(this, 'admin')">Admin</button>
            </td>
        </tr>
    </table>
    <script>
        function selectType(btn, type) {
            // Remove .selected from all buttons
            document.querySelectorAll('.select-btn').forEach(b => b.classList.remove('selected'));
            // Add .selected to clicked button
            btn.classList.add('selected');
            // Redirect to login page after selection (optional)
            // window.location.href = 'login.php?type=' + type;
        }
    </script>
</body>
</html>
