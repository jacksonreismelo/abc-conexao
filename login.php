<?php
require 'db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $pdo = getConnection();
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = :username AND password = :password');
    $stmt->execute(['username' => $username, 'password' => $password]);
    $admin = $stmt->fetch();

    if ($admin) {
        $_SESSION['admin'] = $admin['id'];
        header('Location: list.php');
        exit();
    } else {
        $error = 'Invalid username or password';
    }
}
?>

<?php include 'header.php'; ?>
<h2>Login</h2>
<form method="post">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>
    <br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <br>
    <button type="submit">Login</button>
    <?php if (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>
</form>
<?php include 'footer.php'; ?>
