<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../models/TransactionModel.php';
require_once '../controllers/HomeController.php';


if (isset($_GET['id'])) {
    $transactionId = $_GET['id'];

    $homeController = new HomeController($transactionModel);

    $isDeleted = $homeController->deleteTransaction($transactionId);

    if ($isDeleted) {
        header("Location: index.php?page=home");
        exit;
    } else {
        echo "La transaction n'a pas pu être supprimée.";
    }
}
