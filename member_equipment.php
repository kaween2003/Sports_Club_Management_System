<?php
// SCMS Version 1.0 - Member equipment page - N.G.Kaween Newmal
require_once "config/config.php";
require_once "includes/auth.php";

check_login();
check_member();

$pageTitle = "Equipment";
include "includes/header.php";
include "includes/navbar_member.php";

$userId  = $_SESSION['user_id'];
$message = "";

// Reserve equipment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eqId   = (int) ($_POST['equipment_id'] ?? 0);
    $qty    = (int) ($_POST['quantity'] ?? 0);
    $date   = $_POST['reserved_date'] ?? date('Y-m-d');

    if ($eqId > 0 && $qty > 0) {
        $eq = $conn->query("SELECT available_quantity FROM equipment WHERE id = $eqId")->fetch_assoc();
        if ($eq && $eq['available_quantity'] >= $qty) {
            $conn->query(
                "INSERT INTO equipment_reservations (equipment_id, user_id, quantity, reserved_date)
                 VALUES ($eqId, $userId, $qty, '$date')"
            );
            $conn->query(
                "UPDATE equipment SET available_quantity = available_quantity - $qty WHERE id = $eqId"
            );
            $message = "Equipment reserved.";
        } else {
            $message = "Not enough quantity available.";
        }
    } else {
        $message = "Please select equipment and quantity.";
    }
}

$equipment = $conn->query("SELECT * FROM equipment ORDER BY name ASC");

$myRes = $conn->query(
    "SELECT er.id, er.quantity, er.reserved_date, eq.name
     FROM equipment_reservations er
     INNER JOIN equipment eq ON er.equipment_id = eq.id
     WHERE er.user_id = $userId
     ORDER BY er.reserved_date DESC"
);
?>
<main class="content">
    <h1>Equipment</h1>

    <?php if ($message !== ""): ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <section class="two-columns">
        <div>
            <h2>Reserve Equipment</h2>
            <form method="post" class="simple-form">
                <label>Equipment</label>
                <select name="equipment_id">
                    <option value="">Select</option>
                    <?php while ($e = $equipment->fetch_assoc()): ?>
                        <option value="<?php echo $e['id']; ?>">
                            <?php echo htmlspecialchars($e['name']); ?> 
                            (Available: <?php echo $e['available_quantity']; ?>)
                        </option>
                    <?php endwhile; ?>
                </select>

                <label>Quantity</label>
                <input type="number" name="quantity" min="1" value="1">

                <label>Reserved Date</label>
                <input type="date" name="reserved_date" value="<?php echo date('Y-m-d'); ?>">

                <button type="submit" class="btn-primary">Reserve</button>
            </form>
        </div>

        <div>
            <h2>My Reservations</h2>
            <table class="data-table">
                <thead>
                <tr><th>Equipment</th><th>Quantity</th><th>Date</th></tr>
                </thead>
                <tbody>
                <?php while ($r = $myRes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['name']); ?></td>
                        <td><?php echo $r['quantity']; ?></td>
                        <td><?php echo $r['reserved_date']; ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php include "includes/footer.php"; ?>
