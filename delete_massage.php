<?php
// SCMS Version 1.0 - Delete message script - N.G.Kaween Newmal
require_once "config/config.php";
require_once "includes/auth.php";

check_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $messageId = (int) ($_POST['message_id'] ?? 0);
    // where to go back after delete
    $redirect  = $_POST['from'] ?? 'messages.php';

    if ($messageId > 0) {
        $userId = (int) $_SESSION['user_id'];

        // Check this user is sender or receiver
        $sql  = "SELECT sender_id, receiver_id FROM messages WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $messageId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if ($row['sender_id'] == $userId || $row['receiver_id'] == $userId) {
                // Delete message
                $del = $conn->prepare("DELETE FROM messages WHERE id = ?");
                $del->bind_param("i", $messageId);
                $del->execute();
            }
        }
    }

    header("Location: " . $redirect);
    exit();
}

// If direct access without POST, go back to main Messages page
header("Location: messages.php");
exit();
?>
