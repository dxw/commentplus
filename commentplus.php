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
    add_action('comments_array', array(&$this, 'comments_array'));
  }

  // Filters

  function comments_template($template) {
    return dirname(__FILE__).'/comments.php';
  }

  function comments_array($comments) {
    // The only appropriate place for fiddling with $wp_query->max_num_comment_pages
    $this->fiddle_max_num_comment_pages($comments);
    return $comments;
  }

  // Actions

  function comment_post($comment_ID) {
    $comment = get_comment($comment_ID);
    $stream = $_POST['commentplus_stream'];
    $streams = json_decode(get_post_meta($comment->comment_post_ID, '_commentplus', 1));
    if ($streams && in_array($stream, $streams))
      add_comment_meta($comment_ID, '_commentplus_stream', $stream);
  }

  // Everything else

  function get_comments($stream) {
    global $wp_query;
    $comments = array();
    foreach ($wp_query->comments as $comment)
      if (get_comment_meta($comment->comment_ID, '_commentplus_stream', 1) == $stream)
        $comments[] = $comment;
    return $comments;
  }

  function fiddle_max_num_comment_pages($comments = null) {
    global $post, $wp_query;

    if ($comments === null)
      $comments = $wp_query->comments;

    // Split comments per stream
    $streams = json_decode(get_post_meta($post->ID, '_commentplus', 1));
    $streamed_comments = array();
    foreach ($streams as $stream)
      $streamed_comments[$stream] = array();

    foreach ($comments as $comment) {
      $stream = get_comment_meta($comment->comment_ID, '_commentplus_stream', 1);
      if (in_array($stream, $streams))
        $streamed_comments[$stream][] = $comment;
    }

    // Calculate the size with or without threading
    $page_counts = array();
    foreach ($streamed_comments as $stream => $scomments) {
      if (!empty($scomments))
        $page_counts[$stream] = get_comment_pages_count($scomments);
    }

    // Use the largest size as the numerator
    $wp_query->max_num_comment_pages = empty($page_counts) ? 1 : max($page_counts);
  }
}

$commentplus = new CommentPlus;

?>
