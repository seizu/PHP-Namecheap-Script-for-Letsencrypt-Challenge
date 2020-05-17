<?php

class namecheap_letsencrypt
{
    private $config;
    private $tld;
    private $sld;
    private $domain;
    private $response;
    public  $error;
    private $clientIP;
    
    public function __construct(&$config) {
        $this->config = $config;
    }

    public function add_letsencrypt_challenge($acme_string, $token) {
        // get own public IP address
        if($this->send_request("", "https://api.ipify.org","","GET") === false) {
            return 1;
        }
        $this->clientIP = $this->response;
        $aHost = explode(".",$acme_string);
        $tld = $aHost[count($aHost)-1];
        $sld = $aHost[count($aHost)-2];
        $domain = "$sld.$tld";
                
        if( !isset($this->config['domains'][$domain]) ) { $this->error="No config found for domain: ".$domain; return 2; }
        $this->domain = $domain;
        $this->tld = $tld;
        $this->sld = $sld;      
        $host = explode(".$sld.$tld",$acme_string);
        $host = $host[0];
        
        // read current host records
        ////////////////////////////
        $postData = [];
        $postData["Command"] = "namecheap.domains.dns.getHosts";
        $this->build_post_data($postData);      
        $postString = http_build_query($postData, "","&",PHP_QUERY_RFC3986);
        if($this->send_request($postString, $this->config['endpoint']) === false) return 3;
                                
        // write host records + Letsencrypt challenge
        //////////////////////////////////////////////
        $this->prepare_dnsdata();
        //add Letscrypt challenge record
        array_push($this->config['domains'][$this->domain]["dnsdata"], ["TXT", $host, $token, "", "300"]);
        $postData = [];
        $postData["Command"] = "namecheap.domains.dns.setHosts";
        $this->build_post_data($postData);  
        $postString = http_build_query($postData, "","&",PHP_QUERY_RFC3986);
        if($this->send_request($postString, $this->config['endpoint']) === false) return 4;
   
        return 0;
    }

    private function prepare_dnsdata() {
        $xml = simplexml_load_string($this->response);
        $obj = $xml->xpath("//*[local-name()='host']");
        foreach ($obj as $key=>$value) {
            // ignore existing TXT _acme-challenge.*
            if(((string)$value->attributes()->Type) == "TXT" && preg_match('/^_acme-challenge\./',((string)$value->attributes()->Name))) continue;
            
            array_push($this->config['domains'][$this->domain]["dnsdata"],[
            (string)$value->attributes()->Type, 
            (string)$value->attributes()->Name,
            (string)$value->attributes()->Address,
            (string)$value->attributes()->MXPref,
            (string)$value->attributes()->TTL]
            );
        }   
    }

    private function send_request($postString, $endpoint, $basicauth="", $method='POST') {
        $ch = curl_init();
        $opts = array(
            CURLOPT_URL            => $endpoint,
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT      => "php_curl",
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_SSL_VERIFYPEER => false
        );
        
        if($method == 'POST') {
            $opts[CURLOPT_POST] = true; 
            $opts[CURLOPT_HTTPHEADER] = array('Content-type: application/x-www-form-urlencoded');
            $opts[CURLOPT_POSTFIELDS] = $postString;
        } else {
            $opts[CURLOPT_POST] = false;
            $opts[CURLOPT_HTTPHEADER] = array('Content-type: text/plain');
        }
        
        $opts[CURLOPT_PROXY] = $this->config['proxy'];
        $opts[CURLOPT_PROXYUSERPWD] = $this->config['proxyauth'];
        $opts[CURLOPT_USERPWD] = $basicauth;
        
        curl_setopt_array( $ch, $opts );
        $this->response = curl_exec( $ch );
        $this->error = curl_error( $ch );
        curl_close( $ch );

        if($this->response === false) {
            return false;
        } else {            
            $xml = @simplexml_load_string($this->response);
            if($xml === false) return true;
            $this->error = (string)($xml->xpath("//*[local-name()='Errors']")[0]->Error);
            if( $this->error != "") return false;
        }
        
        return true;
    }

    private function build_post_data(&$postData) {
        $inx = 1;
        $arr = $this->config['domains'][$this->domain]["dnsdata"];

        $postData["apikey"] = $this->config['domains'][$this->domain]["apikey"];
        $postData["apiuser"] = $this->config['domains'][$this->domain]["apiuser"];
        $postData["username"] = $this->config['domains'][$this->domain]["apiuser"];
        $postData["ClientIp"] = $this->clientIP;
        $postData["TLD"] = $this->tld;
        $postData["SLD"] = $this->sld;

        foreach($arr as $ax) {      
            $postData["RecordType$inx"] = $ax[0];
            $postData["HostName$inx"] = $ax[1];
            $postData["Address$inx"] = $ax[2];
            $postData["MXPref$inx"] = $ax[3];
            $postData["TTL$inx"] = $ax[4];
            $inx++;
        }

        return $postData;
    }
}
?>
