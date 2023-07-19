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
        // Valider les données si nécessaire
        return $this->transactionModel->addTransaction($name, $amount, $date, $category);
    }
}
