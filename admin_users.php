<?php
// SCMS Version 1.0 - Admin user management - N.G.Kaween Newmal
require_once "config/config.php";
require_once "includes/auth.php";

check_login();
check_admin();

$pageTitle = "Manage Users";
include "includes/header.php";
include "includes/navbar_admin.php";

$message = "";

// Add simple user (admin or member)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? "");
    $email    = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";
    $role     = $_POST['role'] ?? "member";

    if ($username !== "" && $email !== "" && $password !== "") {
        $hash = hash('sha256', $password);
        $sql  = "INSERT INTO users (username, email, password, role) VALUES (?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $hash, $role);

        if ($stmt->execute()) {
            $message = "User added successfully.";
        } else {
            $message = "Error adding user.";
        }
    } else {
        $message = "Please fill all fields for new user.";
    }
}

// Delete user
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id > 0) {
        $conn->query("DELETE FROM users WHERE id = $id AND role <> 'admin'");
        $message = "User deleted.";
    }
}

$users = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
?>
<main class="content">
    <h1>Manage Users</h1>

    <?php if ($message !== ""): ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <section class="two-columns">
        <div>
            <h2>Add New User</h2>
            <form method="post" class="simple-form">
                <label>Username</label>
                <input type="text" name="username" required>

                <label>Email</label>
                <input type="email" name="email" required>

                <label>Password</label>
                <input type="password" name="password" required>

                <label>Role</label>
                <select name="role">
                    <option value="member">Member</option>
                    <option value="admin">Admin</option>
                </select>

                <button type="submit" class="btn-primary">Save User</button>
            </form>
        </div>

        <div>
            <h2>All Users</h2>
            <table class="data-table">
                <thead>
                <tr>
                    <th>#</th><th>Username</th><th>Email</th><th>Role</th><th>Created</th><th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($u = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['username']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><?php echo $u['role']; ?></td>
                        <td><?php echo $u['created_at']; ?></td>
                        <td>
                            <?php if ($u['role'] !== 'admin'): ?>
                                <a class="btn-small" href="?delete=<?php echo $u['id']; ?>" 
                                   onclick="return confirm('Delete this user?');">Delete</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php include "includes/footer.php"; ?>
