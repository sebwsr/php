<?php
require "database.php";
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$error = null;
$contact_id = $_GET["contact_id"] ?? null;

// Verificar que el contacto existe y pertenece al usuario
$stmt = $conn->prepare("SELECT * FROM contacts WHERE id = :id AND user_id = :user_id LIMIT 1");
$stmt->execute([":id" => $contact_id, ":user_id" => $_SESSION["user"]["id"]]);
$contact = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$contact) {
    header("Location: home.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recibir y limpiar cada campo individualmente
    $street  = trim($_POST["street"]);
    $city    = trim($_POST["city"]);
    $state   = trim($_POST["state"]);
    $zip     = trim($_POST["zip"]);
    $country = trim($_POST["country"]);

    // Validar que al menos la calle y la ciudad no estén vacías
    if (empty($street) || empty($city) || empty($country)) {
        $error = "Street, City and Country are required.";
    } else {
        // 2. Unir los datos en un solo string separado por comas
        $fullAddress = "$street, $city, $state, $zip, $country";

        // 3. Guardar en la base de datos el string completo
        $stmt = $conn->prepare("INSERT INTO addresses (contact_id, user_id, address) VALUES (:contact_id, :user_id, :address)");
        $stmt->execute([
            ":contact_id" => $contact_id,
            ":user_id" => $_SESSION["user"]["id"],
            ":address" => $fullAddress
        ]);
        
        $_SESSION["flash"] = ["message" => "Address added for {$contact['name']}."];
        header("Location: addresses.php?contact_id=" . $contact_id);
        exit();
    }
}
?>

<?php require "partials/header.php"; ?>

<div class="container pt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow border-0 rounded-lg">
                <div class="card-header text-center pt-4 pb-2">
                    <h4 class="mb-0">Add Address</h4>
                    <p class="text-muted mb-0">For <?= htmlspecialchars($contact["name"]) ?></p>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label for="street" class="form-label">Street Address</label>
                                <input type="text" class="form-control" id="street" name="street" placeholder="Av. Paseo de la Reforma 123" required autofocus>
                            </div>
                            <div class="col-md-6">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="azcapotzalco" required>
                            </div>
                            <div class="col-md-6">
                                <label for="state" class="form-label">State / Province</label>
                                <input type="text" class="form-control" id="state" name="state" placeholder="CDMX">
                            </div>
                            <div class="col-md-4">
                                <label for="zip" class="form-label">CP</label>
                                <input type="text" class="form-control" id="zip" name="zip" placeholder="01000">
                            </div>
                            <div class="col-md-8">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control" id="country" name="country" placeholder="México" required>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="addresses.php?contact_id=<?= $contact_id ?>" class="btn btn-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Save Address</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require "partials/footer.php"; ?>
