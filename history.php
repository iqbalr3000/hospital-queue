<?php
include 'queue-system.php';

$hospitalSystem = new HospitalQueueSystem();
$history = $hospitalSystem->getHistory();

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Pasien</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <nav class="navbar container">
            <div class="logo">🏥 Hospital Queue</div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="history.php">History</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1>History Pasien</h1>
        <input type="text" id="search" placeholder="Cari Nama Pasien">

        <table>
            <thead>
                <tr>
                    <th>Nama Pasien</th>
                    <th>Waktu Dilayani</th>
                </tr>
            </thead>
            <tbody id="historyList">
                <?php if (!empty($history)) : ?>
                    <?php foreach ($history as $record) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['name']); ?></td>
                            <td><?php echo $record['time']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="2" style="text-align: center; font-weight: bold;">Belum ada riwayat pasien</td>
                    </tr>
                <?php endif; ?>
            </tbody>

        </table>
    </div>

    <script>
        document.getElementById('search').addEventListener('input', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#historyList tr');

            rows.forEach(row => {
                let name = row.cells[0]?.textContent.toLowerCase();
                row.style.display = name && name.includes(filter) ? '' : 'none';
            });
        });
    </script>
</body>

</html>