<?php
// SCMS Version 1.0 - Simple admin reports - N.G.Kaween Newmal
require_once "config/config.php";
require_once "includes/auth.php";

check_login();
check_admin();

$pageTitle = "Reports";
include "includes/header.php";
include "includes/navbar_admin.php";

// Basic counts as simple report
$events = $conn->query("SELECT e.name, COUNT(r.id) AS registrations
                        FROM events e
                        LEFT JOIN event_registrations r ON e.id = r.event_id
                        GROUP BY e.id
                        ORDER BY e.event_date DESC");

$equipmentUse = $conn->query("SELECT eq.name, SUM(er.quantity) AS total_reserved
                              FROM equipment eq
                              LEFT JOIN equipment_reservations er ON eq.id = er.equipment_id
                              GROUP BY eq.id");
?>
<main class="content">
    <h1>Basic Reports</h1>

    <section>
        <h2>Event Participation</h2>
        <table class="data-table">
            <thead>
            <tr><th>Event</th><th>Registrations</th></tr>
            </thead>
            <tbody>
            <?php while ($row = $events->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo $row['registrations'] ?? 0; ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <section>
        <h2>Equipment Usage</h2>
        <table class="data-table">
            <thead>
            <tr><th>Equipment</th><th>Total Reserved Quantity</th></tr>
            </thead>
            <tbody>
            <?php while ($row = $equipmentUse->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo $row['total_reserved'] ?? 0; ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>
<?php include "includes/footer.php"; ?>
