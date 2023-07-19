<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../models/TransactionModel.php';
require_once '../controllers/HomeController.php';

function getEnvVar($key, $defaultValue = null)
{
    $envFilePath = __DIR__ . '/.env';

    if (file_exists($envFilePath)) {
        $lines = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                list($name, $value) = $parts;
                if ($name === $key) {
                    return $value;
                }
            }
        }
    }

    return $defaultValue;
}

define('DB_HOST', getEnvVar('DB_HOST', 'localhost'));
define('DB_NAME', getEnvVar('DB_NAME', 'accounts'));
define('DB_USER', getEnvVar('DB_USER', 'root'));
define('DB_PASSWORD', getEnvVar('DB_PASSWORD', 'root'));

class DB
{
    private static $instance = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            try {
                self::$instance = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}

$pdo = DB::getInstance();

$transactionModel = new TransactionModel($pdo);
$homeController = new HomeController($transactionModel);


$transactions = $transactionModel->getTransactionsByMonth();

function formatAmount($amount)
{
    return number_format($amount, 2) . ' €';
}

function formatBalance($balance)
{
    return ($balance >= 0 ? '+' : '-') . formatAmount(abs($balance));
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["updateTransaction"])) {
    $transactionId = $_POST["transactionId"];
    $updatedTransaction = [
        "name" => $_POST["name"],
        "date_transaction" => $_POST["date"],
        "amount" => floatval($_POST["amount"]),
        "id_category" => $_POST["category"],
    ];
    $isUpdated = $homeController->updateTransaction($transactionId, $updatedTransaction);


    if ($isUpdated) {
        var_dump($isUpdated);

        header("Location: index.php?page=home");
        exit;
    } else {
        echo 'La transaction n\'a pas pu être modifiée.';
    }
}

if (isset($_GET['id'])) {
    include '../actions/deleteTransaction.php';
}



?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opérations de Juillet 2023 - Mes Comptes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>

    <div class="container-fluid">
        <header class="row flex-wrap justify-content-between align-items-center p-3 mb-4 border-bottom">
            <a href="index.php?page=home" class="col-1">
                <i class="bi bi-piggy-bank-fill text-primary fs-1"></i>
            </a>
            <nav class="col-11 col-md-7">
                <ul class="nav">
                    <li class="nav-item">
                        <a href="index.php?page=home" class="nav-link link-secondary" aria-current="page">Opérations</a>
                    </li>
                    <li class="nav-item">
                        <a href="summary.php" class="nav-link link-body-emphasis">Synthèses</a>
                    </li>
                    <li class="nav-item">
                        <a href="categories.php" class="nav-link link-body-emphasis">Catégories</a>
                    </li>
                    <li class="nav-item">
                        <a href="import.php" class="nav-link link-body-emphasis">Importer</a>
                    </li>
                    <li class="nav-item">
                        <a href="add.php" class="nav-link link-body-emphasis">Ajouter</a>
                    </li>
                </ul>
            </nav>
            <form action="" class="col-12 col-md-4" role="search">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Rechercher..." aria-describedby="button-search">
                    <button class="btn btn-primary" type="submit" id="button-search">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </header>
    </div>

    <div class="container">
        <section class="card mb-4 rounded-3 shadow-sm">
            <div class="card-header py-3">
                <h2 class="my-0 fw-normal fs-4">Solde aujourd'hui</h2>
            </div>
            <div class="card-body">
                <p class="card-title pricing-card-title text-center fs-1"><?= formatBalance($homeController->getBalance()) ?></p>
            </div>
        </section>

        <section class="card mb-4 rounded-3 shadow-sm">
            <div class="card-header py-3">
                <h1 class="my-0 fw-normal fs-4">Opérations de Juillet 2023</h1>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col" colspan="2">Opération</th>
                            <th scope="col" class="text-end">Montant</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction) { ?>
                            <tr>
                                <td><?= htmlspecialchars($transaction['name']) ?></td>
                                <td><?= htmlspecialchars($transaction['date_transaction']) ?></td>
                                <td><?= htmlspecialchars($transaction['amount']) ?> €</td>
                                <td><?= $transactionModel->getCategoryNameById($transaction['id_category']) ?></td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-circle" data-bs-toggle="modal" data-bs-target="#editModal<?= $transaction['id_transaction'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="index.php?page=home&id=<?= $transaction['id_transaction'] ?>" class="btn btn-outline-danger btn-sm rounded-circle">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <div class="modal fade" id="editModal<?= $transaction['id_transaction'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $transaction['id_transaction'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel<?= $transaction['id_transaction'] ?>">Modifier l'opération</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="post">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Nom de l'opération *</label>
                                                    <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($transaction['name']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="date" class="form-label">Date *</label>
                                                    <input type="date" class="form-control" name="date" id="date" value="<?= htmlspecialchars($transaction['date_transaction']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="amount" class="form-label">Montant *</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="amount" id="amount" value="<?= htmlspecialchars($transaction['amount']) ?>" required>
                                                        <span class="input-group-text">€</span>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="category" class="form-label">Catégorie</label>
                                                    <select class="form-select" name="category" id="category">
                                                        <option value="" selected>Aucune catégorie</option>
                                                        <option value="1" <?= ($transaction['id_category'] == 1) ? 'selected' : '' ?>>Habitation</option>
                                                        <option value="2" <?= ($transaction['id_category'] == 2) ? 'selected' : '' ?>>Travail</option>
                                                        <option value="3" <?= ($transaction['id_category'] == 3) ? 'selected' : '' ?>>Cadeau</option>
                                                        <option value="4" <?= ($transaction['id_category'] == 4) ? 'selected' : '' ?>>Numérique</option>
                                                        <option value="5" <?= ($transaction['id_category'] == 5) ? 'selected' : '' ?>>Alimentation</option>
                                                        <option value="6" <?= ($transaction['id_category'] == 6) ? 'selected' : '' ?>>Voyage</option>
                                                        <option value="7" <?= ($transaction['id_category'] == 7) ? 'selected' : '' ?>>Loisir</option>
                                                        <option value="7" <?= ($transaction['id_category'] == 8) ? 'selected' : '' ?>>Voiture</option>
                                                        <option value="7" <?= ($transaction['id_category'] == 9) ? 'selected' : '' ?>>Santé</option>
                                                    </select>
                                                </div>
                                                <div class="text-center">
                                                    <button type="submit" class="btn btn-primary btn-lg" name="updateTransaction">Modifier</button>
                                                </div>
                                                <input type="hidden" name="transactionId" value="<?= $transaction['id_transaction'] ?>">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </tbody>


                </table>
            </div>
            <div class="card-footer">
                <nav class="text-center">
                    <ul class="pagination d-flex justify-content-center m-2">
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="bi bi-arrow-left"></i>
                            </span>
                        </li>
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">Juillet 2023</span>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="index.php?page=home">Juin 2023</a>
                        </li>
                        <li class="page-item">
                            <span class="page-link">...</span>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="index.php?page=home">
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </section>
    </div>


    <footer class="py-3 mt-4 border-top">
        <p class="text-center text-body-secondary">© 2023 Mes comptes</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>