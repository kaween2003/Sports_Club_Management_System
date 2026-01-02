<?php
// SCMS Version 1.0 - Admin dashboard - N.G.Kaween Newmal
require_once "config/config.php";
require_once "includes/auth.php";

check_login();
check_admin();

$pageTitle = "Admin Dashboard";

include "includes/header.php";
include "includes/navbar_admin.php";

// Simple stats for cards
$userCount      = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$eventCount     = $conn->query("SELECT COUNT(*) AS c FROM events")->fetch_assoc()['c'];
$equipmentCount = $conn->query("SELECT COUNT(*) AS c FROM equipment")->fetch_assoc()['c'];
$regCount       = $conn->query("SELECT COUNT(*) AS c FROM event_registrations")->fetch_assoc()['c'];

// Message stats for admin dashboard - messages sent by members to this admin
$adminId = (int) $_SESSION['user_id'];

$msgCountRow = $conn->query(
    "SELECT COUNT(*) AS c
     FROM messages m
     INNER JOIN users u ON m.sender_id = u.id
     WHERE m.receiver_id = $adminId
       AND u.role = 'member'"
)->fetch_assoc();
$msgCount = $msgCountRow['c'];

$recentMessages = $conn->query(
    "SELECT m.id, m.subject, m.body, m.created_at, u.username AS sender
     FROM messages m
     INNER JOIN users u ON m.sender_id = u.id
     WHERE m.receiver_id = $adminId
       AND u.role = 'member'
     ORDER BY m.created_at DESC
     LIMIT 5"
);
?>
<main class="dashboard">
    <section class="dashboard-header">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
        <p>Sports Club Management System - Admin Panel</p>
    </section>

    <section class="card-grid">
        <a href="admin_users.php" class="dash-card">
            <h3>Total Users</h3>
            <p class="number"><?php echo $userCount; ?></p>
            <span>Manage members and admins</span>
        </a>
        <a href="admin_events.php" class="dash-card">
            <h3>Events</h3>
            <p class="number"><?php echo $eventCount; ?></p>
            <span>Create and manage events</span>
        </a>
        <a href="admin_equipment.php" class="dash-card">
            <h3>Equipment Items</h3>
            <p class="number"><?php echo $equipmentCount; ?></p>
            <span>Handle club equipment</span>
        </a>
        <a href="admin_reports.php" class="dash-card">
            <h3>Event Registrations</h3>
            <p class="number"><?php echo $regCount; ?></p>
            <span>View basic reports</span>
        </a>
        <a href="messages.php" class="dash-card">
            <h3>Messages</h3>
            <p class="number"><?php echo $msgCount; ?></p>
            <span>Messages from members</span>
        </a>
    </section>

    <!-- Recent messages from members -->
    <section style="margin-top: 24px;">
        <h2>Recent Messages from Members</h2>
        <?php if ($recentMessages && $recentMessages->num_rows > 0): ?>
            <table class="data-table">
                <thead>
                <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($m = $recentMessages->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($m['sender']); ?></td>
                        <td><?php echo htmlspecialchars($m['subject']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($m['body'])); ?></td>
                        <td><?php echo $m['created_at']; ?></td>
                        <td>
                            <form method="post" action="delete_message.php"
                                  onsubmit="return confirm('Delete this message?');">
                                <input type="hidden" name="message_id" value="<?php echo $m['id']; ?>">
                                <input type="hidden" name="from" value="admin_dashboard.php">
                                <button type="submit" class="btn-small">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No messages received from members yet.</p>
        <?php endif; ?>
    </section>

    <section class="dashboard-footer-note">
        <p>Designed by <strong>N.G.kaween Newmal</strong></p>
    </section>
</main>
<?php include "includes/footer.php"; ?>
