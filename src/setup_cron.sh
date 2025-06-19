#!/bin/bash
# This script should set up a CRON job to run cron.php every 5 minutes.

# Get the current directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CRON_PHP_PATH="$SCRIPT_DIR/cron.php"

# Create the cron job entry
CRON_JOB="*/5 * * * * /usr/bin/php $CRON_PHP_PATH"

# Check if the cron job already exists
if crontab -l 2>/dev/null | grep -q "$CRON_PHP_PATH"; then
    echo "CRON job already exists for $CRON_PHP_PATH"
else
    # Add the new cron job to the existing crontab
    (crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -
    echo "CRON job added successfully: $CRON_JOB"
fi

# Display current crontab to verify
echo "Current crontab entries:"
crontab -l