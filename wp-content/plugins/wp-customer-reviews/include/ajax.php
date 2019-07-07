<?php
define('DOING_AJAX', true);

$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
require_once($parse_uri[0].'wp-load.php');

$parse_uri = explode('wp-customer-reviews', $_SERVER['SCRIPT_FILENAME']);
require_once($parse_uri[0].'wp-customer-reviews/wp-customer-reviews-3.php');
$WPCustomerReviews3->ajax();
?>