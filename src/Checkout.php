<?php

namespace App;

use Exception;

class Checkout
{
    private $fileProduk;
    private $filePesanan;

    public function __construct($fileProduk, $filePesanan)
    {
        $this->fileProduk = $fileProduk;
        $this->filePesanan = $filePesanan;
    }

    public function prosesCheckout($emailPelanggan, $alamat, $keranjang)
    {

        /*
        
        VALIDASI DATA CHECKOUT
        
        */

        // Validasi keranjang kosong
        if (empty($keranjang)) {
            throw new Exception("Keranjang belanja kosong.");
        }

        // Validasi alamat
        if (empty($alamat)) {
            throw new Exception("Alamat pengiriman wajib diisi.");
        }

        /*
       
        AMBIL DATA PRODUK
        
        */

        $products = json_decode(
            file_get_contents($this->fileProduk),
            true
        );

        // Validasi file produk
        if (!$products) {
            throw new Exception("Data produk tidak ditemukan.");
        }

        $totalHargaBarang = 0;

        /*
        
        PROSES KERANJANG BELANJA
        
        */

        foreach ($keranjang as $kodeProduk => $qty) {

            // Validasi quantity
            if ($qty <= 0) {
                throw new Exception(
                    "Kuantitas harus lebih dari 0."
                );
            }

            // Validasi produk
            if (!isset($products[$kodeProduk])) {
                throw new Exception(
                    "Produk tidak valid."
                );
            }

            // Validasi stok
            if ($products[$kodeProduk]['stok'] < $qty) {
                throw new Exception(
                    "Stok " .
                    $products[$kodeProduk]['nama'] .
                    " tidak mencukupi."
                );
            }

            // Hitung total harga
            $totalHargaBarang += (
                $products[$kodeProduk]['harga'] * $qty
            );

            // Kurangi stok produk
            $products[$kodeProduk]['stok'] -= $qty;
        }

        /*
        
        LOGIKA ONGKIR & DISKON
       
        */

        // Node 1
        $ongkosKirim = 20000;

        // Node 2
        $diskon = 0;

        // Node 3
        if ($totalHargaBarang > 500000) {

            // Node 4
            $ongkosKirim = 0;

            // Node 5
            if ($totalHargaBarang > 1000000) {

                // Node 6
                $diskon = $totalHargaBarang * 0.10;
            }
        }

        // Node 7
        $totalBayar = (
            $totalHargaBarang - $diskon
        ) + $ongkosKirim;

        /*
    
        SIMPAN PESANAN
        
        */

        $pesananBaru = [

            'id_pesanan' => uniqid('ORD-'),

            'email' => $emailPelanggan,

            // Proteksi XSS
            'alamat' => htmlspecialchars($alamat),

            'items' => $keranjang,

            'total_bayar' => $totalBayar,

            'status' => 'Menunggu Pembayaran',

            'tanggal' => date('Y-m-d H:i:s')
        ];

        /*
        
        UPDATE STOK PRODUK
        
        */

        file_put_contents(
            $this->fileProduk,
            json_encode(
                $products,
                JSON_PRETTY_PRINT
            )
        );

        /*
       
        SIMPAN DATA PESANAN
        
        */

        $orders = json_decode(
            file_get_contents($this->filePesanan),
            true
        );

        // Jika orders kosong
        if (!$orders) {
            $orders = [];
        }

        // Tambahkan pesanan baru
        $orders[] = $pesananBaru;

        // Simpan ke file JSON
        file_put_contents(
            $this->filePesanan,
            json_encode(
                $orders,
                JSON_PRETTY_PRINT
            )
        );

        /*
       
        RETURN DATA PESANAN
       
        */

        return $pesananBaru;
    }
}