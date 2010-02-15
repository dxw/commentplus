<?php
/*
Plugin Name: Comment+
Description: Multiple streams of comments
Author: The Dextrous Web
Author URI: http://thedextrousweb.com/
*/

function commentplus_comments_template($template) {
  return dirname(__FILE__).'/comments.php';
}

add_filter('comments_template', 'commentplus_comments_template');
?>
