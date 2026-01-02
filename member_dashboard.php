<?php 
// SCMS Version 1.0 - Member dashboard - N.G.Kaween Newmal
require_once "config/config.php";
require_once "includes/auth.php";

check_login();
check_member();

$pageTitle = "Member Dashboard";

include "includes/header.php";
include "includes/navbar_member.php";

// Simple counts for member
$userId = $_SESSION['user_id'];

$myEvents = $conn->query(
    "SELECT COUNT(*) AS c FROM event_registrations WHERE user_id = $userId"
)->fetch_assoc()['c'];

$myRes = $conn->query(
    "SELECT COUNT(*) AS c FROM equipment_reservations WHERE user_id = $userId"
)->fetch_assoc()['c'];

// Message stats for member dashboard - messages sent by admin to this member
$msgCountRow = $conn->query(
    "SELECT COUNT(*) AS c
     FROM messages m
     INNER JOIN users u ON m.sender_id = u.id
     WHERE m.receiver_id = $userId
       AND u.role = 'admin'"
)->fetch_assoc();
$msgCount = $msgCountRow['c'];

$recentMessages = $conn->query(
    "SELECT m.id, m.subject, m.body, m.created_at, u.username AS sender
     FROM messages m
     INNER JOIN users u ON m.sender_id = u.id
     WHERE m.receiver_id = $userId
       AND u.role = 'admin'
     ORDER BY m.created_at DESC
     LIMIT 5"
);
?>
<main class="dashboard">
    <section class="dashboard-header">
        <h1>Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
        <p>Welcome to Sports Club Management System</p>
    </section>

    <section class="card-grid">
        <a href="member_events.php" class="dash-card">
            <h3>My Events</h3>
            <p class="number"><?php echo $myEvents; ?></p>
            <span>View and register for events</span>
        </a>
        <a href="member_equipment.php" class="dash-card">
            <h3>My Reservations</h3>
            <p class="number"><?php echo $myRes; ?></p>
            <span>Reserve equipment</span>
        </a>
        <a href="messages.php" class="dash-card">
            <h3>Messages</h3>
            <p class="number"><?php echo $msgCount; ?></p>
            <span>Messages from admin</span>
        </a>
    </section>

    <!-- Recent messages from admin -->
    <section style="margin-top: 24px;">
        <h2>Recent Messages from Admin</h2>
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
                                <input type="hidden" name="from" value="member_dashboard.php">
                                <button type="submit" class="btn-small">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No messages received from admin yet.</p>
        <?php endif; ?>
    </section>

    <section class="dashboard-footer-note">
        <p>Designed by <strong>N.G.kaween Newmal</strong></p>
    </section>
</main>
<?php include "includes/footer.php"; ?>
