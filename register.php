<?php
include 'koneksi.php';

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if ($password !== $confirm) {
        echo "<script>alert('Password tidak sama!');</script>";
    } else {
        $cek = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
        if (mysqli_num_rows($cek) > 0) {
            echo "<script>alert('Username sudah terpakai, cari yang lain!');</script>";
        } else {
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            
            $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$pass_hash')";
            
            if (mysqli_query($conn, $query)) {
                echo "<script>
                        window.location.href = 'login.php';
                      </script>";
            } else {
                echo "<script>alert('Gagal mendaftar: " . mysqli_error($conn) . "');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokemon Register Page</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/pages/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="register-card">
        
        <h2>Welcome New Trainer!</h2>
        
        <form action="" method="POST"> 
            
            <div class="input-group">
                <label for="username"><i class="fas fa-user vector-icon"></i> Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="input-group">
                <label for="email"><i class="fas fa-envelope vector-icon"></i> Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="input-group">
                <label for="password"><i class="fas fa-key vector-icon"></i> Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="input-group">
                <label for="confirm-password"><i class="fas fa-key vector-icon"></i> Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm_password" required>
            </div>

            <button type="submit" name="register" class="btn-pokeball">
                <span>REGISTER</span>
            </button>

        </form>

        <div class="footer-link">
            <p>Sudah menjadi trainer? <a href="login.php">klik disini!</a></p>
        </div>
    </div>

</body>
</html>