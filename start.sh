#!/usr/bin/env bash
set -e

# Allow Render-style PORT override, default to 80
PORT=${PORT:-80}
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/httpd/conf/httpd.conf

# Start Apache in foreground
exec httpd -D FOREGROUND
