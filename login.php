<?php
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $row['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            
            if ($row['team'] == NULL) {
                header("Location: choose-character.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        }
    }

    $error = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokemon Login Page</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/pages/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="login-card">
        
        <h2>Welcome Trainer!</h2>

        <?php if(isset($error)) : ?>
            <p style="color: red; text-align: center; font-style: italic;">Username / Password Salah!</p>
        <?php endif; ?>
        
        <form action="" method="POST"> 
            
            <div class="input-group">
                <label for="username"><i class="fas fa-user vector-icon"></i> Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="input-group">
                <label for="password"><i class="fas fa-key vector-icon"></i> Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div style="margin-top: 20px;"></div>

            <button type="submit" name="login" class="btn-pokeball">
                <span>LOGIN</span>
            </button>

        </form>

        <div class="footer-link">
            <p>Daftar jadi trainer baru? <a href="register.php">klik disini!</a></p>
        </div>
    </div>

</body>
</html>