#!/bin/bash

echo ""
echo -e "Script 3 : Installation interface graphique"
echo ""
echo ""
sleep 2
# WEBUI
echo ""
echo -e "INSTALLATION INTERFACE ECRAN GPIO"
echo ""
echo ""
sleep 2
sudo /bin/cp -rf /home/pi/install-vpn/html/* /var/www/html

# PERMISSION
echo ""
echo -e "INSTALLATION DES PERMISSIONS"
echo ""
echo ""
sleep 2
cd /home/pi/install-vpn/ || exit
sudo chmod +x permissions.sh
sudo ./permissions.sh

# INSTALL
echo ""
echo -e "INSTALLATION DU MODE FULLSCREEN"
echo ""
echo ""
sleep 2
sudo apt-get install xdotool unclutter sed

# INSTALL SERVICES
echo ""
echo -e "INSTALLATION DES SERVICES AU DEMARRAGE!"
echo ""
echo ""
sleep 2
sudo /bin/cp /home/pi/install-vpn/kiosk.sh /home/pi/kiosk.sh
sudo /bin/cp /home/pi/install-vpn/kiosk.service /lib/systemd/system/kiosk.service
sudo systemctl enable kiosk.service
cd /home/pi/install-vpn/ || exit
sudo chmod +x screen.sh

#CONF ECRAN
echo ""
echo -e "CONFIGURATION DE L'ECRAN"
echo ""
echo ""
sleep 2
cd /home/pi || exit
git clone https://github.com/waveshare/LCD-show.git
cd LCD-show/ || exit
sudo ./LCD35-show 180
sudo reboot
 
