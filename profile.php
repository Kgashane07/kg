<?php
session_start();
if (!isset($_SESSION['student_num'])) {
    header("Location: login.php");
    exit();
}

include 'db/config.php';
$student_num = $_SESSION['student_num'];

$sql = "SELECT * FROM users WHERE student_num = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_num);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

include 'includes/header.php';
?>

<a href="logout.php" style="text-align:center; display:block;">Logout</a>

<?php if ($user['profile_picture']): ?>
    <img src="images/<?php echo htmlspecialchars($user['profile_picture']); ?>" width="100" height="100" alt="Profile Picture">
<?php else: ?>
    <p style="text-align:center;">No profile picture uploaded.</p>
<?php endif; ?>

<form method="post" action="update_profile.php" enctype="multipart/form-data">
    <input type="hidden" name="student_num" value="<?php echo $user['student_num']; ?>">
    <label>Name: <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required></label><br>
    <label>Surname: <input type="text" name="surname" value="<?php echo htmlspecialchars($user['surname']); ?>" required></label><br>
    <label>Contact: <input type="text" name="contact_num" value="<?php echo htmlspecialchars($user['contact_num']); ?>"></label><br>
    <label>Module Code: <input type="text" name="module_code" value="<?php echo htmlspecialchars($user['module_code']); ?>"></label><br>
    <label>Email: <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></label><br>
    <label>Profile Picture: <input type="file" name="profile_picture"></label><br>
    <input type="submit" value="Update Profile">
</form>

<?php include 'includes/footer.php'; ?>
