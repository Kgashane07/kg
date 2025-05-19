<?php
session_start();
include 'db/config.php';

$email = $password = "";
$login_error = "";

if (isset($_GET['registered']) && $_GET['registered'] == 1) {
    echo '<p class="success-msg">Registration successful! Please log in.</p>';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $login_error = "Both fields are required.";
    } else {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION["student_num"] = $user["student_num"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["surname"] = $user["surname"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["contact_num"] = $user["contact_num"];
                $_SESSION["module_code"] = $user["module_code"];
                $_SESSION["profile_picture"] = $user["profile_picture"];
                header("Location: profile.php");
                exit();
            } else {
                $login_error = "Invalid password.";
            }
        } else {
            $login_error = "No account found with that email.";
        }
    }
}

include 'includes/header.php';

if (!empty($login_error)) {
    echo "<p style='color:red; text-align:center;'>$login_error</p>";
}
?>

<form method="post" action="">
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <input type="submit" value="Login">
</form>

<p style="text-align:center;">
    Not registered yet? <a href="register.php">Click here to register</a>
</p>

<?php include 'includes/footer.php'; ?>
