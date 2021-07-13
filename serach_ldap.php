<?php

   $ad = ldap_connect("ldap://172.16.1.5")
          or die("Couldn't connect to AD!");
  
   
    $bd = ldap_bind($ad,"STI\chandram","Reset123") or die("Couldn't bind to AD!");

	echo "login successfull >> ". $bd; 
    ldap_unbind($ad);

    //ldap_close($ad);
?>