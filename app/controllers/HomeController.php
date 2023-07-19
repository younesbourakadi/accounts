<?php

class HomeController
{
    private $transactionModel;

    public function __construct(TransactionModel $transactionModel)
    {
        $this->transactionModel = $transactionModel;
    }

    public function index()
    {
        $transactions = $this->transactionModel->getTransactionsByMonth();
        return $transactions;
    }

    public function getBalance()
    {
        $transactions = $this->transactionModel->getAllTransactions();
        $balance = 0;
        foreach ($transactions as $transaction) {
            $balance += $transaction['amount'];
        }
        return $balance;
    }

    public function addTransaction($name, $amount, $date, $category)
    {
        return $this->transactionModel->addTransaction($name, $amount, $date, $category);
    }

    public function updateTransaction($transactionId, $updatedTransaction)
    {
        if (empty($updatedTransaction['name']) || empty($updatedTransaction['date_transaction']) || empty($updatedTransaction['amount']) || empty($updatedTransaction['id_category'])) {
            return false;
        }

        return $this->transactionModel->updateTransaction($transactionId, $updatedTransaction);
    }

    public function deleteTransaction($transactionId)
    {
        return $this->transactionModel->deleteTransactionById($transactionId);
    }
}
