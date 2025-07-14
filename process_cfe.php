<?php

require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {

    $id = $_POST['id'] ?? $_GET['id'] ?? null;

    if ($id) {
        $new_status = (rand(0, 1) === 1) ? 'Approved' : 'Error';

        $sql = "UPDATE cfes SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $new_status, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            echo json_encode(['success' => true, 'new_status' => $new_status]);
            exit();
        }
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid Request']);
?>