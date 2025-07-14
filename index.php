<?php

require_once 'db.php';

$clients_sql = "SELECT id, name FROM clients ORDER BY name ASC";
$clients_result = mysqli_query($conn, $clients_sql);
$clients = [];
while ($client_row = mysqli_fetch_assoc($clients_result)) {
    $clients[] = $client_row;
}

$cfes_sql = "
    SELECT cfes.*, clients.name AS client_name, clients.rut AS client_rut
    FROM cfes
    JOIN clients ON cfes.client_id = clients.id
    ORDER BY cfes.issue_date DESC
";
$cfes_result = mysqli_query($conn, $cfes_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CFE Manager</title>
    <link rel="stylesheet" href="style.css?v=1.8">
</head>
<body>

    <header>
        <nav>
            <a href="index.php" class="nav-brand">CFE Manager</a>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="support.php">Support Tickets</a></li>
            </ul>
        </nav>
    </header>

    <main>

        <?php
            if (isset($_GET['import_status'])) {
                if ($_GET['import_status'] === 'success') {
                    $imported = (int)$_GET['imported'];
                    $skipped = (int)$_GET['skipped'];
                    echo "<div class='alert alert-success'>Import successful! $imported CFEs were created. $skipped rows were skipped (client RUT not found).<span class='alert-close'>×</span></div>";
                } else {
                    echo "<div class='alert alert-error'>Error during file upload. Please try again.<span class='alert-close'>×</span></div>";
                }
            }
        ?>

        <section class="card">
            <h2>Create New CFE</h2>
            <form action="create_cfe.php" method="POST" class="cfe-form">
                <div class="form-group">
                    <label for="client_id">Client:</label>
                    <select name="client_id" id="client_id" required>
                        <option value="">-- Select a Client --</option>
                        <?php
                        foreach ($clients as $client) {
                            echo '<option value="' . htmlspecialchars($client['id']) . '">'
                               . htmlspecialchars($client['name'])
                               . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="issue_date">Issue Date:</label>
                    <input type="date" name="issue_date" id="issue_date" required>
                </div>
                <div class="form-group">
                    <label for="amount">Amount:</label>
                    <input type="number" name="amount" id="amount" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="button-primary">Create CFE</button>
                </div>
            </form>
        </section>

        <section class="card" style="margin-top: 30px;">
            <h2>Import CFEs from CSV</h2>

            <form action="import_csv.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="csv_file">Select CSV File to Upload:</label>
                    <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="button-primary">Import File</button>
                </div>
            </form>
            <p style="margin-top:15px; font-size: 0.9em; color: #555;">
                <strong>Note:</strong> The CSV file must have 3 columns in this order: <strong>client_rut,issue_date,amount</strong> (without a header row).
            </p>
        </section>

        <section class="card" style="margin-top: 30px;">
            <h2>Issued CFE List</h2>

            <div class="export-container">
                <a href="export_csv.php" class="button-secondary">Export to CSV</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>RUT</th>
                        <th>Issue Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($cfes_result) > 0) {
                        while($row = mysqli_fetch_assoc($cfes_result)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['client_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['client_rut']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['issue_date']) . "</td>";
                            echo "<td>$" . number_format($row['amount'], 2) . "</td>";
                            
                            echo "<td id='status-" . $row['id'] . "'>" . htmlspecialchars($row['status']) . "</td>";
                            
                            echo "<td id='actions-" . $row['id'] . "'>";
                            if ($row['status'] === 'Pending') {
                                echo "<button class='button-process' data-id='" . $row['id'] . "'>Process</button>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No CFEs registered yet.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>

</body>
<script src="script.js?v=1.2"></script>
</html>

<?php
mysqli_close($conn);
?>