<?php

error_reporting(E_ALL & ~E_NOTICE);

include_once("namecheap_config.php");
include_once("namecheap_letsencrypt.class.php");

/////////////////////////////////////////////////////////////////////////////////
//                                                                             //
//  Namecheap DNS host records update script for Letscrypt DNS challange       //
//                                                                             //
/////////////////////////////////////////////////////////////////////////////////
//                                                                             //
//  !! Edit namecheap_config.php adjust/remove APIKEY, APIUSER and DOMAINS !!  //
//                                                                             //
//  !!!!  Do not run this script from your web server, the Namecheap API !!!!  //
//  !!!!  is way too powerful to leave the APIKEY on your web server     !!!!  //
//                                                                             //
/////////////////////////////////////////////////////////////////////////////////

if($argc != 3 ) {
	echo "Usage: namecheap.php [acme-host-string] [token-value]\ne.g. php namecheap.php _acme-challenge.mail.example.com Rvj61XDf39_3e2YC8pCYiq9vgrVkg1JcQWqnKpvdko\n";
	exit(1);
}

echo "Updating Namecheap DNS...\n";
$up = new namecheap_letsencrypt($config);
$rc = $up->add_letsencrypt_challenge( $argv[1], $argv[2]);

if($rc > 0) echo "Error: ".$up->error; else echo "Done!";

exit($rc);

?>
