#!/bin/bash
 
# MAJ

echo ""
echo -n "Script 1 : Mettre à jour, installer OpenVPN et configurer le pays Wifi ? [taper oui ou non]:"
echo ""
read answer

if [[ $answer != "oui" ]]; then
        echo "Arret du script"
        exit 0
    fi
	
echo ""	
echo -n "Mettre à jour le mot de passe? [taper oui ou non]: "
echo ""
read answer

if [ "$answer" != 'non' ] && [ "$answer" != 'non' ]; then
        sudo passwd pi
fi

cd /home/pi/install-vpn/ || exit
sudo chmod +x install-raspap.sh
sudo chmod +x install-ecran.sh
sudo raspi-config nonint do_wifi_country FR
sudo apt-get update -y
sudo apt-get install openvpn -y
sudo apt-get dist-upgrade -y
echo ""
echo ""

echo -e "Fin du premier script : redemarrage dans 10sec "
echo ""
sleep 10
sudo reboot