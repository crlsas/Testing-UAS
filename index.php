<?php 
session_start();

// Proteksi Halaman
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// 1. Menghitung Total Jenis Produk
$q_total_produk = mysqli_query($conn, "SELECT COUNT(*) as total FROM produk");
$res_total_produk = mysqli_fetch_assoc($q_total_produk);
$total_jenis = $res_total_produk['total'];

// 2. Menghitung Total Stok Fisik
$q_total_stok = mysqli_query($conn, "SELECT SUM(stok_sistem) as total_stok FROM produk");
$res_total_stok = mysqli_fetch_assoc($q_total_stok);
$total_stok_fisik = $res_total_stok['total_stok'] ?: 0;

// 3. Menghitung Barang Kritis (Logika diperbaiki: Membandingkan stok_sistem dengan stok_minimum)
$q_kritis = mysqli_query($conn, "SELECT COUNT(*) as total FROM produk WHERE stok_sistem < stok_minimum");
$res_kritis = mysqli_fetch_assoc($q_kritis);
$total_kritis = $res_kritis['total'];

// 4. Mengambil 5 Produk Terbaru (PASTIKAN stok_minimum ikut dipanggil)
$recent_items = mysqli_query($conn, "SELECT id_produk, nama_produk, stok_sistem, stok_minimum FROM produk ORDER BY id_produk DESC LIMIT 5");
?>

<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - VMW Carlos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  </head>
  <body class="bg-gray-100 flex">
    
    <aside class="w-64 bg-blue-900 h-screen sticky top-0 text-white p-6 shadow-xl flex flex-col">
      <div class="flex-grow">
          <h1 class="text-2xl font-bold mb-10 flex items-center"><i class="fas fa-boxes-stacked mr-3 text-yellow-400"></i> VMW</h1>
          <nav class="space-y-4">
            <a href="index.php" class="block bg-blue-800 p-3 rounded-lg border-l-4 border-yellow-400 font-semibold"> <i class="fas fa-chart-line mr-2"></i> Dashboard </a>
            <a href="inventory.php" class="block p-3 hover:bg-blue-800 rounded-lg transition"> <i class="fas fa-box-open mr-2"></i> Inventaris </a>
            <a href="opname.php" class="block p-3 hover:bg-blue-800 rounded-lg transition"> <i class="fas fa-clipboard-check mr-2"></i> Stock Opname </a>
            <a href="sales.php" class="block p-3 hover:bg-blue-800 rounded-lg transition"> <i class="fas fa-shopping-cart mr-2"></i> Penjualan </a>
            <a href="reports.php" class="block p-3 hover:bg-blue-800 rounded-lg transition"> <i class="fas fa-file-contract mr-2"></i> Laporan </a>
          </nav>
      </div>

      <div class="border-t border-blue-800 pt-4">
          <a href="login.php" onclick="return confirm('Apakah anda yakin ingin keluar?')" class="block p-3 text-red-400 hover:bg-red-600 hover:text-white rounded-lg transition font-bold">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
          </a>
      </div>
    </aside>

    <main class="flex-1 p-8">
      <header class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Ringkasan Gudang</h2>
            <p class="text-gray-500 text-sm italic">Status Database: Terhubung</p>
        </div>
        <div class="flex items-center space-x-4">
          <span class="text-gray-500 font-medium"><?php echo date('l, d F Y'); ?></span>
          <div class="bg-white p-2 rounded-full shadow-sm px-4 text-blue-900 font-bold border border-blue-100">
            <i class="fas fa-user-circle mr-2"></i> Admin <?php echo $_SESSION['nama'] ?? $_SESSION['username']; ?>
          </div>
        </div>
      </header>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-xl shadow-sm border-b-4 border-blue-500 hover:shadow-md transition">
          <div class="text-blue-500 mb-2"><i class="fas fa-box fa-2x"></i></div>
          <p class="text-sm text-gray-500 uppercase tracking-wider">Total Produk</p>
          <h3 class="text-3xl font-bold"><?php echo $total_jenis; ?></h3>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-b-4 border-green-500 hover:shadow-md transition">
          <div class="text-green-500 mb-2"><i class="fas fa-cubes fa-2x"></i></div>
          <p class="text-sm text-gray-500 uppercase tracking-wider">Total Stok Fisik</p>
          <h3 class="text-3xl font-bold"><?php echo $total_stok_fisik; ?></h3>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-b-4 border-red-500 hover:shadow-md transition">
          <div class="text-red-500 mb-2"><i class="fas fa-exclamation-triangle fa-2x"></i></div>
          <p class="text-sm text-gray-500 uppercase tracking-wider">Stok Kritis</p>
          <h3 class="text-3xl font-bold text-red-600"><?php echo $total_kritis; ?></h3>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-b-4 border-yellow-500 hover:shadow-md transition">
          <div class="text-yellow-500 mb-2"><i class="fas fa-history fa-2x"></i></div>
          <p class="text-sm text-gray-500 uppercase tracking-wider">Sesi Login</p>
          <h3 class="text-xl font-bold text-yellow-600"><?php echo $_SESSION['username']; ?></h3>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
          <h4 class="font-bold text-gray-700">5 Produk Terakhir Ditambahkan</h4>
          <a href="inventory.php" class="text-blue-600 hover:underline text-sm font-bold">Kelola Semua <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <table class="w-full text-left border-collapse">
          <thead class="bg-white text-xs uppercase text-gray-400">
            <tr>
              <th class="p-4 border-b">Nama Barang</th>
              <th class="p-4 border-b text-center">Stok Sistem</th>
              <th class="p-4 border-b text-right">Status Ambalan</th>
            </tr>
          </thead>
          <tbody>
            <?php if(mysqli_num_rows($recent_items) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($recent_items)): ?>
                <tr class="hover:bg-blue-50/50 border-b last:border-0 transition">
                    <td class="p-4 text-gray-700 font-medium"><?php echo $row['nama_produk']; ?></td>
                    <td class="p-4 text-center font-bold text-blue-800"><?php echo $row['stok_sistem']; ?></td>
                    <td class="p-4 text-right">
                        <?php 
                        // Logic perbaikan status: bandingkan stok_sistem dengan stok_minimum
                        // Gunakan operator < (kurang dari)
                        if ($row['stok_sistem'] < $row['stok_minimum']): 
                        ?>
                            <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-bold uppercase">
                                Restock Segera
                            </span>
                        <?php else: ?>
                            <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs font-bold uppercase">
                                Stok Aman
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="p-10 text-center text-gray-400 italic text-sm">Belum ada data di database.</td>
                </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </body>
</html>