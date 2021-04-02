#!/bin/bash

cat >> /etc/sudoers << EOF
www-data ALL=(ALL) NOPASSWD: /usr/sbin/openvpn
www-data ALL=(ALL) NOPASSWD: /usr/bin/killall
EOF

