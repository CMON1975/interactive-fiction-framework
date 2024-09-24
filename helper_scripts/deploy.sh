#!/bin/bash

# Define the source paths
SOURCE_PAGES="../pages/index.php"
SOURCE_BACKEND="../backend/"

# Define the destination path
DESTINATION="/var/www/html/"

# Copy the index.php file to /var/www/html/
echo "Copying index.php to $DESTINATION"
sudo cp $SOURCE_PAGES $DESTINATION

# Copy the backend/ folder and its contents to /var/www/html/
echo "Copying backend/ folder to $DESTINATION"
sudo cp -r $SOURCE_BACKEND $DESTINATION

# Output success message
echo "Deployment complete."
