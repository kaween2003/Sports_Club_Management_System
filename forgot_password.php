<?php
// SCMS Version 1.0 - Password recovery demo page - N.G.Kaween Newmal
require_once "config/config.php";

$pageTitle = "Forgot Password";
$message   = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? "");

    if ($email !== "") {
        // Only check if email exists; in real system send email
        $sql  = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Password reset link is sent to your email (demo message).";
        } else {
            $message = "Email not found.";
        }
    }
}

include "includes/header.php";
?>
<div class="center-screen">
    <div class="auth-card">
        <h1>Sports Club Management System</h1>
        <h2>Forgot Password</h2>

        <?php if ($message !== ""): ?>
            <div class="alert"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Registered Email</label>
            <input type="email" name="email" required>

            <button type="submit" class="btn-primary">Send Reset Link</button>

            <p class="small-links">
                <a href="index.php">Back to Login</a>
            </p>
        </form>
    </div>
</div>
<?php include "includes/footer.php"; ?>
