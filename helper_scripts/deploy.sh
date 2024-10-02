#!/bin/bash

# Define the source paths
SOURCE_PAGES="../pages/"
SOURCE_BACKEND="../backend/"

# Define the destination path
DESTINATION="/var/www/html/"

# Copy the contents of the pages directory directly to /var/www/html/
echo "Copying files from $SOURCE_PAGES to $DESTINATION"
sudo cp -r $SOURCE_PAGES* $DESTINATION

# Ensure the backend directory exists at /var/www/html/backend/
echo "Ensuring backend directory exists at $DESTINATION/backend/"
sudo mkdir -p $DESTINATION/backend/

# Copy the backend/ folder and its contents to /var/www/html/backend/
echo "Copying backend/ folder to $DESTINATION/backend/"
sudo cp -r $SOURCE_BACKEND* $DESTINATION/backend/

# Output success message
echo "Deployment complete."
