<?php
require 'db.php';

session_start();

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $pdo = getConnection();
    $stmt = $pdo->prepare('INSERT INTO guests (name, email, phone, address) VALUES (:name, :email, :phone, :address)');
    $stmt->execute(['name' => $name, 'email' => $email, 'phone' => $phone, 'address' => $address]);

    header('Location: list.php');
    exit();
}
?>

<?php include 'header.php'; ?>
<h2>Register Guest</h2>
<form method="post">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required>
    <br>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    <br>
    <label for="phone">Phone:</label>
    <input type="text" id="phone" name="phone" required>
    <br>
    <label for="address">Address:</label>
    <input type="text" id="address" name="address" required>
    <br>
    <button type="submit">Register</button>
</form>
<?php include 'footer.php'; ?>