FROM ubuntu

RUN apt-get -y update \
 && apt-get -y upgrade \
 && apt-get -y install wget libcurl4-openssl-dev libjansson-dev unzip lighttpd php7.0-cgi git \
 && lighty-enable-mod fastcgi \
 && lighty-enable-mod fastcgi-php \
 && git clone https://github.com/wienerdogracing/MinerMonitor.git \
 && mv MinerMonitor /opt/MinerMonitor \
 && sed -i 's%/var/www/html%/opt/MinerMonitor%g' /etc/lighttpd/lighttpd.conf \
 && mkdir -p /var/run/lighttpd \
 && chmod 777 /var/run/lighttpd

COPY config.ini /opt/MinerMonitor/config.ini
COPY minerHosts /opt/MinerMonitor/minerHosts

EXPOSE 80

CMD ["lighttpd", "-D", "-f", "/etc/lighttpd/lighttpd.conf"]
