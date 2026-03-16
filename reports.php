<?php
session_start();

// 1. PROTEKSI HALAMAN (Auth Guard)
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// 2. AMBIL DATA PRODUK UNTUK TABEL
$query_produk = mysqli_query($conn, "SELECT * FROM produk ORDER BY nama_produk ASC");
$data_produk = [];
while($row = mysqli_fetch_assoc($query_produk)) {
    $data_produk[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pusat Laporan - VMW Carlos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
</head>
<body class="bg-slate-50 flex">

    <aside class="w-64 bg-blue-900 h-screen sticky top-0 text-white p-6 shadow-xl flex flex-col">
      <div class="flex-grow">
          <h1 class="text-2xl font-bold mb-10 flex items-center"><i class="fas fa-boxes-stacked mr-3 text-yellow-400"></i> VMW</h1>
          <nav class="space-y-4">
            <a href="index.php" class="block p-3 hover:bg-blue-800 rounded-lg transition"> <i class="fas fa-chart-line mr-2"></i> Dashboard </a>
            <a href="inventory.php" class="block p-3 hover:bg-blue-800 rounded-lg transition"> <i class="fas fa-box-open mr-2"></i> Inventaris </a>
            <a href="opname.php" class="block p-3 hover:bg-blue-800 rounded-lg transition"> <i class="fas fa-clipboard-check mr-2"></i> Stock Opname </a>
            <a href="sales.php" class="block p-3 hover:bg-blue-800 rounded-lg transition"> <i class="fas fa-shopping-cart mr-2"></i> Penjualan </a>
            <a href="reports.php" class="block bg-blue-800 p-3 rounded-lg border-l-4 border-yellow-400 font-semibold"> <i class="fas fa-file-pdf mr-2"></i> Laporan </a>
          </nav>
      </div>
      <div class="border-t border-blue-800 pt-4">
          <a href="login.php" onclick="return confirm('Logout?')" class="block p-3 text-red-400 hover:bg-red-600 hover:text-white rounded-lg transition font-bold text-sm">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
          </a>
      </div>
    </aside>

    <main class="flex-1 p-8">
        <header class="flex justify-between items-center mb-10">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">Pusat Laporan</h2>
                <p class="text-slate-500 text-sm italic">Cetak data stok real-time ke format PDF</p>
            </div>
            <div class="flex items-center space-x-4">
                <button onclick="generateInventoryPDF()" class="bg-red-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-red-700 shadow-lg shadow-red-100 flex items-center transition active:scale-95">
                    <i class="fas fa-file-export mr-2"></i> Export ke PDF
                </button>
                <div class="bg-white p-2 rounded-full shadow-sm px-4 text-blue-900 font-bold border border-blue-100">
                    Admin <?php echo $_SESSION['nama'] ?? $_SESSION['username']; ?>
                </div>
            </div>
        </header>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            <h3 class="font-bold text-slate-700 mb-6 uppercase text-xs tracking-widest flex items-center">
                <i class="fas fa-eye mr-2 text-blue-500"></i> Preview Tabel Laporan Stok
            </h3>
            
            <table class="w-full text-left border-collapse" id="table-stok">
                <thead class="bg-slate-50 text-slate-400 text-[10px] uppercase">
                    <tr>
                        <th class="p-4 border-b">Nama Produk</th>
                        <th class="p-4 border-b">Kategori</th>
                        <th class="p-4 border-b text-center">Stok Sistem</th>
                        <th class="p-4 border-b">Lokasi Rak</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php foreach($data_produk as $p): ?>
                    <tr class="border-b hover:bg-slate-50 transition">
                        <td class="p-4 font-bold text-slate-700"><?php echo $p['nama_produk']; ?></td>
                        <td class="p-4 text-slate-500"><?php echo $p['kategori']; ?></td>
                        <td class="p-4 text-center font-mono font-bold text-blue-700"><?php echo $p['stok_sistem']; ?></td>
                        <td class="p-4 text-slate-400 italic text-xs font-mono"><?php echo $p['lokasi_rak']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function generateInventoryPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Desain Header PDF (Navy Blue Bar)
            doc.setFillColor(30, 58, 138); 
            doc.rect(0, 0, 210, 45, 'F');
            
            doc.setTextColor(255, 255, 255);
            doc.setFontSize(22);
            doc.text("LAPORAN STOK GUDANG", 14, 25);
            doc.setFontSize(10);
            doc.text("WMS Pro - Smart Inventory System", 14, 33);

            // Informasi Pencetakan
            doc.setTextColor(100);
            doc.setFontSize(9);
            doc.text("Admin : <?php echo $_SESSION['nama']; ?>", 14, 55);
            doc.text("Tanggal: " + new Date().toLocaleString('id-ID'), 14, 60);
            doc.text("Status : Laporan Resmi Inventaris", 14, 65);

            // Render Tabel Otomatis
            doc.autoTable({
                html: '#table-stok',
                startY: 75,
                theme: 'grid',
                headStyles: { fillColor: [30, 58, 138], halign: 'center' },
                styles: { fontSize: 8, cellPadding: 3 },
                columnStyles: {
                    2: { halign: 'center' }
                }
            });

            // Tanda Tangan Footer
            let finalY = doc.lastAutoTable.finalY + 20;
            doc.setFontSize(10);
            doc.text("Mengetahui,", 150, finalY);
            doc.setFont("helvetica", "bold");
            doc.text("Admin <?php echo $_SESSION['nama']; ?>", 150, finalY + 25);

            // Simpan File
            doc.save("Laporan_WMS_Pro_" + new Date().getTime() + ".pdf");
        }
    </script>
</body>
</html>