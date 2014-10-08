<?php

/*
Library Name: SageOne-API-PHP
Description: A PHP Library to access the SageOne API
Version: 1.1
Author: Eddie Harrison

Copyright 2014 Ryan Mitchell

*/

class SageOne {
    
    private $clientId;
    private $clientSecret;
    private $signingSecret;
    
    private $oAuthAuthoriseURL = 'https://www.sageone.com/oauth2/auth';
    private $oAuthAccessTokenURL = 'https://api.sageone.com/oauth2/token';
    private $accessToken;
    
    private $apiUrl = 'https://api.sageone.com/accounts/v1';
    
    private $debug = false;
    
    function __construct($clientId, $clientSecret, $signingSecret){
        
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->signingSecret = $signingSecret;
    }
    
    public function getAuthoriseURL($callback){
        
        $authoriseURL  = $this->oAuthAuthoriseURL;
        $authoriseURL .= '?response_type=code';
        $authoriseURL .= '&client_id='.$this->clientId;
        $authoriseURL .= '&redirect_uri='.urlencode($callback);
        $authoriseURL .= '&scope=full_access';
        
        return $authoriseURL;
    }
    
    public function getAccessToken($code, $callback){
        
        if ($this->accessToken){
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
    
    public function getRefreshToken($code, $callback){
        
        if ($this->accessToken){
            return $this->accessToken;
        }
        
        $params = array(
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $code,
            'grant_type' => 'refresh_token'
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
    
    private function ksortRecursive(&$array, $sort_flags = SORT_REGULAR){
		
		if (!is_array($array)) return false;
		
		ksort($array, $sort_flags);
		
		foreach ($array as &$arr){
			$this->ksortRecursive($arr, $sort_flags);
		}
		
		return true;
		
	}
    
    private function buildSignature($method, $url, $params, $nonce){
        
        // uc method and append &
    	$signature = strtoupper($method).'&';
    	
    	// percent encode bit of url before ? and append &
    	$signature .= rawurlencode(array_shift(explode('?', $url))).'&';
    	
    	// percent encode any params and append &
    	if (is_array($params)){
    	    	
    		// sort params alphabetically
    		$this->ksortRecursive($params);
    		    		
    		// build query string from params, encode it and append &
    		$signature .= str_replace(
    			array('%2B'), 
    			array('%2520'), 
    			rawurlencode(http_build_query($params, '', '&'))
    		).'&';
    	
    	// params can be string
    	} else {
	    	
    		// build query string from params, encode it and append &
    		$signature .= rawurlencode($params).'&';

    	}
    	
    	// add 'nonce' - just use an md5
    	$signature .= $nonce;
    	    	    	
    	// now generate signing key
    	$signingKey = rawurlencode($this->signingSecret).'&'.rawurlencode($this->accessToken);
    	
    	// encode using sha 1, then base64 encode    	
    	$finalSignature = base64_encode(hash_hmac('sha1', $signature, $signingKey, true));
    	
    	return $finalSignature;
	    
    }
    
    private function call($endpoint, $type, $data=false){
        
        // To-do: Validate endpoints
        // To-do: Validate types
        
        $ch = curl_init();
        
        // Set curl url to call
        if($type == 'oauth'){
            $curlURL = $this->oAuthAccessTokenURL;
        } else {
            $curlURL = $this->apiUrl.$endpoint;
        }

        curl_setopt($ch, CURLOPT_URL, $curlURL);
        
        // Setup curl options
        $curl_options = array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_USERAGENT      => 'DepotHQ.com',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false
        );
        
        // generate a nonce
        $nonce = md5(time().uniqid());
        
        switch ($type){
        
            case 'post':
                $curl_options = $curl_options + array(
                    CURLOPT_HTTPHEADER => array(
                        'Accept: */*',
                        'Authorization: Bearer '.$this->accessToken,
                        'X-Signature: '.$this->buildSignature($type, $curlURL, $data, $nonce),
                        'X-Nonce: '.$nonce,
                        'Content-Type: application/x-www-form-urlencoded',
                        'User-Agent: DepotHQ.com'
                    ),
                    CURLOPT_POST        => 1,
                    CURLOPT_POSTFIELDS  => http_build_query($data, '', '&')
                );
                
            break;
                
            case 'put':
                $curl_options = $curl_options + array(
                	CURLOPT_CUSTOMREQUEST => 'PUT',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: */*',
                        'Authorization: Bearer '.$this->accessToken,
                        'X-Signature: '.$this->buildSignature($type, $curlURL, $data, $nonce),
                        'X-Nonce: '.$nonce,
                        'Content-Type: application/x-www-form-urlencoded',
                        'User-Agent: DepotHQ.com'
                    ),
                    CURLOPT_POST        => 1,
                    CURLOPT_POSTFIELDS  => http_build_query($data, '', '&')
                );
                
            break;
                         
            case 'delete':
                $curl_options = $curl_options + array(
                	CURLOPT_CUSTOMREQUEST => 'DELETE',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: */*',
                        'Authorization: Bearer '.$this->accessToken,
                        'X-Signature: '.$this->buildSignature($type, $curlURL, array(), $nonce),
                        'X-Nonce: '.$nonce,
                        'Content-Type: application/x-www-form-urlencoded',
                        'User-Agent: DepotHQ.com'
                    ),
                    CURLOPT_POST        => 1,
                    CURLOPT_POSTFIELDS  => http_build_query($data, '', '&')
                );
            break;
                                                
            case 'get':
            
            	$params = array();
            	
            	// build url params into array
            	if (sizeof(explode('?', $curlURL)) > 1){
	            	$queryString = array_pop(explode('?', $curlURL));
	            	parse_str($queryString, $params);
            	}
            
                $curl_options = $curl_options + array(
                    CURLOPT_HTTPHEADER => array(
                        'Accept: */*',
                        'Authorization: Bearer '.$this->accessToken,
                        'X-Signature: '.$this->buildSignature($type, $curlURL, $params, $nonce),
                        'X-Nonce: '.$nonce,
                        'Content-Type: application/x-www-form-urlencoded',
                        'User-Agent: DepotHQ.com'
                    )
                );
            break;
                
            case 'oauth':
                $curl_options = $curl_options + array(
                    CURLOPT_HTTPHEADER => array(
                    	'Accept: */*',
                        'User-Agent: DepotHQ.com'
                    ),
                    CURLOPT_POST        => 1,
                    CURLOPT_POSTFIELDS  => $data
                );
            break;
            
        }
                
        // Set curl options
        curl_setopt_array($ch, $curl_options);
        
        // Send the request
        $this->result = $result = curl_exec($ch);
        $this->error = $error = curl_errno($ch);
        $this->httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($this->debug){
            var_dump($result);
            var_dump($error);
            var_dump(curl_getinfo($ch, CURLINFO_HTTP_CODE));
        }
                
        // Close the connection
        curl_close($ch);
        
        return json_decode($result, true);
    }
            
}

?>