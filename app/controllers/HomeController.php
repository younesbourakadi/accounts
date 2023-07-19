<?php
class HomeController
{
    private $transactionModel;

    public function __construct(TransactionModel $transactionModel)
    {
        // Injecte le modèle de transaction dans le contrôleur lorsqu'il est instancié
        $this->transactionModel = $transactionModel;
    }

    // Ajoute une nouvelle transaction en utilisant le modèle TransactionModel
    public function addTransaction($name, $amount, $date, $category)
    {
        return $this->transactionModel->addTransaction($name, $amount, $date, $category);
    }

    // Met à jour une transaction existante en utilisant le modèle TransactionModel
    public function updateTransaction($transactionId, $updatedTransaction)
    {
        // Vérifie que les champs requis pour la mise à jour sont présents
        if (empty($updatedTransaction['name']) || empty($updatedTransaction['date_transaction']) || empty($updatedTransaction['amount']) || empty($updatedTransaction['id_category'])) {
            return false;
        }

        // Utilise le modèle TransactionModel pour effectuer la mise à jour
        return $this->transactionModel->updateTransaction($transactionId, $updatedTransaction);
    }

    // Supprime une transaction par son identifiant en utilisant le modèle TransactionModel
    public function deleteTransaction($transactionId)
    {
        return $this->transactionModel->deleteTransactionById($transactionId);
    }
}
