<?php
require_once 'functions.php';

// This script should send GitHub updates to registered emails every 5 minutes.
// Log the execution for debugging
error_log("CRON job executed at " . date('Y-m-d H:i:s'));

try {
    sendGitHubUpdatesToSubscribers();
    error_log("GitHub updates sent successfully at " . date('Y-m-d H:i:s'));
} catch (Exception $e) {
    error_log("Error sending GitHub updates: " . $e->getMessage());
}