<?php
use PHPUnit\Framework\TestCase;
use App\Inventory;

class InventoryTest extends TestCase {
    private $inv;

    protected function setUp(): void {
        $this->inv = new Inventory();
    }

    // --- TEST KATEGORI 1: STATUS STOK (5 KASUS) ---
    public function testStokKritis() { $this->assertEquals("KRITIS", $this->inv->getStockStatus(1, 5)); }
    public function testStokAman() { $this->assertEquals("AMAN", $this->inv->getStockStatus(10, 5)); }
    public function testStokPas() { $this->assertEquals("AMAN", $this->inv->getStockStatus(5, 5)); }
    public function testStokNol() { $this->assertEquals("KRITIS", $this->inv->getStockStatus(0, 5)); }
    public function testStokNegatif() { $this->assertEquals("INVALID", $this->inv->getStockStatus(-1, 5)); }

    // --- TEST KATEGORI 2: VALIDASI NAMA (5 KASUS) ---
    public function testNamaOk() { $this->assertTrue($this->inv->validateName("Laptop")); }
    public function testNamaPendek() { $this->assertFalse($this->inv->validateName("HP")); }
    public function testNamaKosong() { $this->assertFalse($this->inv->validateName("")); }
    public function testNamaSpasi() { $this->assertFalse($this->inv->validateName("   ")); }
    public function testNamaPanjang() { $this->assertTrue($this->inv->validateName("Monitor Gaming 144Hz")); }

    // --- TEST KATEGORI 3: LOGIKA DISKON (5 KASUS) ---
    public function testDiskonMember() { $this->assertEquals(900, $this->inv->calculateDiscount(1000, true)); }
    public function testDiskonNonMember() { $this->assertEquals(1000, $this->inv->calculateDiscount(1000, false)); }
    public function testHargaNol() { $this->assertEquals(0, $this->inv->calculateDiscount(0, true)); }
    public function testHargaNegatif() { $this->assertEquals(0, $this->inv->calculateDiscount(-500, false)); }
    public function testDiskonBesar() { $this->assertEquals(9000, $this->inv->calculateDiscount(10000, true)); }

    // --- TEST OPNAME ---
    public function testOpnameKurang() { $this->assertEquals(-2, $this->inv->calculateOpnameDiff(10, 8)); }
    public function testOpnamePas() { $this->assertEquals(0, $this->inv->calculateOpnameDiff(10, 10)); }

    // --- TEST LOGIN ---
    public function testLoginValid() { $this->assertTrue($this->inv->isLoginValid("admin", "123456")); }
    public function testPasswordPendek() { $this->assertFalse($this->inv->isLoginValid("admin", "123")); }

    // --- TEST LAPORAN ---
    public function testTrendNaik() { $this->assertEquals("NAIK", $this->inv->getStockTrend(100, 50)); }
}