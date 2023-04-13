#!/bin/bash

set -e

# Generate SSL certificate and key
SSL_KEY_FILE=/etc/nginx/certs/server.key
CERT_FILE=/etc/nginx/certs/server.crt

if [ ! -f "$SSL_KEY_FILE" ]; then
    echo >&1 "Generating self-signed certificate..."
    openssl req -new -newkey rsa:2048 -days 365 -nodes -x509 \
        -subj "/C=US/ST=California/L=San Francisco/O=My Company/CN=localhost" \
        -keyout $SSL_KEY_FILE \
        -out $CERT_FILE
fi

# Print banner
echo >&1 "
__/\\\\\\\\____________/\\\\\\\\__/\\\\\\\\\\\\\\\\\\\\\_____/\\\\\\\\\\\\\\\\\\\\\____/\\\\\\\\\\\\\\\\\\\\\\\\\___        
 _\/\\\\\\\\\\\\________/\\\\\\\\\\\\_\/////\\\\\///____/\\\\\/////////\\\\\_\/\\\\\/////////\\\\\_       
  _\/\\\\\//\\\\\____/\\\\\//\\\\\_____\/\\\\\______\//\\\\\______\///__\/\\\\\_______\/\\\\\_      
   _\/\\\\\\\\///\\\\\/\\\\\/_\/\\\\\_____\/\\\\\_______\////\\\\\_________\/\\\\\\\\\\\\\\\\\\\\\\\\\/__     
    _\/\\\\\__\///\\\\\/___\/\\\\\_____\/\\\\\__________\////\\\\\______\/\\\\\/////////____    
     _\/\\\\\____\///_____\/\\\\\_____\/\\\\\_____________\////\\\\\___\/\\\\\_____________   
      _\/\\\\\_____________\/\\\\\_____\/\\\\\______/\\\\\______\//\\\\\__\/\\\\\_____________  
       _\/\\\\\_____________\/\\\\\__/\\\\\\\\\\\\\\\\\\\\\_\///\\\\\\\\\\\\\\\\\\\\\/___\/\\\\\_____________ 
        _\///______________\///__\///////////____\///////////_____\///______________
         ____________________________________________________________________________
          _______________________________________________Threat Sharing Platform______
          
"

sleep 0.2
echo >&1 "

  @@@@@@ @@@  @@@  @@@@@@  @@@@@@@  @@@@@@@@                                           
 !@@     @@!  @@@ @@!  @@@ @@!  @@@ @@!                                                
  !@@!!  @!@!@!@! @!@!@!@! @!@!!@!  @!!!:!                                             
     !:! !!:  !!! !!:  !!! !!: :!!  !!:                                                
 ::.: :   :   : :  :   : :  :   : : : :: :::"

sleep 0.2
echo >&1 "
                                                                            
 @@@ @@@  @@@@@@  @@@  @@@ @@@@@@@                                                     
 @@! !@@ @@!  @@@ @@!  @@@ @@!  @@@                                                    
  !@!@!  @!@  !@! @!@  !@! @!@!!@!                                                     
   !!:   !!:  !!! !!:  !!! !!: :!!                                                     
   .:     : :. :   :.:: :   :   : :"

sleep 0.2
echo >&1 "

 @@@@@@@  @@@       @@@@@@   @@@@@@  @@@@@@@  @@@ @@@                                  
 @@!  @@@ @@!      @@!  @@@ @@!  @@@ @@!  @@@ @@! !@@                                  
 @!@!@!@  @!!      @!@  !@! @!@  !@! @!@  !@!  !@!@!                                   
 !!:  !!! !!:      !!:  !!! !!:  !!! !!:  !!!   !!:                                    
 :: : ::  : ::.: :  : :. :   : :. :  :: :  :    .:"                           

sleep 0.2
echo >&1 "

 @@@ @@@  @@@ @@@@@@@  @@@  @@@@@@@  @@@@@@  @@@@@@@  @@@@@@  @@@@@@@   @@@@@@         
 @@! @@!@!@@@ @@!  @@@ @@! !@@      @@!  @@@   @@!   @@!  @@@ @@!  @@@ !@@             
 !!@ @!@@!!@! @!@  !@! !!@ !@!      @!@!@!@!   @!!   @!@  !@! @!@!!@!   !@@!!          
 !!: !!:  !!! !!:  !!! !!: :!!      !!:  !!!   !!:   !!:  !!! !!: :!!      !:!         
 :   ::    :  :: :  :  :    :: :: :  :   : :    :     : :. :   :   : : ::.: : 
 
 
 
 "

# Start nginx
nginx -g "daemon off;"
