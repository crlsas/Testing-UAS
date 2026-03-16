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
       // Ubah 'qty' menjadi 'jumlah_jual' sesuai struktur database kamu
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
}