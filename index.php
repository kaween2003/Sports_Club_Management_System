<?php
// SCMS Version 1.0 - Login page - N.G.Kaween Newmal
require_once "config/config.php";

$pageTitle = "SCMS Login";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? "");
    $password = $_POST['password'] ?? "";

    if ($username === "" || $password === "") {
        $error = "Please enter username and password.";
    } else {
        $sql  = "SELECT id, username, password, role FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Here we use SHA2 from database for simple demo
            if (hash('sha256', $password) === $row['password']) {
                $_SESSION['user_id']   = $row['id'];
                $_SESSION['user_name'] = $row['username'];
                $_SESSION['user_role'] = $row['role'];

                if ($row['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: member_dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    }
}

include "includes/header.php";
?>
<div class="center-screen">
    <div class="auth-card">
        <h1>Sports Club Management System</h1>
        <h2>Login</h2>

        <?php if ($error !== ""): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit" class="btn-primary">Login</button>

            <p class="small-links">
                <a href="register.php">New member? Register</a> |
                <a href="forgot_password.php">Forgot password?</a>
            </p>
        </form>
    </div>
</div>
<?php include "includes/footer.php"; ?>
