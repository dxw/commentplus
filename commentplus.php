<?php
/*
Plugin Name: Comment+
Description: Multiple streams of comments
Author: The Dextrous Web
Author URI: http://thedextrousweb.com/
*/

class CommentPlus {
  function __construct() {
    add_filter('comments_template', array(&$this, 'comments_template'));
    add_action('comment_post', array(&$this, 'comment_post'));
  }

  function comments_template($template) {
    return dirname(__FILE__).'/comments.php';
  }

  function comment_post($comment_ID) {
    $comment = get_comment($comment_ID);
    $stream = $_POST['commentplus_stream'];
    $streams = json_decode(get_post_meta($comment->comment_post_ID, 'commentplus', 1));
    if ($streams && in_array($stream, $streams))
      add_comment_meta($comment_ID, 'commentplus_stream', $stream);
  }
}

new CommentPlus;

?>
