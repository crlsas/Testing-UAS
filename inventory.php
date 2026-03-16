<?php
session_start();

// Proteksi Halaman
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config.php'; 

// 1. Logika Tambah Produk
if (isset($_POST['save_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['prodName']);
    $cat  = mysqli_real_escape_string($conn, $_POST['prodCat']);
    $qty  = $_POST['prodQty'];
    $rak  = mysqli_real_escape_string($conn, $_POST['prodRak']);

    $query = "INSERT INTO produk (nama_produk, kategori, stok_sistem, lokasi_rak, stok_minimum) 
              VALUES ('$name', '$cat', '$qty', '$rak', 5)";
    
    if (mysqli_query($conn, $query)) {
        header("Location: inventory.php?status=success");
        exit();
    }
}

// 2. Logika Hapus Produk
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM produk WHERE id_produk = $id");
    header("Location: inventory.php");
    exit();
}

// 3. Ambil Data untuk Tabel
$result = mysqli_query($conn, "SELECT * FROM produk ORDER BY id_produk DESC");
?>

<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inventaris - VMW </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  </head>
  <body class="bg-gray-100 flex">
    
    <aside class="w-64 bg-blue-900 h-screen sticky top-0 text-white p-6 shadow-xl flex flex-col">
      <div class="flex-grow">
          <h1 class="text-2xl font-bold mb-10 flex items-center"><i class="fas fa-boxes-stacked mr-3 text-yellow-400"></i> VMW</h1>
          <nav class="space-y-4">
            <a href="index.php" class="block p-3 hover:bg-blue-800 rounded-lg transition"> <i class="fas fa-chart-line mr-2"></i> Dashboard </a>
            <a href="inventory.php" class="block bg-blue-800 p-3 rounded-lg border-l-4 border-yellow-400 font-semibold"> <i class="fas fa-box-open mr-2"></i> Inventaris </a>
            <a href="opname.php" class="block p-3 hover:bg-blue-800 rounded-lg transition"> <i class="fas fa-clipboard-check mr-2"></i> Stock Opname </a>
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
            <h2 class="text-3xl font-bold text-gray-800">Manajemen Stok</h2>
            <p class="text-gray-500 text-sm">Kelola seluruh item inventaris gudang</p>
        </div>
        <div class="flex items-center space-x-4">
          <button onclick="openModal()" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-bold hover:bg-blue-700 shadow-md transition">
            <i class="fas fa-plus mr-2"></i> Tambah Produk Baru
          </button>
          <div class="bg-white p-2 rounded-full shadow-sm px-4 text-blue-900 font-bold border border-blue-100">
            <i class="fas fa-user-circle mr-2"></i> Admin <?php echo $_SESSION['nama'] ?? $_SESSION['username']; ?>
          </div>
        </div>
      </header>

      <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <table class="w-full text-left border-collapse">
          <thead class="bg-gray-50 text-xs uppercase text-gray-400">
            <tr>
              <th class="p-4 border-b">Nama Barang</th>
              <th class="p-4 border-b text-center">Stok Sistem</th>
              <th class="p-4 border-b">Kategori</th>
              <th class="p-4 border-b">Lokasi Rak</th>
              <th class="p-4 border-b text-right">Opsi</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr class="hover:bg-blue-50/50 border-b last:border-0 transition">
                <td class="p-4 text-gray-700 font-bold"><?php echo $row['nama_produk']; ?></td>
                <td class="p-4 text-center font-bold text-blue-800"><?php echo $row['stok_sistem']; ?></td>
                <td class="p-4"><span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-[10px] font-bold"><?php echo $row['kategori']; ?></span></td>
                <td class="p-4 text-gray-500 text-sm italic"><?php echo $row['lokasi_rak']; ?></td>
                <td class="p-4 text-right">
                    <a href="inventory.php?delete=<?php echo $row['id_produk']; ?>" onclick="return confirm('Hapus produk ini?')" class="text-gray-300 hover:text-red-500 px-2 transition">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </main>

    <div id="productModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center backdrop-blur-sm z-50">
      <form action="inventory.php" method="POST" class="bg-white w-96 rounded-xl shadow-2xl p-6">
        <h3 class="text-xl font-bold mb-4 text-gray-800 text-center border-b pb-2">Detail Produk Baru</h3>
        <div class="space-y-4">
          <input type="text" name="prodName" placeholder="Nama Barang" required class="w-full border p-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500" />
          <select name="prodCat" class="w-full border p-2 rounded-lg">
            <option>Elektronik</option>
            <option>Alat Kantor</option>
            <option>Makanan</option>
          </select>
          <input type="number" name="prodQty" placeholder="Stok Awal" required class="w-full border p-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500" />
          <input type="text" name="prodRak" placeholder="Lokasi Rak (Contoh: A-01)" class="w-full border p-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
        <div class="flex justify-end mt-6 space-x-3">
          <button type="button" onclick="closeModal()" class="text-gray-500 px-4 py-2 hover:bg-gray-100 rounded-lg text-sm">Batal</button>
          <button type="submit" name="save_product" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 shadow-md">Simpan</button>
        </div>
      </form>
    </div>

    <script>
      function openModal() { document.getElementById("productModal").classList.remove("hidden"); }
      function closeModal() { document.getElementById("productModal").classList.add("hidden"); }
    </script>
  </body>
</html>