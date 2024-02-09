<?php
echo "<div style='margin:24px;'>";
echo "<pre>";
$history = [['Transaction ID', 'Transaction Status', 'Order ID', 'Order Status', 'Amount', 'Transaction Type', 'Validity', 'Validity Status']];

class Transaction
{
    // Properties
    public $id; // unique int
    public $id_status;
    public $orderld; //non valid transaction makes all other transactions non valid int
    public $orderld_status;
    public $amount; //negative balance should make transaction invalid int
    public $txtype; //Bet decreases amount, Win increases string
    public $validity; //Validity status bool
    public $validity_status; //Comment for validity string



    function do_transaction($id, $orderld, $txtype, $amount, $customer)
    {
        global $history;
        $validity = true;
        $id_status = 'id unqiue';
        $orderld_status = 'ok';
        $validity_status = 'ok';

        // check ID of transaction for uniqueness
        $temp_id_array = [];
        foreach ($history as $history_record) {
            array_push($temp_id_array, $history_record[0]);
        }
        if (array_search($id, $temp_id_array)) {
            $validity = false;
            $validity_status = 'This Transaction ID NOT unique';
            $id_status = 'This Transaction ID NOT unique';
        };

        // check orderID if previous transaction non unique
        $temp_orderld_array = [];
        foreach ($history as $history_record) {
            array_push($temp_orderld_array, [$history_record[2], $history_record[1]]);
        }
        if (array_search([$orderld, 'This Transaction ID NOT unique'], $temp_orderld_array)) {
            $validity = false;
            $orderld_status = "Cancelled because previous Transaction ID NOT unique (" . array_search([$orderld, 'This Transaction ID NOT unique'], $temp_orderld_array) . ")";
            if ($validity_status != 'ok') {
                $validity_status .= " and " . $orderld_status;
            } else if ($validity_status == 'ok') {
                $validity_status = $orderld_status;
            }
        }



        $account = $customer->balance;
        if ($txtype == 'Bet') {
            $account -= $amount;
        } elseif ($txtype = 'Win') {
            $account += $amount;
        } else {
            echo 'wrong account change value';
        }
        if ($validity) {
            if ($account >= 0) {
                $customer->balance = $account;
                echo "<br>" . $customer->name . "'s Transaction valid (balance positive ".$account.")";
            } else {
                $validity = false;
                echo "<br>" . $customer->name . "'s Transaction invalid (balance negative ".$account.")";
                if ($validity_status != 'ok') {
                    $validity_status .= " and balance negative (" . $account . ")";
                } else if ($validity_status == 'ok') {
                    $validity_status = "balance negative (" . $account . ")";
                }
            }
        }

        array_push($history, [$id, $id_status, $orderld, $orderld_status, $amount, $txtype, $validity, $validity_status]);
    }
}
class Customer
{
    public $name;
    public $balance;
}

$john = new Customer;
$john->balance = 1000;
$john->name = 'John';

$anna = new Customer;
$anna->balance = 1000;
$anna->name = 'Anna';

$ta = new Transaction;
$ta->do_transaction(1, 1, 'Bet', 500, $john); //good
$ta->do_transaction(1, 2, 'Bet', 500, $john); //bad
$ta->do_transaction(2, 2, 'Bet', 500, $john); //bad
$ta->do_transaction(3, 2, 'Bet', 500, $john); //bad
$ta->do_transaction(4, 3, 'Bet', 500, $john); //good
$ta->do_transaction(5, 3, 'Win', 300, $john); //good
print_r($history);

echo "</pre>";
echo "</div>";
