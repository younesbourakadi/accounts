<?php

class TransactionModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Récupérer toutes les transactions de la table 'transaction'
    public function getAllTransactions()
    {
        $query = $this->pdo->query("SELECT * FROM transaction");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les transactions d'un mois spécifique
    public function getTransactionsByMonth($currentMonth)
    {
        // Formater le mois au format 'Y-m' pour la requête SQL
        $currentMonth = date('Y-m', strtotime($currentMonth));
        $query = $this->pdo->prepare("SELECT * FROM transaction WHERE DATE_FORMAT(date_transaction, '%Y-%m') = :currentMonth");
        $query->bindParam(':currentMonth', $currentMonth, PDO::PARAM_STR);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajouter une nouvelle transaction
    public function addTransaction($name, $amount, $date, $category)
    {
        $query = $this->pdo->prepare("INSERT INTO transaction (name, amount, date_transaction, id_category) VALUES (:name, :amount, :date, :category)");
        $query->bindParam(':name', $name, PDO::PARAM_STR);
        $query->bindParam(':amount', $amount, PDO::PARAM_INT);
        $query->bindParam(':date', $date, PDO::PARAM_STR);
        $query->bindParam(':category', $category, PDO::PARAM_INT);
        return $query->execute();
    }

    // Récupérer le nom de la catégorie par son identifiant
    public function getCategoryNameById($categoryId)
    {
        try {
            $query = $this->pdo->prepare("SELECT category_name FROM category WHERE id_category = :categoryId");
            $query->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
            $query->execute();

            if ($query->rowCount() > 0) {
                $categoryData = $query->fetch(PDO::FETCH_ASSOC);
                return $categoryData['category_name'];
            } else {
                return "Catégorie inconnue";
            }
        } catch (PDOException $e) {
            die("Erreur lors de la récupération du nom de la catégorie : " . $e->getMessage());
        }
    }

    // Récupérer le solde du mois spécifique (la somme des montants des transactions)
    public function getBalance($currentMonth)
    {
        // Formater le mois au format 'Y-m' pour la requête SQL
        $currentMonth = date('Y-m', strtotime($currentMonth));

        $query = $this->pdo->prepare("SELECT SUM(amount) as balance FROM transaction WHERE DATE_FORMAT(date_transaction, '%Y-%m') = :currentMonth");
        $query->bindParam(':currentMonth', $currentMonth, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        return (float) $result['balance'];
    }

    // Mettre à jour une transaction existante
    public function updateTransaction($transactionId, $updatedTransaction)
    {
        $query = "UPDATE transaction SET name = :name, date_transaction = :date_transaction, amount = :amount, id_category = :id_category WHERE id_transaction = :id_transaction";

        $query = $this->pdo->prepare($query);
        $query->bindValue(":name", $updatedTransaction['name']);
        $query->bindValue(":date_transaction", $updatedTransaction['date_transaction']);
        $query->bindValue(":amount", $updatedTransaction['amount']);
        $query->bindValue(":id_category", $updatedTransaction['id_category']);
        $query->bindValue(":id_transaction", $transactionId);

        return $query->execute();
    }

    // Supprimer une transaction par son identifiant
    public function deleteTransactionById($transactionId)
    {
        $query = $this->pdo->prepare("DELETE FROM transaction WHERE id_transaction = :id");
        $query->bindParam(':id', $transactionId, PDO::PARAM_INT);
        return $query->execute();
    }
}
