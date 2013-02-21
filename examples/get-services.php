<?php

require '../SageOne.php';

define('SAGE_CLIENT_ID', 'client id / api key');
define('SAGE_CLIENT_SECRET', 'client / api secret');

// If you do not already have an access token please see:
// /examples/auth.php
define('SAGE_ACCESS_TOKEN', 'your access token');
    
$client = new SageOne(SAGE_CLIENT_ID, SAGE_CLIENT_SECRET);
$client->setAccessToken(SAGE_ACCESS_TOKEN);


$result = $client->getServices();


echo '<pre>';
print_r($result);
echo '</pre>';

?>