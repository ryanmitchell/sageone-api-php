<?php

require '../SageOne.php';

define('SAGE_CLIENT_ID', 'client id / api key');
define('SAGE_CLIENT_SECRET', 'client / api secret');
    
$client = new SageOne(SAGE_CLIENT_ID, SAGE_CLIENT_SECRET);

$callbackURL = 'http://example.com/auth/sageone/callback';


// We need to build the authorise url and redirect user to authorise our app
if(!$_GET['code']){
    
    $authoriseURL = $client->getAuthoriseURL($callbackURL);
    
    // redirect user
    header("Location: ".$authoriseURL);
    exit;
    
    
// We now have the authorisation code to retrieve the access token
} else {

    $accessToken = $client->getAccessToken($_GET['code'], $callbackURL);
    
    echo '<pre>';
    print_r($accessToken);
    echo '</pre>';
    
    // or
    
    echo '<br>';
    echo $accessToken['accessToken'];
    
    // Note: The access token does not expire so you can now store that access
    // token in your database against that user or as a constant if you are
    // talking with the api for your account only.
}

?>