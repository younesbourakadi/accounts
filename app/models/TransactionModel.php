<?php

class TransactionModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllTransactions()
    {
        $stmt = $this->pdo->query("SELECT * FROM transaction");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTransactionsByMonth()
    {
        $currentMonth = date('Y-m');
        $stmt = $this->pdo->prepare("SELECT * FROM transaction WHERE DATE_FORMAT(date_transaction, '%Y-%m') = :currentMonth");
        $stmt->bindParam(':currentMonth', $currentMonth, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addTransaction($name, $amount, $date, $category)
    {
        $stmt = $this->pdo->prepare("INSERT INTO transaction (name, amount, date_transaction, id_category) VALUES (:name, :amount, :date, :category)");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':category', $category, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
