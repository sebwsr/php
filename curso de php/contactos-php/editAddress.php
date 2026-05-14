<?php
require "database.php";
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    return;
}

$error = null;
$id = $_GET["id"];

// Obtener la dirección
$stmt = $conn->prepare("SELECT * FROM addresses WHERE id = :id AND user_id = :user_id");
$stmt->execute([":id" => $id, ":user_id" => $_SESSION["user"]["id"]]);
$address = $stmt->fetch();

if (!$address) {
    header("Location: home.php");
    return;
}

// Obtener el contacto relacionado
$stmt = $conn->prepare("SELECT * FROM contacts WHERE id = :id");
$stmt->execute([":id" => $address["contact_id"]]);
$contact = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["address"])) {
        $error = "Please enter an address.";
    } else {
        $stmt = $conn->prepare("UPDATE addresses SET address = :address WHERE id = :id");
        $stmt->execute([
            ":address" => $_POST["address"],
            ":id" => $id
        ]);
        
        $_SESSION["flash"] = ["message" => "Address updated for {$contact['name']}."];
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
                <div class="card-header">Edit Address for <?= $contact["name"] ?></div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <p class="text-danger"><?= $error ?></p>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3 row">
                            <label for="address" class="col-md-4 col-form-label text-md-end">Address</label>
                            <div class="col-md-6">
                                <textarea id="address" name="address" class="form-control" rows="3" required><?= $address["address"] ?></textarea>
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">Update Address</button>
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