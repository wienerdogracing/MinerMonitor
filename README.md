MinerMonitor
==============

This is a very basic monitor for your miners.
(see [AUTHORS](AUTHORS) for a list of contributors)

#### Table of contents

* [Dependencies](#dependencies)
* [Download](#download)
* [Usage instructions](#usage-instructions)
* [TODOS](#todos)
* [Donations](#donations)
* [Credits](#credits)
* [License](#license)

Dependencies
============
 * Tested on Raspberry Pi Stretch Lite.
 If you need to setup the Verium wallet on Rasbian Stretch
 ```
 sudo apt-get update
 sudo apt-get upgrade
 sudo apt-get remove libssl-dev
 sudo nano /etc/apt/sources.list
 edit the line below change stretch to jessie
 deb http://mirrordirector.raspbian.org/raspbian/ jessie main contrib non-free rpi
 ctrl x then Y to save
 sudo apt-get update
 sudo apt-get install libssl-dev
 sudo apt-mark hold libssl-dev
 sudo nano /etc/apt/sources.list
 edit the line below change jessie to stretch
 deb http://mirrordirector.raspbian.org/raspbian/ stretch main contrib non-free rpi
 ctrl x then Y to save

 wget https://raw.githubusercontent.com/DJoeDt/verium/master/install_Verium_Wallet.sh
 chmod +x install_Verium_Wallet.sh
 ./install_Verium_Wallet.sh
 Verium Wallet install script credit to https://vrm.mining-pool.ovh/
 ``` 
 * PHP enabled web server
 * fsock enabled for PHP
 * You can run a built in PHP server from your PC instead of a full server

Download
========
 * Git tree:   https://github.com/wienerdogracing/MinerMonitor
 * Clone with `git clone https://github.com/wienerdogracing/MinerMonitor.git`

Usage instructions
==================
#### On miner machines:
 * Run cpuminer with the command line option: `--api-bind 0.0.0.0:4048`
 * Open firewall incoming port tcp:4048 (might not be needed)
 * For Odroids it is suggested to run Fireworm's latest version for correct CPU frequency and Temperature reporting
 * For Odroids soloing or make adjustments for the stratum pool
 ```
 ./cpuminer -o walletIP:33987 -O walletuser:walletpassword -t 2 -1 6 --cpu-affinity-stride 1 --cpu-affinity-default-index 6 --cpu-affinity-oneway-index 0 --api-bind 0.0.0.0:4048
 ```
#### Install Web Server
```
sudo apt-get install -y lighttpd php7.0-cgi
sudo lighty-enable-mod fastcgi
sudo lighty-enable-mod fastcgi-php
sudo nano /etc/lighttpd/lighttpd.conf
```
change = "/var/www/html" to = "/home/pi/MinerMonitor" or to where ever you cloned the repository to above.
```
sudo service lighttpd force-reload
```
#### On Web Server Machine
 * Download or clone repo
 * Modify minerHosts file with a list of your miners to monitor. (IP address or hostname)
 * Modify minerHosts path in config.ini
   * By default it will be in the same folder, but you can place it anywhere as long as you correctly set the path
 * Modify config.ini for solo = TRUE (Default) or solo = FALSE for pool mining
 * Modify config.ini for wallet user, password and address.  (Solo mining only)
 * Open firewall outgoing port tcp:4048 (not needed on the Pi)

#### Finish
 * Point your browser to your webserver

TODOS
=====
 * [ ] Add additional data for pool mining 

Donations
=========
 * VRM Address: VG3g6FqTGbGqhKWcUxpi297AfcM7s4FZEj
 * VRC Address: VLv5zmWsQ5q7Vm2SG4FdD1csRwbGwJeMtb

Credits
=======
MinerMonitor is based on Birty's original version.
Derricke fixed code formatting, remade some functions, added useability, etc.
I added wallet integration for solo mining, better odroid support and added reporting.

License
=======
GPLv3.  See [LICENSE](LICENSE) for details.
