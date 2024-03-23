<?php

declare(strict_types=1);

$connectionData = getenv('AZURE_POSTGRESQL_CONNECTIONSTRING');
$connectionData = explode(';', $connectionData);
$connectionData = array_combine(
    array_map(fn($d) => explode('=', $d)[0], $connectionData),
    array_map(fn($d) => explode('=', $d)[1], $connectionData),
);

$host = $connectionData['Server'];
$dbname = $connectionData['Database'];
$user = $connectionData['User Id'];
$password = $connectionData['Password'];


$dsn = "pgsql:host=$host;dbname=$dbname";
try {
    $pdo = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    die("Nie można się połączyć z bazą danych: " . $e->getMessage());
}

$sql = "CREATE TABLE IF NOT EXISTS visit_counter (
    id SERIAL PRIMARY KEY,
    visits INT NOT NULL DEFAULT 1
)";
$pdo->exec($sql);

$sql = "SELECT visits FROM visit_counter WHERE id = 1";
$stmt = $pdo->query($sql);

if ($stmt->rowCount() > 0) {
    $sql = "UPDATE visit_counter SET visits = visits + 1 WHERE id = 1";
    $pdo->exec($sql);
} else {
    $sql = "INSERT INTO visit_counter (visits) VALUES (1)";
    $pdo->exec($sql);
}

$sql = "SELECT visits FROM visit_counter WHERE id = 1";
$stmt = $pdo->query($sql);
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $visits = $row['visits'];
    echo "Liczba wizyt na stronie: $visits";
} else {
    echo "Nie udało się pobrać liczby wizyt.";
}

$pdo = null;

