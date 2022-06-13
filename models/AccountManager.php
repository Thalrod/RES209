<?php

class AccountManager extends Model
{


    public function getAccountByID(int $id)
    {

        $this->table = "ACCOUNTS";
        $account = $this->find(
            [
                "selectors" => [
                    "ACCOUNTS.*",
                ],
                "conditions" => [
                    "ACCOUNTS.id  =" => $id
                ]
            ]

        );

        return new Account($account[0]);
    }

    public function getAccounts()
    {

        $this->table = "ACCOUNTS";
        $accountsArray = $this->find(
            [
                "selectors" => [

                    "ACCOUNTS.last_name",
                    "ACCOUNTS.first_name",
                    "ACCOUNTS.id"
                ],
                "order" => "ACCOUNTS.last_name ASC"
            ]

        );

        $accounts  = [];
        foreach ($accountsArray as $account) {
            $accounts[] = new Account($account);
        }


        return $accounts;
    }

    public function isExist(int $accountID)
    {

        $this->table = "ACCOUNTS";
        $account = $this->find(
            [
                "selectors" => [
                    "id",
                ],
                "conditions" => [
                    "id  =" => $accountID
                ]
            ]

        );

        return $account ? true : false;
    }
}
