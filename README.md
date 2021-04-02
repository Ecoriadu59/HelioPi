# HelioPi

## Matos:

[Raspberry pi](https://amzn.to/2Jx63LR) \
[Carte micro sd](https://amzn.to/2Wk9dsa) \
[Support batterie](https://amzn.to/2Wk9Vpk) \
[Ecran](https://amzn.to/2VJd1yI) \
[Model 3D](https://www.thingiverse.com/thing:3646338)

## Vidéo:

[![](http://img.youtube.com/vi/kWujr37V1BY/0.jpg)](http://www.youtube.com/watch?v=kWujr37V1BY "")

## Go:

Télécharger l’archive “install-vpn” [ICI](https://github.com/Ecoriadu59/HelioPi/releases/tag/V1.0) \
Installer Putty, FileZilla et IP Scanner \
Déterminer l’adresse du Raspberry grâce à Advanced Ip Scanner \
Avec Filezilla connecter vous au Pi en sftp et glisser le dossier install-vpn de votre ordinateur vers le dossier distant par défaut du RaspberryPi (identifiant par defaut : pi / mot de passe : raspberry) \
Ensuite connectez vous avec Putty et insérez les commandes suivantes :  \
cd install-vpn \
sudo chmod +x install-update.sh \
sudo ./install-update.sh \
Suivez les instructions \
Après redémarrage : Se reconnecter via Putty \
cd install-vpn \
sudo ./install-raspap.sh \
Suivez les instructions \
Après redémarrage : Se reconnecter via Putty \
cd install-vpn \
sudo ./install-ecran.sh \
Suivez les instructions \
Après redémarrage : Se reconnecter via Putty \
cd install-vpn \
sudo ./screen.sh \
cd /var/www/html/includes \
sudo nano auth.txt \
Inserer vos identifiants en lieu et place de ceux presents. \
Effectuez un “ctrl+x” \
Puis tapez “y” \
Puis “Entrée” \
Redémarrez

## Fin
