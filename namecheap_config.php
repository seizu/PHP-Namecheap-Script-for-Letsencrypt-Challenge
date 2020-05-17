<?php
  
$config = [
    "proxy" => "",         // e.g. "127.0.0.1:9999",
    "proxyauth" => "",     // e.g. "user:password",
            
    "endpoint" => 'https://api.namecheap.com/xml.response',
    "domains" => [
        "exampledomain1.com" =>        [ "apikey" => "----NAMECHEAP-APIKEY1---", "apiuser" => "--NAMECHEAP-APIUSER1--",  "dnsdata" =>[] ], 
        "exampledomain2.com" =>        [ "apikey" => "----NAMECHEAP-APIKEY1---", "apiuser" => "--NAMECHEAP-APIUSER1--",  "dnsdata" =>[] ], 
        "exampledomain3.com" =>        [ "apikey" => "----NAMECHEAP-APIKEY2---", "apiuser" => "--NAMECHEAP-APIUSER2--",  "dnsdata" =>[] ]
    ]
];
    
?>
