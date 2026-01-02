<?php
// SCMS Version 1.0 - Admin event management - N.G.Kaween Newmal
require_once "config/config.php";
require_once "includes/auth.php";

check_login();
check_admin();

$pageTitle = "Manage Events";
include "includes/header.php";
include "includes/navbar_admin.php";

$message = "";

// Add new event
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $name        = trim($_POST['name'] ?? "");
    $date        = $_POST['event_date'] ?? "";
    $time        = $_POST['event_time'] ?? "";
    $location    = trim($_POST['location'] ?? "");
    $description = trim($_POST['description'] ?? "");
    $capacity    = (int) ($_POST['capacity'] ?? 0);

    if ($name !== "" && $date !== "" && $time !== "" && $location !== "") {
        $sql  = "INSERT INTO events (name, event_date, event_time, location, description, capacity, created_by)
                 VALUES (?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $createdBy = $_SESSION['user_id'];
        $stmt->bind_param("ssssiii", $name, $date, $time, $location, $description, $capacity, $createdBy);
        if ($stmt->execute()) {
            $message = "Event created.";
        } else {
            $message = "Error creating event.";
        }
    } else {
        $message = "Please fill required event fields.";
    }
}

// Delete event
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id > 0) {
        $conn->query("DELETE FROM events WHERE id = $id");
        $conn->query("DELETE FROM event_registrations WHERE event_id = $id");
        $message = "Event deleted.";
    }
}

$events = $conn->query("SELECT * FROM events ORDER BY event_date DESC, event_time DESC");
?>
<main class="content">
    <h1>Manage Events</h1>

    <?php if ($message !== ""): ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <section class="two-columns">
        <div>
            <h2>Create Event</h2>
            <form method="post" class="simple-form">
                <input type="hidden" name="action" value="add">

                <label>Event Name</label>
                <input type="text" name="name" required>

                <label>Date</label>
                <input type="date" name="event_date" required>

                <label>Time</label>
                <input type="time" name="event_time" required>

                <label>Location</label>
                <input type="text" name="location" required>

                <label>Capacity</label>
                <input type="number" name="capacity" min="0" value="0">

                <label>Description</label>
                <textarea name="description" rows="3"></textarea>

                <button type="submit" class="btn-primary">Save Event</button>
            </form>
        </div>

        <div>
            <h2>Existing Events</h2>
            <table class="data-table">
                <thead>
                <tr>
                    <th>#</th><th>Name</th><th>Date</th><th>Time</th>
                    <th>Location</th><th>Capacity</th><th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($e = $events->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $e['id']; ?></td>
                        <td><?php echo htmlspecialchars($e['name']); ?></td>
                        <td><?php echo $e['event_date']; ?></td>
                        <td><?php echo $e['event_time']; ?></td>
                        <td><?php echo htmlspecialchars($e['location']); ?></td>
                        <td><?php echo $e['capacity']; ?></td>
                        <td>
                            <a class="btn-small" href="?delete=<?php echo $e['id']; ?>"
                               onclick="return confirm('Delete this event?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php include "includes/footer.php"; ?>
