<?php
use BookStore\OrderService;

require_once __DIR__ . '/vendor/autoload.php';

$productsJson = file_get_contents('../data/products.json');
$products = json_decode($productsJson, true);
$orderResult = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $address = $_POST['address'];

    $orderService = new OrderService();
    $responseData = $orderService->sendOrderRequest($productId, (int)$quantity);
    $totalPrice = $responseData['total'];

    $orderResult = "<div class='result' id='orderSummary'>";
    $orderResult .= "<h3>Pesanan Berhasil Diproses</h3>";
    $orderResult .= "<p id='summaryTotal'>Total Harga: Rp " . number_format($totalPrice, 0, ',', '.') . "</p>";
    $orderResult .= "<p id='summaryAddress'>Pengiriman ke: " . $address . "</p>";
    $orderResult .= "</div>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BookStore Group 1</title>
    <script src="script.js"></script>
</head>
<body>
    <h1 id="pageTitle">Katalog Toko Buku</h1>
    <form id="orderForm" method="POST" action="">
        <label for="product_id">Pilih Buku:</label><br>
        <select name="product_id" id="product_id">
            <?php foreach ($products as $p): ?>
                <option value="<?php echo $p['id']; ?>" data-price="<?php echo $p['price']; ?>" data-stock="<?php echo $p['stock']; ?>">
                    <?php echo $p['title']; ?> (Stok: <?php echo $p['stock']; ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label for="quantity">Kuantitas:</label><br>
        <input type="number" name="quantity" id="quantity" data-testid="input-quantity">
        <br><br>
        <label for="address">Alamat Pengiriman:</label><br>
        <textarea name="address" id="address" data-testid="input-address"></textarea>
        <br><br>
        <button type="submit" id="submitBtn">Beli Sekarang</button>
    </form>
    <hr>
    <?php echo $orderResult; ?>
</body>
</html>