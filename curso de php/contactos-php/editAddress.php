<?php
require "database.php";
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$error = null;
$id = $_GET["id"] ?? null;

// Obtener la dirección y el nombre del contacto
$stmt = $conn->prepare("
    SELECT a.*, c.name as contact_name 
    FROM addresses a 
    JOIN contacts c ON a.contact_id = c.id 
    WHERE a.id = :id AND a.user_id = :user_id LIMIT 1
");
$stmt->execute([":id" => $id, ":user_id" => $_SESSION["user"]["id"]]);
$addressRow = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$addressRow) {
    header("Location: home.php");
    exit();
}

// ==========================================
// LÓGICA PARA SEPARAR EL STRING EN PARTES
// ==========================================
// Separamos el string guardado usando la coma como delimitador
$addressParts = explode(',', $addressRow["address"]);

// Limpiamos los espacios en blanco de cada parte extraída y las asignamos
$savedStreet  = isset($addressParts[0]) ? trim($addressParts[0]) : '';
$savedCity    = isset($addressParts[1]) ? trim($addressParts[1]) : '';
$savedState   = isset($addressParts[2]) ? trim($addressParts[2]) : '';
$savedZip     = isset($addressParts[3]) ? trim($addressParts[3]) : '';
$savedCountry = isset($addressParts[4]) ? trim($addressParts[4]) : '';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $street  = trim($_POST["street"]);
    $city    = trim($_POST["city"]);
    $state   = trim($_POST["state"]);
    $zip     = trim($_POST["zip"]);
    $country = trim($_POST["country"]);

    if (empty($street) || empty($city) || empty($country)) {
        $error = "Street, City and Country are required.";
    } else {
        // Volvemos a unir en un solo string
        $fullAddress = "$street, $city, $state, $zip, $country";

        $stmt = $conn->prepare("UPDATE addresses SET address = :address WHERE id = :id");
        $stmt->execute([
            ":address" => $fullAddress,
            ":id" => $id
        ]);
        
        $_SESSION["flash"] = ["message" => "Address updated for {$addressRow['contact_name']}."];
        header("Location: addresses.php?contact_id=" . $addressRow["contact_id"]);
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
                    <h4 class="mb-0">Edit Address</h4>
                    <p class="text-muted mb-0">For <?= htmlspecialchars($addressRow["contact_name"]) ?></p>
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
                                <input type="text" class="form-control" id="street" name="street" value="<?= htmlspecialchars($savedStreet) ?>" required autofocus>
                            </div>
                            <div class="col-md-6">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" value="<?= htmlspecialchars($savedCity) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="state" class="form-label">State / Province</label>
                                <input type="text" class="form-control" id="state" name="state" value="<?= htmlspecialchars($savedState) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="zip" class="form-label">CP</label>
                                <input type="text" class="form-control" id="zip" name="zip" value="<?= htmlspecialchars($savedZip) ?>">
                            </div>
                            <div class="col-md-8">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control" id="country" name="country" value="<?= htmlspecialchars($savedCountry) ?>" required>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="addresses.php?contact_id=<?= $addressRow["contact_id"] ?>" class="btn btn-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Update Address</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require "partials/footer.php"; ?>
