<?php
include 'queue-system.php';

$hospitalSystem = new HospitalQueueSystem();

// Menangani input pasien
if (isset($_POST['add_patient']) && !empty($_POST['patient_name'])) {
    $patientName = htmlspecialchars($_POST['patient_name']);
    $hospitalSystem->addPatient($patientName);
}

// Menangani layanan pasien
if (isset($_POST['serve_patient'])) {
    $hospitalSystem->servePatient();
    $hospitalSystem->saveToHistory($hospitalSystem->getQueue());
}

// Menangani pindah pasien
if (isset($_POST['move_patient'])) {
    $hospitalSystem->movePatientBack();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrian Rumah Sakit</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <nav class="navbar container">
            <div class="logo">🏥 Klinik Queue</div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="history.php">History</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1>Antrian Pasien</h1>

        <div class="form-container">
            <form method="POST" action="">
                <label for="patient_name">Nama Pasien:</label>
                <input type="text" id="patient_name" name="patient_name" required placeholder="Masukkan Nama Pasien">
                <button type="submit" name="add_patient" class="btn-primary">Tambah Pasien</button>
            </form>
        </div>

        <div class="queue-container">
            <h2>📋 Antrian Pasien</h2>
            <ul class="queue-list">
                <?php
                $queue = $hospitalSystem->getQueue();
                $totalQueue = $queue->size();

                if ($queue->isEmpty()) {
                    echo "<div class='empty-queue'>
                            <img src='ilustration.png' alt='Antrian Kosong'> 
                            <p>Tidak ada pasien dalam antrian.</p>
                          </div>";
                } else {
                    $index = 1;
                    foreach ($_SESSION['queue'] as $patient) {
                        if ($index > 3) break; // Menampilkan 3 antrian pertama

                        echo "<li class='queue-item'>
                                <span class='queue-number'>$index</span>
                                <span class='queue-name'>$patient</span>
                              </li>";
                        $index++;
                    }

                    // Jika lebih dari 5 pasien, tampilkan pesan tambahan
                    if ($totalQueue > 3) {
                        echo "<p class='more-queue'>+ " . ($totalQueue - 3) . " pasien dalam antrian...</p>";
                    }
                }
                ?>
            </ul>
        </div>

        <div class="action-buttons">
            <form method="POST" action="">
                <button type="submit" name="move_patient" class="btn-secondary"
                    <?php echo ($queue->isEmpty() || count($_SESSION['queue']) < 2) ? 'disabled' : ''; ?>>
                    ↩
                </button>
                <button type="submit" name="serve_patient" class="btn-success" <?php echo ($queue->isEmpty()) ? 'disabled' : ''; ?>>Layani Pasien</button>
            </form>
        </div>

        <div class="queue-status">
            <p>Jumlah Pasien dalam Antrian: <strong><?php echo $totalQueue; ?></strong></p>
        </div>
    </div>
</body>

</html>