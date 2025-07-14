<?php

require_once 'db.php';

$filename = "cfes_export_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$sql = "
    SELECT 
        clients.name AS client_name,
        clients.rut AS client_rut,
        cfes.issue_date,
        cfes.amount,
        cfes.status
    FROM cfes
    JOIN clients ON cfes.client_id = clients.id
    ORDER BY cfes.issue_date DESC
";
$result = mysqli_query($conn, $sql);

$output = fopen('php://output', 'w');

fputcsv($output, ['Client Name', 'Client RUT', 'Issue Date', 'Amount', 'Status']);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, $row);
    }
}

fclose($output);
exit();

?>
