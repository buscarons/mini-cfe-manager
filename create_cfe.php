<?php

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $client_id = $_POST['client_id'] ?? null;
    $issue_date = $_POST['issue_date'] ?? null;
    $amount = $_POST['amount'] ?? null;

    if ($client_id && $issue_date && $amount) {
        
        $sql = "INSERT INTO cfes (client_id, issue_date, amount) VALUES (?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "isd", $client_id, $issue_date, $amount);

            mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);
        } else {
            die("Error preparing statement: " . mysqli_error($conn));
        }
    }
}

header("Location: index.php");
exit(); 

?>
