<?php
require "database.php";
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    return;
}

// Obtener contactos con sus direcciones
$contacts = $conn->query("SELECT * FROM contacts WHERE user_id = {$_SESSION['user']['id']}");
?>

<?php require "partials/header.php"; ?>

<div class="container pt-4 p-3">
    <div class="row">
        <!-- Enlace para ver todas las direcciones -->
        <div class="col-12 mb-3">
            <a href="addresses.php" class="btn btn-info">View All Addresses</a>
        </div>
        
        <?php if ($contacts->rowCount() == 0): ?>
            <div class="col-md-4 mx-auto">
                <div class="card card-body text-center">
                    <p>No contacts saved yet</p>
                    <a href="add.php">Add One!</a>
                </div>
            </div>
        <?php endif; ?>
        
        <?php foreach ($contacts as $contact): ?>
            <div class="col-md-4 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="card-title text-capitalize"><?= $contact["name"] ?></h3>
                        <p class="m-2"><?= $contact["phone_number"] ?></p>
                        
                        <!-- Mostrar direcciones -->
                        <?php
                        $stmt = $conn->prepare("SELECT * FROM addresses WHERE contact_id = :contact_id");
                        $stmt->execute([":contact_id" => $contact["id"]]);
                        $addresses = $stmt->fetchAll();
                        ?>
                        
                        <?php if (count($addresses) > 0): ?>
                            <hr>
                            <div class="text-start">
                                <strong>Addresses:</strong>
                                <?php foreach ($addresses as $addr): ?>
                                    <p class="small mb-1">• <?= nl2br($addr["address"]) ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-2">
                            <a href="add_address.php?contact_id=<?= $contact["id"] ?>" class="btn btn-sm btn-success mb-2">Add Address</a>
                            <a href="edit.php?id=<?= $contact["id"] ?>" class="btn btn-secondary mb-2">Edit Contact</a>
                            <a href="delete.php?id=<?= $contact["id"] ?>" class="btn btn-danger mb-2">Delete Contact</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require "partials/footer.php"; ?>