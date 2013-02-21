<?php

require '../SageOne.php';

define('SAGE_CLIENT_ID', 'client id / api key');
define('SAGE_CLIENT_SECRET', 'client / api secret');

// If you do not already have an access token please see:
// /examples/auth.php
define('SAGE_ACCESS_TOKEN', 'your access token');
    
$client = new SageOne(SAGE_CLIENT_ID, SAGE_CLIENT_SECRET);
$client->setAccessToken(SAGE_ACCESS_TOKEN);


$result = $client->createContact(array(
    "name" => "Magnolia House",
    "contact_type_id" => "1",
    "telephone" => "01482845038",
    "email" => "linsayaltoft@parklanehealthcare.co.uk",
    "main_address" => array(
        "street_one" => "42 Hull Road",
        "street_two" => "",
        "town" => "Cottingham",
        "county" => "East Riding of Yorkshire",
        "postcode" => "HU16 4PX",
        "country_id" => 218 // UK
    )
));


echo '<pre>';
print_r($result);
echo '</pre>';

/* $result = 
Array
(
    [id] => 760900
    [name] => Berkeley House
    [company_name] => 
    [name_and_company_name] => Berkeley House
    [contact_type] => Array
        (
            [id] => 1
            [$key] => 1
        )

    [email] => 
    [telephone] => 
    [mobile] => 
    [notes] => 
    [tax_reference] => 
    [lock_version] => 0
    [main_address] => Array
        (
            [street_one] => 
            [street_two] => 
            [town] => 
            [county] => 
            [postcode] => 
            [country] => Array
                (
                    [$key] => 
                )

            [$key] => 1558571
        )

    [delivery_address] => Array
        (
            [street_one] => 
            [street_two] => 
            [town] => 
            [county] => 
            [postcode] => 
            [country] => Array
                (
                    [$key] => 
                )

            [$key] => 1558572
        )

    [$key] => 760900
)
*/

?>