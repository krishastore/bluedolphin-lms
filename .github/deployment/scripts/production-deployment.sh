#!/bin/bash

###################################################
# Bash script to deploy code from git to production environment.
# Author: krishaweb

# Print message about deploying to production environment
echo -e "\n\nDeploying project on ${PROD_ENV_IP} environment\n"

# SSH into the production server and execute commands remotely
ssh -o StrictHostKeyChecking=no "${PROD_ENV_USER}@${PROD_ENV_IP}" bash <<EOF

# Check if root directory exists
if [ -d "/var/www/" ]; then
    # Change directory from root to project.
    echo "Root directory exists"
    cd /var/www/
fi

# Check if project directory does not exist
if [ ! -d bluedolphinlms ]; then
    # Clone the repository if project directory does not exist
    git clone git@github.com:krishastore/bluedolphin-lms.git bluedolphinlms
fi

# Change directory to the project directory
cd bluedolphinlms

# Switch to the main branch
git checkout main

# Reset the repository to the latest commit
# git reset --hard

# Pull the latest changes from the main branch
git pull origin main

# Check if composer.json file exists
if [ -f "composer.json" ]; then
    # Run composer install if composer.json exists
    echo "composer.json and composer.lock exist"
    composer install
else
    echo "composer.json not found. Skipping PHP dependency installation."
fi

# Load NVM and Node.js environment
export NVM_DIR="\$HOME/.nvm"
[ -s "\$NVM_DIR/nvm.sh" ] && \. "\$NVM_DIR/nvm.sh"  # This loads nvm
[ -s "\$NVM_DIR/bash_completion" ] && \. "\$NVM_DIR/bash_completion"  # This loads nvm bash_completion

# Check if package.json file exists
if [ -f "package.json" ]; then
    # Run package install if package.json exists
    echo "package.json and package-lock.json exist"
    npm ci
    echo "Building the project..."
    npm run build
else
    echo "package.json not found. Skipping Node.js dependency installation."
fi

echo "Deployment completed successfully."
EOF
