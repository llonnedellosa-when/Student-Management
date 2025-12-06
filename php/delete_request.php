<?php
// php/delete.php
include "config.php";

// Check if ID exists via POST
if (!isset($_POST['id'])) {
    header("Location: ../php/requests.php");
    exit;
}

$id = intval($_POST['id']); // sanitize input

$stmt = $conn->prepare("DELETE FROM service_requests WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: ../php/requests.php?message=deleted");
} else {
    echo "Error deleting record.";
}

$stmt->close();
$conn->close();
?>
