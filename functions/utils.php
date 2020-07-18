<?php
function sanitiseOutput($str) {
	return htmlentities($str, ENT_QUOTES, 'utf-8');
}

// Generate a random string for cross-site request forgery prevention 
function generateFormValidationHash($userId) {
	$time = microtime();
	$base = $userId . $time;
	return hash('sha256', $base);
}


