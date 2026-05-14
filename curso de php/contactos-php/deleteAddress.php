<?php
require "database.php";
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    return;
}

$id = $_GET["id"];

// Verificar que la dirección pertenece al usuario
$stmt = $conn->prepare("SELECT a.*, c.name FROM addresses a JOIN contacts c ON a.contact_id = c.id WHERE a.id = :id AND a.user_id = :user_id");
$stmt->execute([":id" => $id, ":user_id" => $_SESSION["user"]["id"]]);
$address = $stmt->fetch();

if ($address) {
    $contact_name = $address["name"];
    $conn->prepare("DELETE FROM addresses WHERE id = :id")->execute([":id" => $id]);
    $_SESSION["flash"] = ["message" => "Address deleted from {$contact_name}."];
}

header("Location: home.php");
?>