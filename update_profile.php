<?php
session_start();
if (!isset($_SESSION['student_num'])) {
    header("Location: login.php");
    exit();
}

include 'db/config.php';

$student_num = $_POST['student_num'];
$name = $_POST['name'];
$surname = $_POST['surname'];
$contact_num = $_POST['contact_num'];
$module_code = $_POST['module_code'];
$email = $_POST['email'];

$profile_picture = null;

if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $target_dir = "images/";
    $file_name = basename($_FILES["profile_picture"]["name"]);
    $target_file = $target_dir . time() . "_" . $file_name;

    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $valid_extensions = ["jpg", "jpeg", "png", "gif"];

    if (in_array($imageFileType, $valid_extensions)) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $profile_picture = basename($target_file);
        }
    }
}

$sql = "UPDATE users SET name = ?, surname = ?, contact_num = ?, module_code = ?, email = ?" . 
       ($profile_picture ? ", profile_picture = ?" : "") . 
       " WHERE student_num = ?";

if ($profile_picture) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $name, $surname, $contact_num, $module_code, $email, $profile_picture, $student_num);
} else {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $name, $surname, $contact_num, $module_code, $email, $student_num);
}

if ($stmt->execute()) {
    // Update session
    $_SESSION["name"] = $name;
    $_SESSION["surname"] = $surname;
    $_SESSION["contact_num"] = $contact_num;
    $_SESSION["module_code"] = $module_code;
    $_SESSION["email"] = $email;
    if ($profile_picture) {
        $_SESSION["profile_picture"] = $profile_picture;
    }

    header("Location: profile.php");
    exit();
} else {
    echo "Error updating profile: " . $stmt->error;
}
