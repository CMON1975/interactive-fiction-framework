#!/bin/bash

# Define the source paths
SOURCE_PAGES="../pages/"
SOURCE_BACKEND="../backend/"

# Define the remote server and target directory
REMOTE_SERVER="cmon1975.com"
REMOTE_DIR="/var/www/if.cmon1975.com/"

# Define a temporary directory on the remote server
TEMP_DIR="/tmp/deploy_temp/"

# Ask for the sudo password upfront
read -sp "Enter the sudo password for $REMOTE_SERVER: " SUDO_PASS
echo

# Check if a flag was provided
if [ "$1" == "--remote" ]; then
    echo "Deploying to remote server $REMOTE_SERVER..."

    # Create a temporary directory on the remote server
    echo "Creating temporary directory on the remote server"
    ssh $REMOTE_SERVER "mkdir -p $TEMP_DIR"

    # Copy the contents of the pages directory to the remote server's temporary directory
    echo "Copying files from $SOURCE_PAGES to $REMOTE_SERVER:$TEMP_DIR"
    scp -r $SOURCE_PAGES* $REMOTE_SERVER:$TEMP_DIR

    # Ensure the backend directory exists in the temporary directory on the remote server
    echo "Copying backend files to $TEMP_DIR/backend/ on $REMOTE_SERVER"
    ssh $REMOTE_SERVER "mkdir -p $TEMP_DIR/backend/"
    scp -r $SOURCE_BACKEND* $REMOTE_SERVER:$TEMP_DIR/backend/

    # Move the files from the temporary directory to the final destination using sudo
    echo "Moving files from $TEMP_DIR to $REMOTE_DIR using sudo on $REMOTE_SERVER"
    ssh $REMOTE_SERVER "echo $SUDO_PASS | sudo -S mv $TEMP_DIR* $REMOTE_DIR"

    # Clean up the temporary directory on the remote server
    ssh $REMOTE_SERVER "rm -rf $TEMP_DIR"

    echo "Remote deployment complete."

elif [ "$1" == "--local" ] || [ -z "$1" ]; then
    echo "Deploying locally to $LOCAL_DEST..."

    # Copy the contents of the pages directory to the local destination
    echo "Copying files from $SOURCE_PAGES to $LOCAL_DEST"
    sudo cp -r $SOURCE_PAGES* $LOCAL_DEST

    # Ensure the backend directory exists locally
    echo "Ensuring backend directory exists at $LOCAL_DEST/backend/"
    sudo mkdir -p $LOCAL_DEST/backend/

    # Copy the backend/ folder locally
    echo "Copying backend/ folder to $LOCAL_DEST/backend/"
    sudo cp -r $SOURCE_BACKEND* $LOCAL_DEST/backend/

    echo "Local deployment complete."

else
    echo "Usage: $0 [--local | --remote]"
    exit 1
fi
