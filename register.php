<?php
include 'db/config.php';

$name = $surname = $student_num = $contact = $module_code = $email = $password = $confirm_password = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $surname = trim($_POST["surname"]);
    $student_num = trim($_POST["student_num"]);
    $contact = trim($_POST["contact"]);
    $module_code = trim($_POST["module_code"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($name) || empty($surname) || empty($student_num) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields marked * are required.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    $check_sql = "SELECT * FROM users WHERE student_num = ? OR email = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $student_num, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errors[] = "Student number or Email already registered.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (student_num, name, surname, contact_num, module_code, email, password)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $student_num, $name, $surname, $contact, $module_code, $email, $hashed_password);
        if ($stmt->execute()) {
            // Redirect to login with message
            header("Location: login.php?registered=1");
            exit();
        } else {
            $errors[] = "Error: " . $stmt->error;
        }
    }
}

include 'includes/header.php';

if (!empty($errors)) {
    echo "<ul style='color:red;'>";
    foreach ($errors as $error) {
        echo "<li>" . htmlspecialchars($error) . "</li>";
    }
    echo "</ul>";
}
?>

<form method="post" action="">
    <label>Name*: <input type="text" name="name" required></label><br>
    <label>Surname*: <input type="text" name="surname" required></label><br>
    <label>Student No*: <input type="text" name="student_num" required></label><br>
    <label>Contact: <input type="text" name="contact"></label><br>
    <label>Module Code: <input type="text" name="module_code"></label><br>
    <label>Email*: <input type="email" name="email" required></label><br>
    <label>Password*: <input type="password" name="password" required></label><br>
    <label>Confirm Password*: <input type="password" name="confirm_password" required></label><br>
    <input type="submit" value="Register">
</form>

<p style="text-align:center;">
    Already have an account? <a href="login.php">Login here</a>
</p>

<?php include 'includes/footer.php'; ?>
