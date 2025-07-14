<?php

require_once 'db.php';

if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
    
    $file_tmp_path = $_FILES['csv_file']['tmp_name'];

    $csv_file = fopen($file_tmp_path, 'r');
    
    $imported_count = 0;
    $skipped_count = 0;

    $find_client_sql = "SELECT id FROM clients WHERE rut = ?";
    $find_client_stmt = mysqli_prepare($conn, $find_client_sql);

    $insert_cfe_sql = "INSERT INTO cfes (client_id, issue_date, amount) VALUES (?, ?, ?)";
    $insert_cfe_stmt = mysqli_prepare($conn, $insert_cfe_sql);

    while (($row = fgetcsv($csv_file)) !== false) {
        
        $client_rut = $row[0];
        $issue_date = $row[1];
        $amount = $row[2];

        mysqli_stmt_bind_param($find_client_stmt, "s", $client_rut);
        mysqli_stmt_execute($find_client_stmt);
        $result = mysqli_stmt_get_result($find_client_stmt);
        
        if ($client_data = mysqli_fetch_assoc($result)) {
            $client_id = $client_data['id'];

            mysqli_stmt_bind_param($insert_cfe_stmt, "isd", $client_id, $issue_date, $amount);
            mysqli_stmt_execute($insert_cfe_stmt);
            $imported_count++;

        } else {
            $skipped_count++;
        }
    }

    fclose($csv_file); 
    mysqli_stmt_close($find_client_stmt);
    mysqli_stmt_close($insert_cfe_stmt);

    header("Location: index.php?import_status=success&imported=$imported_count&skipped=$skipped_count");
    exit();

} else {
    header("Location: index.php?import_status=error");
    exit();
}
?>
