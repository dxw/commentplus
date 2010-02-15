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

function commentplus_comment_post($comment_ID) {
  $comment = get_comment($comment_ID);
  $stream = $_POST['commentplus_stream'];
  $streams = json_decode(get_post_meta($comment->comment_post_ID, 'commentplus', 1));
  if ($streams && in_array($stream, $streams))
    add_comment_meta($comment_ID, 'commentplus_stream', $stream);
}

add_action('comment_post', 'commentplus_comment_post');

?>
