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
        getGames($pdo);
        break;
    case 'POST':
        createGame($pdo);
        break;
    case 'PUT':
        updateGame($pdo);
        break;
    case 'DELETE':
        deleteGame($pdo);
        break;
    default:
        echo json_encode(["error" => "Metode tidak didukung"]);
        break;
}

// Fungsi untuk mengambil data game
function getGames($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM games");
    $stmt->execute();
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($games);
}

// Fungsi untuk menambahkan data game
function createGame($pdo) {
    $data = json_decode(file_get_contents("php://input"), true);
    $name = $data['name'];
    $price = $data['price'];
    $developer = $data['developer'];
    $rating = $data['rating'];
    $genre = $data['genre'];

    $stmt = $pdo->prepare("INSERT INTO games (name, price, developer, rating, genre) VALUES (:name, :price, :developer, :rating, :genre)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':developer', $developer);
    $stmt->bindParam(':rating', $rating);
    $stmt->bindParam(':genre', $genre);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Game berhasil ditambahkan"]);
    } else {
        echo json_encode(["error" => "Gagal menambahkan game"]);
    }
}

// Fungsi untuk memperbarui data game
function updateGame($pdo) {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];
    $name = $data['name'];
    $price = $data['price'];
    $developer = $data['developer'];
    $rating = $data['rating'];
    $genre = $data['genre'];

    $stmt = $pdo->prepare("UPDATE games SET name = :name, price = :price, developer = :developer, rating = :rating, genre = :genre WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':developer', $developer);
    $stmt->bindParam(':rating', $rating);
    $stmt->bindParam(':genre', $genre);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Game berhasil diperbarui"]);
    } else {
        echo json_encode(["error" => "Gagal memperbarui game"]);
    }
}

// Fungsi untuk menghapus data game
function deleteGame($pdo) {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    $stmt = $pdo->prepare("DELETE FROM games WHERE id = :id");
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Game berhasil dihapus"]);
    } else {
        echo json_encode(["error" => "Gagal menghapus game"]);
    }
}
?>