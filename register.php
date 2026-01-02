<?php
// SCMS Version 1.0 - Member registration page - N.G.Kaween Newmal
require_once "config/config.php";

$pageTitle = "Member Registration";
$message   = "";
$error     = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? "");
    $email    = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";
    $confirm  = $_POST['confirm_password'] ?? "";

    if ($username === "" || $email === "" || $password === "") {
        $error = "Please fill all fields.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check username or email already used
        $checkSql  = "SELECT id FROM users WHERE username = ? OR email = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ss", $username, $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $error = "Username or email already taken.";
        } else {
            $hash = hash('sha256', $password);

            $sql  = "INSERT INTO users (username, email, password, role) VALUES (?,?,?, 'member')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $email, $hash);

            if ($stmt->execute()) {
                $message = "Registration successful. You can now login.";
            } else {
                $error = "Error saving data.";
            }
        }
    }
}

include "includes/header.php";
?>
<div class="center-screen">
    <div class="auth-card">
        <h1>Sports Club Management System</h1>
        <h2>Member Registration</h2>

        <?php if ($message !== ""): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($error !== ""): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Confirm Password</label>
            <input type="password" name="confirm_password" required>

            <button type="submit" class="btn-primary">Register</button>

            <p class="small-links">
                <a href="index.php">Back to Login</a>
            </p>
        </form>
    </div>
</div>
<?php include "includes/footer.php"; ?>
