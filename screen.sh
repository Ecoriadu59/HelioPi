#!/bin/bash


echo ""
echo -e "MODIFICATION RESOLUTION ECRAN"
echo ""
echo ""

cat >> /boot/config.txt << EOF
framebuffer_width=720
framebuffer_height=480
EOF
