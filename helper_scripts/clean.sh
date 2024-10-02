#!/bin/bash

# Define the destination path
DESTINATION="/var/www/html/"

# Check if the user wants to proceed
echo "This will clean (delete) all files in $DESTINATION. Do you want to continue? (y/n)"
read confirmation

if [ "$confirmation" == "y" ]; then
    # Remove all files and directories in /var/www/html/
    echo "Cleaning $DESTINATION..."
    sudo rm -rf ${DESTINATION}*

    # Optionally, recreate any necessary folder structure
    echo "Recreating necessary folder structure..."
    sudo mkdir -p $DESTINATION/backend/

    # Output success message
    echo "Cleanup complete. The $DESTINATION folder has been reset."
else
    echo "Operation canceled."
fi
