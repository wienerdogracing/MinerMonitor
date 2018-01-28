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
 * Tested on Raspberry Pi Stretch Lite
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
 * Open firewall incoming port tcp:4048 (not needed on the Raspberry Pi)
 
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
