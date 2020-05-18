PHP-Namecheap-Script-for-Letsencrypt-Challenge
==============================================

This PHP command line script can be used to add a Letsencrypt "DNS-01 challenge" record to your 
Namecheap DNS. Namcheap API is used to add such TXT record. It can handle multiple domains 
with different Namcheap-APIKEYs. The script expects only two input parameters provided 
by Letsencrypt during the SSL validation process, an ACME-HOST-String and a TOKEN-String.

More information: https://letsencrypt.org/docs/challenge-types/

Installation
------------
1) Install it on your preferred operating system where PHP 5 (or higher) is installed.
2) Copy all script files into YOUR_HOME_DIR.
3) PHP extension CURL must be enabled (php.ini).
4) Edit your domains, Namecheap API credentials and proxy settings in "namecheap_config.php"!
5) Run namecheap.php from the command line. E.g.:
```
   php namecheap.php _acme-challenge.mail.example.com Rvj61XDf393e2YC8pCYiq9vgrVkg1JcQWqnKpvdko
```   

!!Warning!!! There are a lot of things you can do with the Namecheap API. Therefore it is not 
recommended to install the script on your web server unless you want to expose your API key to others.
