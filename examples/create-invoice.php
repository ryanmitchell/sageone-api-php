<?php

require '../SageOne.php';

define('SAGE_CLIENT_ID', 'client id / api key');
define('SAGE_CLIENT_SECRET', 'client / api secret');

// If you do not already have an access token please see:
// /examples/auth.php
define('SAGE_ACCESS_TOKEN', 'your access token');
    
$client = new SageOne(SAGE_CLIENT_ID, SAGE_CLIENT_SECRET);
$client->setAccessToken(SAGE_ACCESS_TOKEN);


$result = $client->createInvoice(array(
    "contact_id" => 760900,
    "contact_name" => "Berkeley House",
    "date" => "20/02/2013",
    "due_date" => "27/02/2013",
    "main_address" => "Greenwich Avenue,\nBilton Grange,\nHull,\nEast Riding of Yorkshire,\nHU9 4UY",
    "line_items_attributes" => array(
        array(
            "service_id" => 38534,
            "description" => "Wellness Exercise Therapy Class on 18/02/2013 at 2pm. Instructed by Steven Gardner.",
            "quantity" => "1.0",
            "unit_price" => "40.00",
            "tax_code_id" => 4,
            "ledger_account_id" => 2517791
        )
    ),
    "reference" => "BERKE0002"
));


echo '<pre>';
print_r($result);
echo '</pre>';

/*  $result = 
Array
(
    [id] => 1319867
    [invoice_number] => SI-4
    [status] => Array
        (
            [id] => 1
            [$key] => 1
        )

    [due_date] => 27/02/2013
    [date] => 20/02/2013
    [void_reason] => 
    [outstanding_amount] => 40.0
    [total_net_amount] => 40.0
    [total_tax_amount] => 0.0
    [tax_scheme_period_id] => 37191
    [carriage] => 0.0
    [carriage_tax_code] => Array
        (
            [$key] => 
        )

    [carriage_tax_rate_percentage] => 0.0
    [contact] => Array
        (
            [id] => 760900
            [$key] => 760900
        )

    [contact_name] => Berkeley House
    [main_address] => Greenwich Avenue,
Bilton Grange,
Hull,
East Riding of Yorkshire,
HU9 4UY
    [delivery_address] => 
    [delivery_address_same_as_main] => 
    [reference] => BERKE0002
    [notes] => 
    [terms_and_conditions] => 
    [lock_version] => 0
    [line_items] => Array
        (
            [0] => Array
                (
                    [id] => 2174199
                    [description] => Wellness Therapy Class on 18/02/2013 at 2pm. Instructed by Steven Gardner.
                    [quantity] => 1.0
                    [unit_price] => 40.0
                    [net_amount] => 40.0
                    [tax_amount] => 0.0
                    [tax_code] => Array
                        (
                            [id] => 5
                            [$key] => 5
                        )

                    [tax_rate_percentage] => 0.0
                    [unit_price_includes_tax] => 
                    [ledger_account] => Array
                        (
                            [id] => 2517791
                            [$key] => 2517791
                        )

                    [product_code] => 
                    [product] => Array
                        (
                            [$key] => 
                        )

                    [service] => Array
                        (
                            [id] => 38534
                            [$key] => 38534
                        )

                    [lock_version] => 0
                    [$key] => 2174199
                )

        )

    [$key] => 1319867
)
*/

?>