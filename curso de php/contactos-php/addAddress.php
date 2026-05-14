<?php
require "database.php";
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    return;
}

$error = null;
$contact_id = $_GET["contact_id"];

// Verificar que el contacto existe y pertenece al usuario
$stmt = $conn->prepare("SELECT * FROM contacts WHERE id = :id AND user_id = :user_id");
$stmt->execute([":id" => $contact_id, ":user_id" => $_SESSION["user"]["id"]]);
$contact = $stmt->fetch();

if (!$contact) {
    header("Location: home.php");
    return;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["address"])) {
        $error = "Please enter an address.";
    } else {
        $stmt = $conn->prepare("INSERT INTO addresses (contact_id, user_id, address) VALUES (:contact_id, :user_id, :address)");
        $stmt->execute([
            ":contact_id" => $contact_id,
            ":user_id" => $_SESSION["user"]["id"],
            ":address" => $_POST["address"]
        ]);
        
        $_SESSION["flash"] = ["message" => "Address added for {$contact['name']}."];
        header("Location: home.php");
        return;
    }
}
?>

<?php require "partials/header.php"; ?>

<div class="container pt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Add Address for <?= $contact["name"] ?></div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <p class="text-danger"><?= $error ?></p>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3 row">
                            <label for="address" class="col-md-4 col-form-label text-md-end">Address</label>
                            <div class="col-md-6">
                                <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">Save Address</button>
                                <a href="home.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require "partials/footer.php"; ?>