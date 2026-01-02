<?php
// SCMS Version 1.0 - Member events page - N.G.Kaween Newmal
require_once "config/config.php";
require_once "includes/auth.php";

check_login();
check_member();

$pageTitle = "Events";
include "includes/header.php";
include "includes/navbar_member.php";

$userId  = $_SESSION['user_id'];
$message = "";

// Register for event
if (isset($_GET['register'])) {
    $eventId = (int) $_GET['register'];

    // Check already registered
    $check = $conn->query(
        "SELECT id FROM event_registrations WHERE event_id = $eventId AND user_id = $userId"
    );
    if ($check->num_rows === 0) {
        $conn->query(
            "INSERT INTO event_registrations (event_id, user_id) VALUES ($eventId, $userId)"
        );
        $message = "You are registered for the event.";
    } else {
        $message = "You are already registered.";
    }
}

$events = $conn->query(
    "SELECT * FROM events ORDER BY event_date DESC, event_time DESC"
);

$myEvents = $conn->query(
    "SELECT e.* FROM events e
     INNER JOIN event_registrations r ON e.id = r.event_id
     WHERE r.user_id = $userId
     ORDER BY e.event_date DESC"
);
?>
<main class="content">
    <h1>Events</h1>

    <?php if ($message !== ""): ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <section class="two-columns">
        <div>
            <h2>Available Events</h2>
            <table class="data-table">
                <thead>
                <tr>
                    <th>Name</th><th>Date</th><th>Time</th><th>Location</th><th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($e = $events->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($e['name']); ?></td>
                        <td><?php echo $e['event_date']; ?></td>
                        <td><?php echo $e['event_time']; ?></td>
                        <td><?php echo htmlspecialchars($e['location']); ?></td>
                        <td>
                            <a class="btn-small" href="?register=<?php echo $e['id']; ?>">
                                Register
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div>
            <h2>My Registered Events</h2>
            <table class="data-table">
                <thead>
                <tr><th>Name</th><th>Date</th><th>Time</th><th>Location</th></tr>
                </thead>
                <tbody>
                <?php while ($e = $myEvents->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($e['name']); ?></td>
                        <td><?php echo $e['event_date']; ?></td>
                        <td><?php echo $e['event_time']; ?></td>
                        <td><?php echo htmlspecialchars($e['location']); ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php include "includes/footer.php"; ?>
