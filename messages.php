<?php
// SCMS Version 1.0 - Simple messaging page - N.G.Kaween Newmal
require_once "config/config.php";
require_once "includes/auth.php";

check_login();

$pageTitle = "Messages";

if ($_SESSION['user_role'] === 'admin') {
    include "includes/header.php";
    include "includes/navbar_admin.php";
} else {
    include "includes/header.php";
    include "includes/navbar_member.php";
}

$userId  = $_SESSION['user_id'];
$message = "";

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiverId = (int) ($_POST['receiver_id'] ?? 0);
    $subject    = trim($_POST['subject'] ?? "");
    $body       = trim($_POST['body'] ?? "");

    if ($receiverId > 0 && $subject !== "" && $body !== "") {
        $sql  = "INSERT INTO messages (sender_id, receiver_id, subject, body) VALUES (?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $userId, $receiverId, $subject, $body);
        if ($stmt->execute()) {
            $message = "Message sent.";
        } else {
            $message = "Error sending message.";
        }
    } else {
        $message = "Please complete all fields.";
    }
}

// For member: send only to admins
// For admin: send to any user
if ($_SESSION['user_role'] === 'member') {
    $receivers = $conn->query("SELECT id, username FROM users WHERE role = 'admin'");
} else {
    $receivers = $conn->query("SELECT id, username FROM users WHERE id <> $userId");
}

// Inbox messages (received by this user)
$inbox = $conn->query(
    "SELECT m.id, m.subject, m.body, m.created_at, u.username AS sender
     FROM messages m
     INNER JOIN users u ON m.sender_id = u.id
     WHERE m.receiver_id = $userId
     ORDER BY m.created_at DESC"
);

// Sent messages (sent by this user)
$sent = $conn->query(
    "SELECT m.id, m.subject, m.body, m.created_at, u.username AS receiver
     FROM messages m
     INNER JOIN users u ON m.receiver_id = u.id
     WHERE m.sender_id = $userId
     ORDER BY m.created_at DESC"
);
?>
<main class="content">
    <h1>Messages</h1>

    <?php if ($message !== ""): ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <section class="two-columns">
        <div>
            <h2>Send Message</h2>
            <form method="post" class="simple-form">
                <label>To</label>
                <select name="receiver_id">
                    <option value="">Select User</option>
                    <?php while ($u = $receivers->fetch_assoc()): ?>
                        <option value="<?php echo $u['id']; ?>">
                            <?php echo htmlspecialchars($u['username']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label>Subject</label>
                <input type="text" name="subject" required>

                <label>Message</label>
                <textarea name="body" rows="4" required></textarea>

                <button type="submit" class="btn-primary">Send</button>
            </form>
        </div>

        <div>
            <h2>Inbox (Messages you received)</h2>
            <table class="data-table">
                <thead>
                <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($inbox && $inbox->num_rows > 0): ?>
                    <?php while ($m = $inbox->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($m['sender']); ?></td>
                            <td><?php echo htmlspecialchars($m['subject']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($m['body'])); ?></td>
                            <td><?php echo $m['created_at']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4">No messages received yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section style="margin-top: 24px;">
        <h2>Sent Messages (Messages you posted)</h2>
        <table class="data-table">
            <thead>
            <tr>
                <th>To</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Date</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($sent && $sent->num_rows > 0): ?>
                <?php while ($m = $sent->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($m['receiver']); ?></td>
                        <td><?php echo htmlspecialchars($m['subject']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($m['body'])); ?></td>
                        <td><?php echo $m['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">No messages sent yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>
<?php include "includes/footer.php"; ?>
