<?php

require '../SageOne.php';

define('SAGE_CLIENT_ID', 'client id / api key');
define('SAGE_CLIENT_SECRET', 'client / api secret');

// If you do not already have an access token please see:
// /examples/auth.php
define('SAGE_ACCESS_TOKEN', 'your access token');
    
$client = new SageOne(SAGE_CLIENT_ID, SAGE_CLIENT_SECRET);
$client->setAccessToken(SAGE_ACCESS_TOKEN);


$result = $client->createInvoicePayment(1319743, array(
    'amount' => '40.00',
    'date' => '21/02/2013',
    'reference' => '000063',
    'destination_id' => 2361651 // Current Account
));


echo '<pre>';
print_r($result);
echo '</pre>';

/* $result = 
Array
(
    [id] => 2783451
    [date] => 21/02/2013
    [reference] => 000063
    [voided] => 
    [amount] => 40.0
    [source] => Array
        (
            [id] => 2361648
            [$key] => 2361648
        )

    [destination] => Array
        (
            [id] => 2361651
            [$key] => 2361651
        )

    [lock_version] => 0
    [$key] => 2783451
)
*/

?>