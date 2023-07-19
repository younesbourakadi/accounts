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

    public function getCategoryNameById($categoryId)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT category_name FROM category WHERE id_category = :categoryId");
            $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $categoryData = $stmt->fetch(PDO::FETCH_ASSOC);
                return $categoryData['category_name'];
            } else {
                return "Catégorie inconnue";
            }
        } catch (PDOException $e) {
            die("Erreur lors de la récupération du nom de la catégorie : " . $e->getMessage());
        }
    }

    public function updateTransaction($transactionId, $updatedTransaction)
    {
        $query = "UPDATE transaction SET name = :name, date_transaction = :date_transaction, amount = :amount, id_category = :id_category WHERE id_transaction = :id_transaction";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":name", $updatedTransaction['name']);
        $stmt->bindValue(":date_transaction", $updatedTransaction['date_transaction']);
        $stmt->bindValue(":amount", $updatedTransaction['amount']);
        $stmt->bindValue(":id_category", $updatedTransaction['id_category']);
        $stmt->bindValue(":id_transaction", $transactionId);

        return $stmt->execute();
    }
}
