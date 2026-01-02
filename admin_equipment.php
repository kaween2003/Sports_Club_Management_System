<?php
// SCMS Version 1.0 - Admin equipment management - N.G.Kaween Newmal
require_once "config/config.php";
require_once "includes/auth.php";

check_login();
check_admin();

$pageTitle = "Manage Equipment";
include "includes/header.php";
include "includes/navbar_admin.php";

$message = "";

// Add equipment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? "");
    $description = trim($_POST['description'] ?? "");
    $total       = (int) ($_POST['total_quantity'] ?? 0);
    $available   = (int) ($_POST['available_quantity'] ?? $total);

    if ($name !== "") {
        $sql  = "INSERT INTO equipment (name, description, total_quantity, available_quantity)
                 VALUES (?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $name, $description, $total, $available);

        if ($stmt->execute()) {
            $message = "Equipment added.";
        } else {
            $message = "Error adding equipment.";
        }
    } else {
        $message = "Please enter equipment name.";
    }
}

// Delete equipment
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id > 0) {
        $conn->query("DELETE FROM equipment WHERE id = $id");
        $message = "Equipment deleted.";
    }
}

$items = $conn->query("SELECT * FROM equipment ORDER BY name ASC");
?>
<main class="content">
    <h1>Manage Equipment</h1>

    <?php if ($message !== ""): ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <section class="two-columns">
        <div>
            <h2>Add New Equipment</h2>
            <form method="post" class="simple-form">
                <label>Name</label>
                <input type="text" name="name" required>

                <label>Description</label>
                <textarea name="description" rows="3"></textarea>

                <label>Total Quantity</label>
                <input type="number" name="total_quantity" min="0" value="0">

                <label>Available Quantity</label>
                <input type="number" name="available_quantity" min="0" value="0">

                <button type="submit" class="btn-primary">Save Equipment</button>
            </form>
        </div>

        <div>
            <h2>Equipment List</h2>
            <table class="data-table">
                <thead>
                <tr>
                    <th>#</th><th>Name</th><th>Total</th><th>Available</th><th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($i = $items->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $i['id']; ?></td>
                        <td><?php echo htmlspecialchars($i['name']); ?></td>
                        <td><?php echo $i['total_quantity']; ?></td>
                        <td><?php echo $i['available_quantity']; ?></td>
                        <td>
                            <a class="btn-small" href="?delete=<?php echo $i['id']; ?>"
                               onclick="return confirm('Delete this item?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php include "includes/footer.php"; ?>
