<?php
require "database.php";
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    return;
}

// Obtener todas las direcciones del usuario
$stmt = $conn->prepare("
    SELECT a.*, c.name as contact_name 
    FROM addresses a 
    JOIN contacts c ON a.contact_id = c.id 
    WHERE a.user_id = :user_id 
    ORDER BY c.name, a.id
");
$stmt->execute([":user_id" => $_SESSION["user"]["id"]]);
$addresses = $stmt->fetchAll();
?>

<?php require "partials/header.php"; ?>

<div class="container pt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>All My Addresses</h3>
                </div>
                <div class="card-body">
                    <?php if (count($addresses) == 0): ?>
                        <p class="text-center">No addresses saved yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Contact</th>
                                        <th>Address</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($addresses as $address): ?>
                                    <tr>
                                        <td><?= $address["contact_name"] ?></td>
                                        <td><?= nl2br($address["address"]) ?></td>
                                        <td>
                                            <a href="editAddress.php?id=<?= $address["id"] ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="deleteAddress.php?id=<?= $address["id"] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this address?')">Delete</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require "partials/footer.php"; ?>
