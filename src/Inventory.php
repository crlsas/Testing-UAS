<?php
namespace App;

class Inventory {
    // 1. Logika Status Stok (5 Skenario)
    public function getStockStatus($stok, $min) {
        if ($stok < 0) return "INVALID";
        return ($stok < $min) ? "KRITIS" : "AMAN";
    }

    // 2. Validasi Nama Produk (5 Skenario)
    public function validateName($name) {
        $name = trim($name);
        return !empty($name) && strlen($name) >= 3;
    }

    // 3. Logika Perhitungan Diskon (5 Skenario)
    public function calculateDiscount($price, $isMember) {
        if ($price < 0) return 0;
        return $isMember ? ($price * 0.9) : $price;
    }
    // 4. Logika Stock Opname (Selisih stok)
    public function calculateOpnameDiff($stokSistem, $stokFisik) {
        return $stokFisik - $stokSistem;
    }
    
    // 5. Logika Login (Hanya validasi format, bukan cek DB)
    public function isLoginValid($user, $pass) {
        return !empty($user) && strlen($pass) >= 6;
    }
    
    // 6. Logika Laporan (Menentukan trend stok)
    public function getStockTrend($penjualanBulanIni, $penjualanBulanLalu) {
        if ($penjualanBulanIni > $penjualanBulanLalu) return "NAIK";
        if ($penjualanBulanIni < $penjualanBulanLalu) return "TURUN";
        return "STABIL";
    }
}