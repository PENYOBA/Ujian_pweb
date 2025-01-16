<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Koneksi ke database
$host = 'localhost';
$dbname = 'game_store';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["error" => "Koneksi gagal: " . $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getHistory($pdo);
        break;
    case 'POST':
        createHistory($pdo);
        break;
    case 'PUT':
        updateHistory($pdo);
        break;
    case 'DELETE':
        deleteHistory($pdo);
        break;
    default:
        echo json_encode(["error" => "Metode tidak didukung"]);
        break;
}

// Fungsi untuk mengambil data history pembelian
function getHistory($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM history");
    $stmt->execute();
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($history);
}

// Fungsi untuk menambahkan data history pembelian
function createHistory($pdo) {
    $data = json_decode(file_get_contents("php://input"), true);
    $buyer_name = $data['buyer_name'];
    $game_name = $data['game_name'];
    $purchase_date = $data['purchase_date'];
    $price = $data['price'];

    $stmt = $pdo->prepare("INSERT INTO history (buyer_name, game_name, purchase_date, price) VALUES (:buyer_name, :game_name, :purchase_date, :price)");
    $stmt->bindParam(':buyer_name', $buyer_name);
    $stmt->bindParam(':game_name', $game_name);
    $stmt->bindParam(':purchase_date', $purchase_date);
    $stmt->bindParam(':price', $price);

    if ($stmt->execute()) {
        echo json_encode(["transaction_id" => $pdo->lastInsertId(), "message" => "Data pembelian berhasil ditambahkan"]);
    } else {
        echo json_encode(["error" => "Gagal menambahkan data pembelian"]);
    }
}

// Fungsi untuk memperbarui data history pembelian
function updateHistory($pdo) {
    $data = json_decode(file_get_contents("php://input"), true);
    $transaction_id = $data['transaction_id'];
    $buyer_name = $data['buyer_name'];
    $game_name = $data['game_name'];
    $purchase_date = $data['purchase_date'];
    $price = $data['price'];

    $stmt = $pdo->prepare("UPDATE history SET buyer_name = :buyer_name, game_name = :game_name, purchase_date = :purchase_date, price = :price WHERE transaction_id = :transaction_id");
    $stmt->bindParam(':transaction_id', $transaction_id);
    $stmt->bindParam(':buyer_name', $buyer_name);
    $stmt->bindParam(':game_name', $game_name);
    $stmt->bindParam(':purchase_date', $purchase_date);
    $stmt->bindParam(':price', $price);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Data pembelian berhasil diperbarui"]);
    } else {
        echo json_encode(["error" => "Gagal memperbarui data pembelian"]);
    }
}

// Fungsi untuk menghapus data history pembelian
function deleteHistory($pdo) {
    $data = json_decode(file_get_contents("php://input"), true);
    $transaction_id = $data['transaction_id'];

    $stmt = $pdo->prepare("DELETE FROM history WHERE transaction_id = :transaction_id");
    $stmt->bindParam(':transaction_id', $transaction_id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Data pembelian berhasil dihapus"]);
    } else {
        echo json_encode(["error" => "Gagal menghapus data pembelian"]);
    }
}
?>
