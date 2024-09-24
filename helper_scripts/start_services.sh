#!/bin/bash

# Start Apache
echo "Starting Apache..."
sudo systemctl start apache2

# Check if Apache started successfully
if systemctl is-active --quiet apache2; then
    echo "Apache started successfully."
else
    echo "Failed to start Apache."
fi

# Start MySQL
echo "Starting MySQL..."
sudo systemctl start mysql

# Check if MySQL started successfully
if systemctl is-active --quiet mysql; then
    echo "MySQL started successfully."
else
    echo "Failed to start MySQL."
fi
