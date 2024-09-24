#!/bin/bash

# Stop Apache
echo "Stopping Apache..."
sudo systemctl stop apache2

# Check if Apache stopped successfully
if ! systemctl is-active --quiet apache2; then
    echo "Apache stopped successfully."
else
    echo "Failed to stop Apache."
fi

# Stop MySQL
echo "Stopping MySQL..."
sudo systemctl stop mysql

# Check if MySQL stopped successfully
if ! systemctl is-active --quiet mysql; then
    echo "MySQL stopped successfully."
else
    echo "Failed to stop MySQL."
fi
