<?php
session_start();

// 1. PROTEKSI HALAMAN
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// 2. LOGIKA UPDATE STOK (OPNAME)
if (isset($_POST['proses_opname'])) {
    $id_produk = $_POST['id_produk'];
    $stok_fisik = $_POST['stok_fisik'];
    $stok_sistem = $_POST['stok_sistem'];
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    // Update stok utama di tabel produk
    $query_update = "UPDATE produk SET stok_sistem = '$stok_fisik' WHERE id_produk = '$id_produk'";
    
    // Catat ke riwayat sebagai 'Penyesuaian'
    $query_log = "INSERT INTO riwayat_stok (id_produk, tipe_transaksi, jumlah, keterangan) 
                  VALUES ('$id_produk', 'Penyesuaian', '$stok_fisik', '$keterangan')";

    if (mysqli_query($conn, $query_update) && mysqli_query($conn, $query_log)) {
        echo "<script>alert('Opname Berhasil! Stok telah disesuaikan.'); window.location='opname.php';</script>";
    }
}

$ambil_produk = mysqli_query($conn, "SELECT * FROM produk ORDER BY nama_produk ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Stock Opname - VMW Carlos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex">

    <aside class="w-64 bg-blue-900 h-screen sticky top-0 text-white p-6 shadow-xl flex flex-col">
      <div class="flex-grow">
          <h1 class="text-2xl font-bold mb-10 flex items-center"><i class="fas fa-boxes-stacked mr-3 text-yellow-400"></i> VMW</h1>
          <nav class="space-y-4">
            <a href="index.php" class="block p-3 hover:bg-blue-800 rounded-lg transition"> <i class="fas fa-chart-line mr-2"></i> Dashboard </a>
            <a href="inventory.php" class="block p-3 hover:bg-blue-800 rounded-lg transition"> <i class="fas fa-box-open mr-2"></i> Inventaris </a>
            <a href="opname.php" class="block bg-blue-800 p-3 rounded-lg border-l-4 border-yellow-400 font-semibold"> <i class="fas fa-clipboard-check mr-2"></i> Stock Opname </a>
            <a href="sales.php" class="block p-3 hover:bg-blue-800 rounded-lg transition"> <i class="fas fa-shopping-cart mr-2"></i> Penjualan </a>
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
                <h2 class="text-3xl font-bold text-slate-800">Stock Opname</h2>
                <p class="text-slate-500 text-sm">Sesuaikan jumlah stok sistem dengan fisik di gudang</p>
            </div>
            <div class="bg-white p-2 rounded-full shadow-sm px-4 text-blue-900 font-bold border border-blue-100 flex items-center">
               <i class="fas fa-user-circle mr-2"></i> Admin <?php echo $_SESSION['nama'] ?? $_SESSION['username']; ?>
            </div>
        </header>

        <div class="space-y-6">
            <?php while($row = mysqli_fetch_assoc($ambil_produk)): ?>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between hover:shadow-md transition duration-300">
                
                <div class="flex-1">
                    <span class="inline-block px-2 py-1 bg-blue-50 text-blue-600 text-[10px] font-bold rounded mb-2 uppercase">
                        <?php echo $row['lokasi_rak']; ?>
                    </span>
                    <h4 class="text-xl font-bold text-slate-700"><?php echo $row['nama_produk']; ?></h4>
                    <p class="text-xs text-slate-400 font-medium">ID: #<?php echo $row['id_produk']; ?> | Kategori: <?php echo $row['kategori']; ?></p>
                </div>

                <form action="opname.php" method="POST" class="flex items-center gap-6">
                    <input type="hidden" name="id_produk" value="<?php echo $row['id_produk']; ?>">
                    <input type="hidden" name="stok_sistem" value="<?php echo $row['stok_sistem']; ?>">

                    <div class="bg-slate-50 px-6 py-4 rounded-xl border border-slate-100 flex items-center gap-8">
                        <div class="text-center">
                            <span class="block text-[9px] text-slate-400 uppercase font-bold tracking-wider">Stok Sistem</span>
                            <span class="text-2xl font-black text-slate-800"><?php echo $row['stok_sistem']; ?></span>
                        </div>

                        <div class="flex flex-col gap-2">
                            <input type="number" name="stok_fisik" placeholder="FISIK" required
                                   class="w-32 p-2 border border-slate-200 rounded-lg text-center font-bold text-blue-600 focus:ring-2 focus:ring-blue-400 outline-none">
                            <input type="text" name="keterangan" placeholder="Catatan (Misal: Rusak)"
                                   class="w-48 p-2 border border-slate-200 rounded-lg text-[10px] outline-none focus:border-blue-400">
                        </div>

                        <button type="submit" name="proses_opname" 
                                class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-emerald-100 transition transform active:scale-95">
                            <i class="fas fa-sync-alt text-sm"></i> Update
                        </button>
                    </div>
                </form>
            </div>
            <?php endwhile; ?>
        </div>
    </main>
</body>
</html>