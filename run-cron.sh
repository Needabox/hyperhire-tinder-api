#!/bin/bash

# Set default port jika PORT tidak diset oleh Railway
PORT=${PORT:-8080}

# Start Laravel web server di background
php artisan serve --host=0.0.0.0 --port=$PORT &

# Simpan PID dari web server
WEB_SERVER_PID=$!

# Trap untuk memastikan proses dihentikan dengan benar saat script dihentikan
trap "kill $WEB_SERVER_PID 2>/dev/null; exit" SIGTERM SIGINT

# Loop forever: jalankan scheduler setiap 1 menit
while true
do
  php artisan schedule:run >> /dev/null 2>&1
  sleep 60
done
