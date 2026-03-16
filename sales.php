<?php
session_start();

// 1. PROTEKSI HALAMAN (Sama dengan Dashboard/index.php)
if (!isset($_SESSION['username'])) { 
    header("Location: login.php"); 
    exit(); 
}

include 'config.php';

// 2. LOGIKA PROSES PENJUALAN
if (isset($_POST['proses_jual'])) {
    $id_produk = $_POST['id_produk'];
    $jumlah = $_POST['jumlah'];
    
    // Cek apakah stok cukup
    $cek_stok = mysqli_query($conn, "SELECT stok_sistem FROM produk WHERE id_produk = '$id_produk'");
    $stok_sekarang = mysqli_fetch_assoc($cek_stok)['stok_sistem'];

    if ($stok_sekarang >= $jumlah) {
        // Kurangi stok di tabel produk
        mysqli_query($conn, "UPDATE produk SET stok_sistem = stok_sistem - $jumlah WHERE id_produk = '$id_produk'");
        
        // Catat di tabel penjualan (Pastikan kolom id_produk adalah Foreign Key)
        mysqli_query($conn, "INSERT INTO penjualan (id_produk, jumlah_jual) VALUES ('$id_produk', '$jumlah')");
        
        // Catat di riwayat_stok agar muncul di tabel 'Aktivitas Terakhir' Dashboard
        mysqli_query($conn, "INSERT INTO riwayat_stok (id_produk, tipe_transaksi, jumlah, keterangan) 
                             VALUES ('$id_produk', 'Keluar', '$jumlah', 'Penjualan Retail')");

        echo "<script>alert('Penjualan Berhasil!'); window.location='sales.php';</script>";
    } else {
        echo "<script>alert('Gagal! Stok tidak mencukupi.');</script>";
    }
}

// Ambil data produk yang stoknya masih ada
$produk = mysqli_query($conn, "SELECT * FROM produk WHERE stok_sistem > 0 ORDER BY nama_produk ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penjualan - VMW Carlos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex">

    <aside class="w-64 bg-blue-900 h-screen sticky top-0 text-white p-6 shadow-xl flex flex-col">
      <div class="flex-grow">
          <h1 class="text-2xl font-bold mb-10 flex items-center"><i class="fas fa-boxes-stacked mr-3 text-yellow-400"></i> VMW</h1>
          <nav class="space-y-4">
            <a href="index.php" class="block p-3 hover:bg-blue-800 rounded-lg transition"> <i class="fas fa-chart-line mr-2"></i> Dashboard </a>
            <a href="inventory.php" class="block p-3 hover:bg-blue-800 rounded-lg transition"> <i class="fas fa-box-open mr-2"></i> Inventaris </a>
            <a href="opname.php" class="block p-3 hover:bg-blue-800 rounded-lg transition"> <i class="fas fa-clipboard-check mr-2"></i> Stock Opname </a>
            <a href="sales.php" class="block bg-blue-800 p-3 rounded-lg border-l-4 border-yellow-400 font-semibold"> <i class="fas fa-shopping-cart mr-2"></i> Penjualan </a>
            <a href="reports.php" class="block p-3 hover:bg-blue-800 rounded-lg transition"> <i class="fas fa-file-contract mr-2"></i> Laporan </a>
          </nav>
      </div>
      <div class="border-t border-blue-800 pt-4">
          <a href="login.php" onclick="return confirm('Logout?')" class="block p-3 text-red-400 hover:bg-red-600 hover:text-white rounded-lg transition font-bold text-sm">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
          </a>
      </div>
    </aside>

    <main class="flex-1 p-8">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">Transaksi Penjualan</h2>
                <p class="text-gray-500 text-sm italic">Input pengeluaran barang retail</p>
            </div>
            <div class="flex items-center space-x-4">
              <div class="bg-white p-2 rounded-full shadow-sm px-4 text-blue-900 font-bold border border-blue-100">
                <i class="fas fa-user-circle mr-2"></i> Admin <?php echo $_SESSION['nama_lengkap']; ?>
              </div>
            </div>
        </header>

        <div class="max-w-2xl bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <form action="sales.php" method="POST" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-widest">Pilih Barang</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-box"></i>
                        </span>
                        <select name="id_produk" required class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 transition cursor-pointer">
                            <option value="">-- Pilih Produk Tersedia --</option>
                            <?php while($row = mysqli_fetch_assoc($produk)): ?>
                                <option value="<?php echo $row['id_produk']; ?>">
                                    <?php echo $row['nama_produk']; ?> (Stok: <?php echo $row['stok_sistem']; ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-widest">Jumlah Unit</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-sort-numeric-up"></i>
                        </span>
                        <input type="number" name="jumlah" required min="1" placeholder="Masukkan jumlah unit" 
                               class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>
                </div>

                <button type="submit" name="proses_jual" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-100 transition transform active:scale-95">
                    <i class="fas fa-check-circle mr-2"></i> Konfirmasi & Potong Stok
                </button>
            </form>
        </div>
    </main>
</body>
</html>