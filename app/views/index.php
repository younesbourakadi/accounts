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
                                <td width="50" class="ps-3">
                                    <?php if (!empty($transaction['icon_class'])) { ?>
                                        <i class="<?= htmlspecialchars($transaction['icon_class']) ?> fs-3"></i>
                                    <?php } ?>
                                </td>
                                <td>
                                    <time datetime="<?= htmlspecialchars($transaction['date_transaction']) ?>" class="d-block fst-italic fw-light"><?= date('d/m/Y', strtotime($transaction['date_transaction'])) ?></time>
                                    <?= htmlspecialchars($transaction['name']) ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($transaction['amount'] < 0) { ?>
                                        <span class="rounded-pill text-nowrap bg-warning-subtle px-2">
                                            <?= formatAmount(abs($transaction['amount'])) ?>
                                        </span>
                                    <?php } else { ?>
                                        <span class="rounded-pill text-nowrap bg-success-subtle px-2">
                                            <?= formatAmount($transaction['amount']) ?>
                                        </span>
                                    <?php } ?>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="#" class="btn btn-outline-primary btn-sm rounded-circle">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-danger btn-sm rounded-circle">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
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