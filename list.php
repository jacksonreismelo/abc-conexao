<?php
require 'db.php';

session_start();

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

$pdo = getConnection();
$stmt = $pdo->query('SELECT * FROM guests');
$guests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>
<h2>Guest List</h2>
<table>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Address</th>
    </tr>
    <?php foreach ($guests as $guest): ?>
        <tr>
            <td><?php echo htmlspecialchars($guest['name']); ?></td>
            <td><?php echo htmlspecialchars($guest['email']); ?></td>
            <td><?php echo htmlspecialchars($guest['phone']); ?></td>
            <td><?php echo htmlspecialchars($guest['address']); ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php include 'footer.php'; ?>