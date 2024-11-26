<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AccessGuard</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        /* Styling khusus untuk halaman login */
        body {
            background-color: #ffccf2; /* Background warna pink lembut */
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }
        .login-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .login-container h2 {
            color: #ff66b2; /* Warna judul pink */
            font-weight: bold;
        }
        .login-container img {
            width: 120px;
            margin-bottom: 1rem;
        }
        .form-control {
            border-radius: 20px;
        }
        .btn-custom {
            background-color: #ff66b2;
            color: #ffffff;
            border-radius: 20px;
            padding: 10px 20px;
            font-weight: bold;
        }
        .btn-custom:hover {
            background-color: #ff4da6;
        }
        .forgot-password {
            color: #ff66b2;
            text-decoration: none;
        }
        .forgot-password:hover {
            color: #ff4da6;
        }
        .alert {
            font-size: 0.9rem;
            margin-top: 1rem;
            border-radius: 10px;
            padding: 0.75rem;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <!-- Display message if there is any -->
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Icon Image -->
        <img src="assets/images/icon.png" alt="Kooky Icon">
        
        <!-- Title -->
        <h2>Welcome to AccessGuard</h2>
        
        <!-- Login Form -->
        <form action="login_process.php" method="POST">
            <!-- Display error message if there is any -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-custom btn-block">Login</button>
        </form>
        
        <!-- Forgot Password Link -->
        <p class="mt-3">
            <a href="#" class="forgot-password">Forgot Password?</a>
        </p>
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
