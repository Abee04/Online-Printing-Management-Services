<?php
session_start();

$login = false;
$error_username = $error_password = "";

// Hardcoded admin credentials
$admin_username = "admin";
$admin_password = "password";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
        $username = $_POST['user'];
        $password = $_POST['pass'];

        if (empty($username)) {
            $error_username = "Enter username or email!";
        }

        if (empty($password)) {
            $error_password = "Enter password!";
        }

        if (!empty($username) && !empty($password)) {
            if ($username === $admin_username && $password === $admin_password) {
                $_SESSION['username'] = $admin_username; // Store username in session
                $_SESSION['loggedin'] = true;
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error_password = "Invalid username or password! Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            flex-direction: column;
        }
        #form {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        #form h1 {
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            transition: opacity 1s ease-out;
        }
    </style>
</head>
<body>
    <?php include('navbar1.html'); ?>
    <div id="form" class="container">
        <h1 class="text-center">Admin Login</h1>
        <?php if (!empty($error_username)): ?>
            <div id="error_username" class="alert alert-danger"><?php echo $error_username; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_password)): ?>
            <div id="error_password" class="alert alert-danger"><?php echo $error_password; ?></div>
        <?php endif; ?>
        <form name="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" onsubmit="return isValid()">
            <div class="mb-3">
                <label for="user" class="form-label">Username</label>
                <input type="text" id="user" name="user" class="form-control">
            </div>
            <div class="mb-3">
                <label for="pass" class="form-label">Password:</label>
                <input type="password" id="pass" name="pass" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Login</button>
        </form>
    </div>
    <script>
        function isValid() {
            var user = document.getElementById("user").value.trim();
            var pass = document.getElementById("pass").value.trim();

            if (user === "") {
                showAlert("error_username", "Enter username or email!");
                return false;
            }
            if (pass === "") {
                showAlert("error_password", "Enter password!");
                return false;
            }
            return true;
        }

        function showAlert(elementId, message) {
            var alertElement = document.getElementById(elementId);
            alertElement.innerHTML = message;
            alertElement.style.opacity = 1;
            setTimeout(function() {
                alertElement.style.opacity = 0;
            }, 3000);
        }
    </script>
</body>
</html>
