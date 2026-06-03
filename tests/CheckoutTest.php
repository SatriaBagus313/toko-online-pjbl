<?php
use PHPUnit\Framework\TestCase;
use App\Checkout;

class CheckoutTest extends TestCase{
    // DIUBAH: Disesuaikan dengan nama file asli di folder data kamu (products.json)
    private $seedFile = __DIR__ . '/../data/products.json';
    private $testFile = __DIR__ . '/../data/products_test.json';
    private $orderFile = __DIR__ . '/../data/orders_test.json';
    private $checkout;

    // CT Stage: Menyiapkan data segar SEBELUM tiap tes
    protected function setUp(): void{
        copy($this->seedFile, $this->testFile);
        file_put_contents($this->orderFile, json_encode([]));
        $this->checkout = new Checkout($this->testFile, $this->orderFile);
    }

    // CT Stage: Integration Test
    public function testCheckoutReducesStock(){
        // Menyiapkan data PRD-002 secara dinamis ke file test agar pengujian independen
        $products = [];
        if (file_exists($this->testFile)) {
            $products = json_decode(file_get_contents($this->testFile), true) ?? [];
        }
        
        $products['PRD-002'] = [
            'nama' => 'Produk Test 2',
            'stok' => 5,
            'harga' => 10000
        ];
        file_put_contents($this->testFile, json_encode($products, JSON_PRETTY_PRINT));

        // Kode asli kamu tetap berjalan di bawah ini tanpa perubahan fungsi
        $keranjang = ['PRD-002' => 1];
        $this->checkout->prosesCheckout('test@mail.com', 'Jl. Sudirman', $keranjang);

        $productsResult = json_decode(file_get_contents($this->testFile), true);
        $this->assertEquals(4, $productsResult['PRD-002']['stok']);
    }

    // CT Stage: Menghapus data sampah SETELAH tiap tes
    protected function tearDown(): void{
        if (file_exists($this->testFile)) unlink($this->testFile);
        if (file_exists($this->orderFile)) unlink($this->orderFile);
    }
}