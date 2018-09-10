<?php

class Rankinity
{
    public $api_key;
    public $api_endpoint = 'https://my.rankinity.com/api/v2/';
    public $client;
    public $query = array();

    public function __construct($api_key){
        $this->api_key = $api_key;
        $this->query[ 'token' ] = $this->api_key;
    }

    public function setApiKey( $api_key ){
        $this->api_key = $api_key;
        $this->query[ 'token' ] = $this->api_key;
    }

    private function doRequest( $opts ){
        $url = $opts[ 'url' ];
        // create curl resource 
        $ch = curl_init(); 

        // set url 
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

        // $output contains the output string 
        $output = curl_exec($ch); 

        // close curl resource to free up system resources 
        curl_close($ch);
        return $output;
        /*
        //API URL
        $url = 'http://www.example.com/api';

        //create a new cURL resource
        $ch = curl_init($url);

        //setup request to send json via POST
        $data = array(
            'username' => 'codexworld',
            'password' => '123456'
        );
        $payload = json_encode(array("user" => $data));

        //attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        //set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        //return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute the POST request
        $result = curl_exec($ch);

        //close cURL resource
        curl_close($ch);
        */
    }

    public function getProjects( $params = array() ){
        $url = $this->api_endpoint.'projects.json?';
        $query = array_merge($this->query, $params);
        $opt = array();
        $opt[ 'url' ] = $url . http_build_query($query);
        return json_decode( $this->doRequest( $opt ), true );
    }

    public function getProjectEngine($params = array()){
        $url = $this->api_endpoint.'projects/search_engines.json?';
        $query = array_merge($this->query, $params);
        $opt = array();
        $opt[ 'url' ] = $url . http_build_query($query);
        return json_decode( $this->doRequest( $opt ), true );
    }

    public function getProjectEngineKeywords($params = array()){
        $url = $this->api_endpoint.'projects/search_engines.json?';
        $query = array_merge($this->query, $params);
        $opt = array();
        $opt[ 'url' ] = $url . http_build_query($query);
        return json_decode( $this->doRequest( $opt ), true );
    }

    public function getProjectEngineRanks($params = array()){
        $url = $this->api_endpoint.'projects/ranks.json?';
        $query = array_merge($this->query, $params);
        $opt = array();
        $opt[ 'url' ] = $url . http_build_query($query);
        return json_decode( $this->doRequest( $opt ), true );
    }

    public function getProjectEngineVisibilities($params = array()){
        $url = $this->api_endpoint.'projects/visibilities.json?';
        $query = array_merge($this->query, $params);
        $opt = array();
        $opt[ 'url' ] = $url . http_build_query($query);        
        return json_decode( $this->doRequest( $opt ), true );
    }
}