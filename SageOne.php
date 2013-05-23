<?php

/*
Library Name: SageOne-API-PHP
Description: A PHP Library to access the SageOne API
Version: 0.1
Author: Eddie Harrison

Copyright 2013  Eddie Harrison  (email:eddie@eddieharrison.co.uk)
*/

class SageOne {
    
    private $clientId;
    private $clientSecret;
    
    private $oAuthAuthoriseURL = 'https://app.sageone.com/oauth/authorize';
    private $oAuthAccessTokenURL = 'https://app.sageone.com/oauth/token';
    private $accessToken;
    
    private $apiUrl = 'https://app.sageone.com/api/v1';
    
    private $debug = false;
    
    function __construct($clientId, $clientSecret){
        
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }
    
    public function getAuthoriseURL($callback){
        
        $authoriseURL  = $this->oAuthAuthoriseURL;
        $authoriseURL .= '?response_type=code';
        $authoriseURL .= '&client_id='.$this->clientId;
        $authoriseURL .= '&redirect_uri='.urlencode($callback);
        
        return $authoriseURL;
    }
    
    public function getAccessToken($code, $callback){
        
        if($this->accessToken){
            return $this->accessToken;
        }
        
        $params = array(
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $callback
        );
        
        $result = $this->call('', 'oauth', $params);
                
        // Save accessToken
        $this->accessToken = $result['access_token'];
    
        // Return the response as an array
        return $result;
    }
    
    public function setAccessToken($accessToken){
        $this->accessToken = $accessToken;
    }
    
    public function get($endpoint, $data=false){
        return $this->call($endpoint, 'get', $data);
    }
    
    public function post($endpoint, $data){
        return $this->call($endpoint, 'post', $data);
    }
    
    public function put($endpoint, $data){
        return $this->call($endpoint, 'put', $data);
    }
    
    public function delete($endpoint, $data){
        return $this->call($endpoint, 'delete', $data);
    }    
    
    /**************************************************************************
    * Private functions
    **************************************************************************/
    
    private function call($endpoint, $type, $data=false){
        
        // To-do: Validate endpoints
        // To-do: Validate types
        
        $ch = curl_init();
        
        // Set curl url to call
        if($type == 'oauth'){
            $curlURL = $this->oAuthAccessTokenURL.'?'.http_build_query($data);
        } else {
            $curlURL = $this->apiUrl.$endpoint;
            
	        // Data (if set) has to be array and then converted to json
	        if ($data){
	            $data = json_encode($data);
	        }
                    
        }

        curl_setopt($ch, CURLOPT_URL, $curlURL);
        
        // Setup curl options
        $curl_options = array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_USERAGENT      => 'SageOne-API-PHP',
            CURLOPT_FOLLOWLOCATION => true
        );
        
        switch($type){
        
            case 'post':
                $curl_options = $curl_options + array(
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: Bearer '.$this->accessToken,
                        'Content-Type: application/json'
                    ),
                    CURLOPT_POST        => 1,
                    CURLOPT_POSTFIELDS  => $data
                );
            break;
                
            case 'put':
                $curl_options = $curl_options + array(
                	CURLOPT_CUSTOMREQUEST => 'PUT',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: Bearer '.$this->accessToken,
                        'Content-Type: application/json'
                    ),
                    CURLOPT_POST        => 1,
                    CURLOPT_POSTFIELDS  => $data
                );
            break;
                         
            case 'delete':
                $curl_options = $curl_options + array(
                	CURLOPT_CUSTOMREQUEST => 'DELETE',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: Bearer '.$this->accessToken,
                        'Content-Type: application/json'
                    ),
                    CURLOPT_POST        => 1,
                    CURLOPT_POSTFIELDS  => $data
                );
            break;
                                                
            case 'get':
                $curl_options = $curl_options + array(
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: Bearer '.$this->accessToken
                    )
                );
            break;
                
            case 'oauth':
                $curl_options = $curl_options + array(
                    CURLOPT_HTTPHEADER => array('Accept: application/json'),
                );
            break;
            
        }
                
        // Set curl options
        curl_setopt_array($ch, $curl_options);
        
        // Send the request
        $result = curl_exec($ch);
        $error = curl_errno($ch);
        
        if($this->debug){
            var_dump($result);
            var_dump($error);
        }
        
        // Close the connection
        curl_close($ch);
        
        return json_decode($result, true);
    }
            
}

?>