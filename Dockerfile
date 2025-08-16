# Base image
FROM centos:7

# Point to Vault repos and install Remi PHP 5.6, Apache & PHP extensions
RUN sed -i 's|mirrorlist=|#mirrorlist=|g' /etc/yum.repos.d/CentOS-*.repo \
 && sed -i 's|#baseurl=http://mirror.centos.org|baseurl=http://vault.centos.org|g' /etc/yum.repos.d/CentOS-*.repo \
 && yum install -y yum-utils epel-release which \
 && yum install -y https://rpms.remirepo.net/enterprise/remi-release-7.rpm \
 && yum-config-manager --enable remi-php56 \
 && yum install -y \
      php \
      php-cli \
      php-mysql \
      php-gd \
      php-mbstring \
      php-xml \
      php-mcrypt \
      httpd

# Enable .htaccess overrides and open up /var/www/html
RUN sed -i '/<Directory "\/var\/www\/html">/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/; \
            /<Directory "\/var\/www\/html">/,/<\/Directory>/ s/Require all denied/Require all granted/; \
            s/^Listen 80$/Listen 0.0.0.0:80/' \
       /etc/httpd/conf/httpd.conf

WORKDIR /var/www/html

# Copy your Magento code (including your manually-created local.xml)
COPY backup/ /var/www/html

# Fix permissions
RUN chown -R apache:apache /var/www/html \
 && chmod -R 755 /var/www/html

# Simple startup script
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80
CMD ["/usr/local/bin/start.sh"]
