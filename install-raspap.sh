#!/bin/bash
 
# INSTALL RASPAP

echo ""
echo -e "Script 2 : Installer le hotspot WIFI"
echo -e "Repondez 'y' à toutes les questions"
echo ""

sleep 2
wget -q https://git.io/voEUQ -O /tmp/raspap && bash /tmp/raspap

 
