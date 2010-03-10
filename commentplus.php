<?php
/*
Plugin Name: Comment+
Description: Multiple streams of comments
Author: The Dextrous Web
Author URI: http://thedextrousweb.com/
*/
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'commentplus.php' == basename($_SERVER['SCRIPT_FILENAME']))
  die ('Please do not load this page directly. Thanks!');

function h($t){echo htmlentities($t);}

class CommentPlus {
  function __construct() {
    $this->stream_defs = json_decode(get_option('commentplus', '{}'));

    add_filter('comments_template', array(&$this, 'comments_template'));
    add_action('comment_post', array(&$this, 'comment_post'));
    add_action('comments_array', array(&$this, 'comments_array'));
    add_action('comment_post', array(&$this, 'comment_post'));
    wp_deregister_script('comment-reply');
    wp_register_script('comment-reply', WP_PLUGIN_URL.'/commentplus/comment-reply.js', array('comment-util'));
    wp_register_script('comment-util', WP_PLUGIN_URL.'/commentplus/comment-util.js', array('jquery'));
    add_action('wp_head', array(&$this, 'wp_head'));
  }

  // Filters

  function comments_template($template) {
    if (basename($template) == 'commentplus_ajah') {
      $file = get_template_directory().'/commentplus_ajah.php';
      if(!file_exists($file))
        $file = dirname(__FILE__).'/comments_ajah.php';
    } else {
      $file = get_template_directory().'/commentplus_comments.php';
      if(!file_exists($file))
        $file = dirname(__FILE__).'/comments.php';
    }

    return $file;
  }

  function comments_array($comments) {
    // The only appropriate place for fiddling with $wp_query->max_num_comment_pages
    $this->fiddle_max_num_comment_pages($comments);
    return $comments;
  }

  function next_comments_link_attributes() {
    return 'class="next_comments_link"';
  }

  function previous_comments_link_attributes() {
    return 'class="previous_comments_link"';
  }

  function wp_head() {
?>
<link rel="commentplus_ajah" href="<?php h(WP_PLUGIN_URL) ?>/commentplus/get_comments.php">
<meta name="cpage" content="<?php h(intval(get_query_var('cpage'))) ?>">
<?php
  }

  // Actions

  function comment_post($comment_ID) {
    $comment = get_comment($comment_ID);
    $stream = $_POST['commentplus_stream'];
    $streams = $this->get_streamset(get_post_meta($comment->comment_post_ID, '_commentplus', 1));
    foreach ($streams as $str)
      if ($str->name == $stream) {
        add_comment_meta($comment_ID, '_commentplus_stream', $str->name, 1);
        break;
      }
  }

  // Everything else

  function fiddle_max_num_comment_pages($comments = null) {
    global $post, $wp_query;

    if ($comments === null)
      $comments = $wp_query->comments;

    // Split comments per stream
    $streams = $this->get_streamset(get_post_meta($post->ID, '_commentplus', 1));
    $streamed_comments = array();
    foreach ($streams as $str)
      $streamed_comments[$str->name] = array();

    foreach ($comments as $comment) {
      $stream = get_comment_meta($comment->comment_ID, '_commentplus_stream', 1);
      foreach ($streams as $str)
        if ($str->name == $stream) {
          $streamed_comments[$str->name][] = $comment;
          break;
        }
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

  function get_streamset($name)
  {
    if(isset($this->stream_defs->{$name}))
      return $this->stream_defs->{$name};
    return (object)array();
  }

  function init_ajah() {
    add_filter('next_comments_link_attributes', array(&$this,'next_comments_link_attributes'));
    add_filter('previous_comments_link_attributes', array(&$this,'previous_comments_link_attributes'));
  }

  // Helper functions

  function has_streams($n = null) {
    global $post;
    $this->streams = $this->get_streamset(get_post_meta($post->ID, '_commentplus',1));
    if($n === null)
      $this->n = -1;
    else
      $this->n = $n;
    return !empty($this->streams);
  }

  function next_stream() {
    $this->n++;
    if(isset($this->streams->{$this->n})) {
      $this->stream = htmlentities($this->streams[$this->n]->name);
      if(isset($this->streams[$this->n]->fields))
        $this->stream_questions = $this->streams[$this->n]->fields;
      else
        $this->stream_questions = null;
      $this->stream_id = preg_replace('/[^A-Za-z0-9_:.-]/','',$this->streams[$this->n]->name);
      return true;
    }
  }

  function get_comments() {
    global $wp_query;
    $comments = array();
    /* YOU ARE HERE */
    foreach ($wp_query->comments as $comment)
      if (get_comment_meta($comment->comment_ID, '_commentplus_stream', 1) == $this->streams[$this->n]->name)
        $comments[] = $comment;
    return $comments;
  }

  function wp_list_comments() {
    wp_list_comments('', $this->get_comments());

    // Helpfully, wp_list_comments overwrites $wp_query->max_num_comment_pages
    $this->fiddle_max_num_comment_pages();
  }

  function respond() {
    global $commentplus, $user_identity, $comment_author, $req, $comment_author_email, $comment_author_url, $post;
    $file = get_template_directory().'/commentplus_respond.php';
    if(!file_exists($file))
      $file = 'respond.php';
    include $file;
  }

  function render_questions() {
    if($this->stream_questions == null)
      return;
    foreach($this->stream_questions as $field) {
      $title = htmlentities($field->name);
      $id = 'cp'.$this->n.'_'.preg_replace('/[^A-Za-z0-9_-]/', '_', $field->name);

      if($field->type != 'yesno')
        $title = '<label for="'.htmlentities($id).'">'.$title.'</label>';
      echo '<h5>'.$title.'</h5>';
      switch($field->type) {
      case 'yesno':
?>
  <ul class="yesno">
    <li><label for="<?php h($id) ?>_yes"><input type="radio" name="<?php h($id) ?>" id="<?php h($id) ?>_yes" /> Yes</label></li>
    <li><label for="<?php h($id) ?>_no"><input type="radio" name="<?php h($id) ?>" id="<?php h($id) ?>_no" /> No</label></li>
    <li><label for="<?php h($id) ?>_nc"><input type="radio" name="<?php h($id) ?>" id="<?php h($id) ?>_nc" /> No response</label></li>
  </ul>
<?php
        break;
      case 'select':
?>
  <p class="select">
    <select name="<?php h($id) ?>" id="<?php h($id) ?>">
      <?php foreach($field->options as $option): ?>
        <option value="<?php h($option->slug) ?>"><?php h($option->title) ?></option>
      <?php endforeach ?>
    </select>
  </p>
<?php
      }
    }
  }
}

$commentplus = new CommentPlus;

include "admin_interface.php";

?>
