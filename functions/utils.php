<?php
function sanitiseOutput($str) {
	return htmlentities($str, ENT_QUOTES, 'utf-8');
}
