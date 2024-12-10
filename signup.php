<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignUp Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        #form {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        #heading {
            text-align: center;
        }
    </style>
</head>
<body>
    
    <div id="form">
        <h1 id="heading">SignUp Form</h1><br>
        <?php
        include("connection.php");

        $error_username = $error_email = $error_password = $error_phone = $error_domain = ""; // Initialize error variables

        if (isset($_POST['submit'])) {
            $username = mysqli_real_escape_string($conn, $_POST['user']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password = mysqli_real_escape_string($conn, $_POST['pass']);
            $cpassword = mysqli_real_escape_string($conn, $_POST['cpass']);
            $phone = mysqli_real_escape_string($conn, $_POST['phone']);

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_email = "Invalid email format.";
            }

            // Validate email domain
            $allowed_domains = ['gmail.com', 'yahoo.com', 'psgitech.ac.in'];
            $email_parts = explode('@', $email);
            $domain = isset($email_parts[1]) ? $email_parts[1] : null;

            if (!in_array($domain, $allowed_domains)) {
                $error_domain = "Email domain not allowed.";
            }

            // Validate phone number (must be exactly 10 digits)
            if (!preg_match('/^\d{10}$/', $phone)) {
                $error_phone = "Phone number must be exactly 10 digits.";
            }

            $sql = "SELECT * FROM signup WHERE username='$username'";
            $result = mysqli_query($conn, $sql);
            $count_user = mysqli_num_rows($result);

            if ($count_user > 0) {
                $error_username = "Username already exists.";
            }

            $sql = "SELECT * FROM signup WHERE email='$email'";
            $result = mysqli_query($conn, $sql);
            $count_email = mysqli_num_rows($result);

            if ($count_email > 0) {
                $error_email = "Email already exists.";
            }

            if ($password != $cpassword) {
                $error_password = "Passwords do not match.";
            }

            // Proceed with registration if no errors
            if (empty($error_username) && empty($error_email) && empty($error_password) && empty($error_phone) && empty($error_domain)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO signup(username, email, phone, password) VALUES('$username', '$email', '$phone', '$hash')";
                $result = mysqli_query($conn, $sql);
                if ($result) {
                    header("Location: login.php");
                    exit(); // Ensure script terminates after redirection
                } else {
                    $error_message = "Registration failed. Please try again later.";
                }
            }
        }
        ?>
        
        <form name="form" action="signup.php" method="POST">
            <div class="mb-3">
                <label for="user" class="form-label">Enter Username:</label>
                <input type="text" class="form-control <?php echo !empty($error_username) ? 'is-invalid' : ''; ?>" id="user" name="user" value="<?php echo isset($_POST['user']) ? $_POST['user'] : ''; ?>" required>
                <div class="invalid-feedback"><?php echo $error_username; ?></div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Enter Email:</label>
                <input type="email" class="form-control <?php echo (!empty($error_email) || !empty($error_domain)) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
                <?php if (!empty($error_email)): ?>
                    <div class="invalid-feedback"><?php echo $error_email; ?></div>
                <?php elseif (!empty($error_domain)): ?>
                    <div class="invalid-feedback"><?php echo $error_domain; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Enter Phone Number:</label>
                <input type="tel" class="form-control <?php echo !empty($error_phone) ? 'is-invalid' : ''; ?>" id="phone" name="phone" pattern="[0-9]{10}" value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : ''; ?>" required>
                <div class="invalid-feedback"><?php echo $error_phone; ?></div>
            </div>
            <div class="mb-3">
                <label for="pass" class="form-label">Password:</label>
                <input type="password" class="form-control" id="pass" name="pass" required>
            </div>
            <div class="mb-3">
                <label for="cpass" class="form-label">Confirm Password:</label>
                <input type="password" class="form-control <?php echo !empty($error_password) ? 'is-invalid' : ''; ?>" id="cpass" name="cpass" required>
                <div class="invalid-feedback"><?php echo $error_password; ?></div>
            </div>
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary" name="submit">SignUp</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script>
        // JavaScript to clear error messages after displaying
        document.addEventListener('DOMContentLoaded', function() {
            const invalidInputs = document.querySelectorAll('.is-invalid');
            invalidInputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                    this.closest('.mb-3').querySelector('.invalid-feedback').textContent = '';
                });
            });
        });
    </script>
</body>
</html>
