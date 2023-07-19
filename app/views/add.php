<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    if (isset($_POST['name'], $_POST['amount'], $_POST['date'], $_POST['category'])) {
        $name = $_POST['name'];
        $amount = (float)$_POST['amount'];
        $date = $_POST['date'];
        $category = $_POST['category'];

        $homeController->addTransaction($name, $amount, $date, $category);
    }
}

$pageTitle = 'Ajouter une opération';
include './includes/header.php';

?>



<div class="container">
    <section class="card mb-4 rounded-3 shadow-sm">
        <div class="card-header py-3">
            <h1 class="my-0 fw-normal fs-4">Ajouter une opération</h1>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label for="name" class="form-label">Nom de l'opération *</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Facture d'électricité" required>
                </div>
                <div class="mb-3">
                    <label for="date" class="form-label">Date *</label>
                    <input type="date" class="form-control" name="date" id="date" required>
                </div>
                <div class="mb-3">
                    <label for="amount" class="form-label">Montant *</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="amount" id="amount" required>
                        <span class="input-group-text">€</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="category" class="form-label">Catégorie</label>
                    <select class="form-select" name="category" id="category">
                        <option value="" selected>Aucune catégorie</option>
                        <option value="1">Habitation</option>
                        <option value="2">Travail</option>
                        <option value="3">Cadeau</option>
                        <option value="4">Numérique</option>
                        <option value="5">Alimentation</option>
                        <option value="6">Voyage</option>
                        <option value="7">Loisir</option>
                        <option value="7">Voiture</option>
                        <option value="7">Santé</option>
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg" name="addTransaction">Ajouter</button>
                </div>
            </form>
        </div>
    </section>
</div>


<footer class="py-3 mt-4 border-top">
    <p class="text-center text-body-secondary">© 2023 Mes comptes</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>