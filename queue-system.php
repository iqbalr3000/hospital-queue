<?php
session_start(); // Memulai sesi untuk menyimpan antrian agar tidak hilang setelah refresh

// Class untuk mengelola antrian menggunakan array
class Queue
{
    private $queue = []; // Array untuk menyimpan antrian pasien

    // Konstruktor untuk memuat antrian dari sesi jika sudah ada
    public function __construct()
    {
        if (!isset($_SESSION['queue'])) {
            $_SESSION['queue'] = []; // Jika sesi belum ada, buat array kosong
        }
        $this->queue = $_SESSION['queue']; // Muat antrian dari sesi
    }

    // Menambahkan pasien ke antrian
    public function enqueue($item)
    {
        array_push($this->queue, $item); // Menambahkan pasien ke belakang antrian
        $_SESSION['queue'] = $this->queue; // Simpan antrian terbaru ke sesi
    }

    // Menghapus pasien dari antrian (FIFO - First In First Out)
    public function dequeue()
    {
        if (!$this->isEmpty()) {
            $dequeued =  !$this->isEmpty() ? array_shift($this->queue) : null; // Menghapus pasien pertama dalam antrian
            $_SESSION['queue'] = $this->queue; // Simpan antrian terbaru ke sesi
            return $dequeued;
        }
        return null; // Jika antrian kosong, kembalikan null
    }

    // Mengecek apakah antrian kosong
    public function isEmpty()
    {
        return empty($this->queue);
    }

    // Menghitung jumlah pasien dalam antrian
    public function size()
    {
        return count($this->queue);
    }

    // Menampilkan daftar antrian pasien
    public function displayQueue()
    {
        if ($this->isEmpty()) {
            echo "<p>Antrian kosong.</p>";
        } else {
            echo "<ul>";
            foreach ($this->queue as $index => $patient) {
                echo "<li>" . ($index + 1) . ". $patient</li>"; // Menampilkan pasien dengan nomor urut
            }
            echo "</ul>";
        }
    }
}

// Class untuk mengelola sistem antrian rumah sakit
class HospitalQueueSystem
{
    private $queue; // Properti untuk menyimpan objek Queue
    private $historyFile = 'history.json';

    // Konstruktor untuk menginisialisasi antrian
    public function __construct()
    {
        $this->queue = new Queue();
    }

    // Fungsi untuk menambahkan pasien ke dalam antrian
    public function addPatient($name)
    {
        $this->queue->enqueue($name);
    }

    // Fungsi untuk melayani pasien dengan mengeluarkannya dari antrian
    public function servePatient()
    {
        $servedPatient = $this->queue->dequeue();
        if (is_string($servedPatient) && !empty(trim($servedPatient))) {
            $this->saveToHistory($servedPatient);
        }

        return null;
    }


    // Fungsi untuk menampilkan daftar pasien dalam antrian
    public function viewQueue()
    {
        $this->queue->displayQueue();
    }

    public function saveToHistory($name)
    {
        if (is_string($name) && !empty(trim($name))) {
            $history = $this->getHistory();
            $history[] = [
                'name' => $name,
                'time' => date('Y-m-d H:i:s')
            ];
            file_put_contents($this->historyFile, json_encode($history, JSON_PRETTY_PRINT));
        }
    }

    public function getHistory()
    {
        if (file_exists($this->historyFile)) {
            return json_decode(file_get_contents($this->historyFile), true);
        }
        return [];
    }

    // Fungsi untuk mengembalikan objek antrian (getter)
    public function getQueue()
    {
        return $this->queue;
    }
}
