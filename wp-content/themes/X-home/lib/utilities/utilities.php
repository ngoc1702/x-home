<?php
/**
 * Handles including the utilities files
 * WordPress.
 *
 * @category CAIA
 * @package  Utilities
 * @author   TuanNM
 */

define('CAIA_UTILITIES_VERSION', '1.0.0');

// ----- utitlies ----------------
require('common.php');
// nhóm utilites dành cho admin
if(is_admin()){
	require('caia-user-roles.php');
	require('caia-logs.php');
}

require('caia-logo.php');
require('caia-optimize.php');
require('caia-featured.php');
require('limit-login-attempts.php');
require('wp_mail_smtp.php');
require('comment-reply-notification.php');
require('advance-scripts.php');
require('toc.php');
require('caia-backtotop.php');
require('caia-menumobile.php');
require('caia-shortcode.php');
require('caia-social.php');
require('caia-rating.php');
require('caia-schema.php');
require('yarpp.php');
require('caia-disable-copy.php');
require('caia-wp-seo.php');
require('caia-optimize-tag.php');
require('caia-helper-wp.php');