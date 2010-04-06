<?php
/*
Plugin Name: Comment+
Description: Multiple streams of comments
Author: The Dextrous Web
Author URI: http://thedextrousweb.com/
*/
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'commentplus.php' == basename($_SERVER['SCRIPT_FILENAME']))
  die ('Please do not load this page directly. Thanks!');

function h($t){echo htmlspecialchars($t);}

class CommentPlus {
  function __construct() {
    $this->_POST = stripslashes_deep($_POST); // FUCKING DIE
    $this->stream_defs = json_decode(get_option('commentplus', '{}'));

    add_filter('comments_template', array(&$this, 'comments_template'));
    add_filter('comment_text', array(&$this, 'comment_text'));
    add_filter('get_comment_author', array(&$this, 'get_comment_author'));
    add_filter('get_comment_author_url', array(&$this, 'get_comment_author_url'));
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

  function comment_text($comment_text) {
    global $comment;
    if(empty($comment))
      return $comment_text;

    $nfp_text = 'This reply is marked not for publication.';

    if($this->comment_should_be_hidden())
      return $nfp_text;

    $streamset = $this->get_streamset(get_post_meta($comment->comment_post_ID, '_commentplus',1));
    $our_stream = get_comment_meta($comment->comment_ID, '_commentplus_stream', 1);
    $commentmeta = json_decode(get_comment_meta($comment->comment_ID, '_commentplus_extra', 1));

    $extra_content = '';
    foreach($streamset as $stream) {
      if($stream->name == $our_stream) {

        $extra_content .= '<dl class="commentplus_extra">';
        if(isset($stream->fields)) {
          foreach($stream->fields as $field) {
            if(isset($commentmeta->{$field->name})) {
              $value = $commentmeta->{$field->name};
              $extra_content .= '<dt>'.htmlspecialchars($field->name).'</dt>';
              $extra_content .= '<dd>'.htmlspecialchars($value).'</dd>';
            }
          }
        }
        $extra_content .= '</dl>';
        break;
      }
    }

    if($this->comment_nfp_but_visible())
      $extra_content = $nfp_text . "\n\n" . $extra_content;

    return $extra_content . $comment_text;
  }

  function get_comment_author($author) {
    if($this->comment_should_be_hidden())
      return 'Not for publication';
    else
      return $author;
  }

  function get_comment_author_url($url) {
    if($this->comment_should_be_hidden())
      return '';
    else
      return $url;
  }

  // Actions

  function wp_head() {
?>
<link rel="commentplus_ajah" href="<?php h(WP_PLUGIN_URL) ?>/commentplus/get_comments.php">
<meta name="cpage" content="<?php h(intval(get_query_var('cpage'))) ?>">
<?php
  }

  function comment_post($comment_ID) {
    $comment = get_comment($comment_ID);
    $stream = $this->_POST['commentplus_stream'];
    $streams = $this->get_streamset(get_post_meta($comment->comment_post_ID, '_commentplus', 1));
    foreach ($streams as $n => $str)
      if ($str->name == $stream) {
        add_comment_meta($comment_ID, '_commentplus_stream', $str->name, 1);

        if (isset($this->_POST['cp'.$n.'_notforpublication']))
          add_comment_meta($comment_ID, '_commentplus_notforpublication', 1, 1);

        // Extra questions
        $extra = (object)array();
        if(isset($str->fields)){
          foreach($str->fields as $field) {

            $field_id = 'cp'.$n.'_'.$this->sanitise($field->name);

            switch($field->type) {
            case 'yesno':
              $value = 'No response';
              if(isset($this->_POST[$field_id]))
                switch($this->_POST[$field_id]) {
                case 'yes':
                  $value = 'Yes';
                  break;
                case 'no':
                  $value = 'No';
                  break;
                default:
                  $value = 'No response';
                }
                $extra->{$field->name} = $value;
              break;
            case 'select':
              $value = 'No response';
              if(isset($this->_POST[$field_id])) {
                $slug = $this->_POST[$field_id];
                foreach($field->options as $option) {
                  if($option->slug == $slug) {
                    $value = $option->title;
                    break;
                  }
                }
              }
              $extra->{$field->name} = $value;
              break;
            }

          }
        }
        add_comment_meta($comment_ID, '_commentplus_extra', json_encode($extra), 1);
        break;
      }
  }

  // Everything else

  function comment_nfp_but_visible() {
    global $comment;
    return ((is_admin() || current_user_can('moderate_comments')) && get_comment_meta($comment->comment_ID, '_commentplus_notforpublication', 1));
  }

  function comment_should_be_hidden() {
    global $comment;
    return (!is_admin() && !current_user_can('moderate_comments') && get_comment_meta($comment->comment_ID, '_commentplus_notforpublication', 1));
  }

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

  function sanitise($t) {
    return preg_replace('/[^A-Za-z0-9_:.-]/','_',$t);
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
      $this->stream = $this->streams[$this->n]->name;
      if(isset($this->streams[$this->n]->fields))
        $this->stream_questions = $this->streams[$this->n]->fields;
      else
        $this->stream_questions = null;
      $this->stream_id = $this->sanitise($this->streams[$this->n]->name);
      return true;
    }
  }

  function get_comments() {
    global $wp_query, $comment;
    $comments = array();
    foreach ($wp_query->comments as $comment)
      if (get_comment_meta($comment->comment_ID, '_commentplus_stream', 1) == $this->streams[$this->n]->name)
        if (!$this->comment_should_be_hidden())
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
?>
    <p class="notforpublication"><label for="cp<?php h($this->n) ?>_notforpublication"><input type="checkbox" name="cp<?php h($this->n) ?>_notforpublication" id="cp<?php h($this->n) ?>_notforpublication" tabindex="<?php h($this->n) ?>3" /> Not for publication</label></p>
<?php

    if($this->stream_questions == null)
      return;
    foreach($this->stream_questions as $field) {
      $title = htmlentities($field->name);
      $id = 'cp'.$this->n.'_'.$this->sanitise($field->name);

      if($field->type != 'yesno')
        $title = '<label for="'.htmlentities($id).'">'.$title.'</label>';
      echo '<h5>'.$title.'</h5>';
      switch($field->type) {
      case 'yesno':
?>
  <ul class="yesno">
    <li><label for="<?php h($id) ?>_yes"><input type="radio" name="<?php h($id) ?>" value="yes" id="<?php h($id) ?>_yes" tabindex="<?php h($this->n) ?>3" /> Yes</label></li>
    <li><label for="<?php h($id) ?>_no"><input type="radio" name="<?php h($id) ?>" value="no" id="<?php h($id) ?>_no" tabindex="<?php h($this->n) ?>3" /> No</label></li>
    <li><label for="<?php h($id) ?>_nc"><input type="radio" name="<?php h($id) ?>" value="nc" checked="checked" id="<?php h($id) ?>_nc" tabindex="<?php h($this->n) ?>3" /> No response</label></li>
  </ul>
<?php
        break;
      case 'select':
?>
  <p class="select">
    <select name="<?php h($id) ?>" id="<?php h($id) ?>" tabindex="<?php h($this->n) ?>3">
      <option></option>
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
