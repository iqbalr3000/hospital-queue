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
            <div class="logo">🏥 Klinik Queue</div>
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
            let table = document.getElementById('historyList');
            let rows = Array.from(table.querySelectorAll('tr'));

            // Ambil semua nama pasien dan simpan dalam array dengan index-nya
            let patients = rows.map((row, index) => ({
                name: row.cells[0]?.textContent.toLowerCase(),
                index: index
            }));

            // Urutkan pasien berdasarkan nama (penting untuk Binary Search)
            patients.sort((a, b) => a.name.localeCompare(b.name));

            // Implementasi Binary Search
            function binarySearch(array, key) {
                let left = 0;
                let right = array.length - 1;

                while (left <= right) {
                    let mid = Math.floor((left + right) / 2);
                    let midValue = array[mid].name;

                    if (midValue.startsWith(key)) return mid; // Jika cocok, kembalikan indeks
                    if (midValue < key) left = mid + 1;
                    else right = mid - 1;
                }
                return -1; // Tidak ditemukan
            }

            // Sembunyikan semua baris dulu
            rows.forEach(row => row.style.display = 'none');

            if (filter.trim() !== "") {
                let index = binarySearch(patients, filter);
                if (index !== -1) {
                    // Jika ditemukan, tampilkan semua yang memiliki prefix yang sama
                    let matchName = patients[index].name;
                    rows[patients[index].index].style.display = ''; // Tampilkan hasil pertama

                    // Cek elemen sebelum dan sesudah yang memiliki prefix yang sama
                    let i = index - 1;
                    while (i >= 0 && patients[i].name.startsWith(filter)) {
                        rows[patients[i].index].style.display = '';
                        i--;
                    }
                    i = index + 1;
                    while (i < patients.length && patients[i].name.startsWith(filter)) {
                        rows[patients[i].index].style.display = '';
                        i++;
                    }
                }
            } else {
                // Jika kotak pencarian kosong, tampilkan semua baris
                rows.forEach(row => row.style.display = '');
            }
        });
    </script>

</body>

</html>