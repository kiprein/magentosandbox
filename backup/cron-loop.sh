#!/bin/bash
set -ex
exec >> /var/log/cron-loop.log 2>&1

MAGENTO_ROOT="/var/www/html"
LOCAL_XML="$MAGENTO_ROOT/app/etc/local.xml"

echo "[Cron] Generating local.xml if needed..."
php /usr/local/bin/generate-local-xml.php

echo "[Cron] Waiting for Magento local.xml to be available..."

while [ ! -f "$LOCAL_XML" ]; do
  echo "[Cron] Waiting... local.xml not found yet. Sleeping 10s."
  sleep 10
done

echo "[Cron] Found local.xml! Starting cron loop."

while true; do
  echo "[Cron] Running safe-cron.php at $(date)"
  php /var/www/html/safe-cron.php || true
  sleep 300
done
