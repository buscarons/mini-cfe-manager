<?php

require_once 'db.php';

$sql = "SELECT * FROM support_tickets ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Tickets</title>
    <link rel="stylesheet" href="style.css?v=1.7">
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
        <section class="card">
            <h2>Client Reported Incidents</h2>
            
            <div class="ticket-list">
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while($ticket = mysqli_fetch_assoc($result)): ?>

                        <div class="ticket">
                            <div class="ticket-header">
                                <span class="ticket-subject"><?php echo htmlspecialchars($ticket['subject']); ?></span>
                                <span class="ticket-status status-<?php echo strtolower($ticket['status']); ?>">
                                    <?php echo htmlspecialchars($ticket['status']); ?>
                                </span>
                            </div>
                            <div class="ticket-meta">
                                From: <?php echo htmlspecialchars($ticket['client_email']); ?> | 
                                Received: <?php echo date('Y-m-d H:i', strtotime($ticket['created_at'])); ?>
                            </div>
                            <div class="ticket-body">
                                <p><?php echo nl2br(htmlspecialchars($ticket['message'])); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No support tickets found.</p>
                <?php endif; ?>
            </div>

        </section>
    </main>

</body>
</html>

<?php
mysqli_close($conn);
?>