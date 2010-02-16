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

  // Filters

  function comments_template($template) {
    return dirname(__FILE__).'/comments.php';
  }

  function query($query) {
    global $wpdb;
    $query = preg_replace('/WHERE/', "JOIN $wpdb->commentmeta ON $wpdb->comments.comment_ID=$wpdb->commentmeta.comment_id \\0 $wpdb->commentmeta.meta_key='commentplus_stream' AND $wpdb->commentmeta.meta_value=%s AND ", $query);
    $query = $wpdb->prepare($query, $this->stream);
    return $query;
  }

  // Actions

  function comment_post($comment_ID) {
    $comment = get_comment($comment_ID);
    $stream = $_POST['commentplus_stream'];
    $streams = json_decode(get_post_meta($comment->comment_post_ID, 'commentplus', 1));
    if ($streams && in_array($stream, $streams))
      add_comment_meta($comment_ID, 'commentplus_stream', $stream);
  }

  // Everything else

  function get_comments($stream) {
    global $wp_query;
    $comments = array();
    foreach ($wp_query->comments as $comment)
      if (get_comment_meta($comment->comment_ID, 'commentplus_stream', 1) == $stream)
        $comments[] = $comment;
    return $comments;
  }
}

$commentplus = new CommentPlus;

?>
