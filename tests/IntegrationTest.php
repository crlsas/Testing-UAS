<?php
use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase {
    // Test 1: Cek apakah config.php bisa connect ke database
    public function testDatabaseConnection() {
        include 'config.php';
        $this->assertNotNull($conn);
        $this->assertTrue($conn->ping());
    }

    // Test 2: Cek alur Penjualan (Input data penjualan)
    public function testInsertSales() {
        include 'config.php';
        // Menggunakan 'jumlah_jual' sesuai kolom di db_gudang
        $sql = "INSERT INTO penjualan (id_produk, jumlah_jual, total_harga) VALUES (1, 2, 20000)";
        $this->assertTrue(mysqli_query($conn, $sql));
    }

    // Test 3: Cek Dashboard (Apakah total produk bisa ditarik)
    public function testDashboardData() {
        include 'config.php';
        $q = mysqli_query($conn, "SELECT COUNT(*) as total FROM produk");
        $res = mysqli_fetch_assoc($q);
        $this->assertArrayHasKey('total', $res);
    }

    // Test 4: Cek Fitur Hapus Produk (Sesuai logika di inventory.php kamu)
    public function testDeleteProduct() {
        include 'config.php';
        // 1. Insert produk dummy dulu
        mysqli_query($conn, "INSERT INTO produk (nama_produk, kategori, stok_sistem) VALUES ('Test Hapus', 'Alat Kantor', 10)");
        $id = mysqli_insert_id($conn);
        
        // 2. Jalankan perintah hapus
        $delete = mysqli_query($conn, "DELETE FROM produk WHERE id_produk = $id");
        $this->assertTrue($delete);
        
        // 3. Pastikan sudah tidak ada di DB
        $check = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk = $id");
        $this->assertEquals(0, mysqli_num_rows($check));
    }

    // Test 5: Cek Fitur Monitoring Stok Kritis (Dashboard Logic)
    public function testCheckCriticalStock() {
        include 'config.php';
        // Mencari produk yang stoknya di bawah stok_minimum (default 5)
        $q = mysqli_query($conn, "SELECT * FROM produk WHERE stok_sistem < stok_minimum");
        $this->assertNotNull($q);
        // Test ini tetap pass walaupun tidak ada produk kritis (yang penting query berhasil)
        $this->assertTrue(is_object($q));
    }
}