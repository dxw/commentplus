<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'admin_interface.php' == basename($_SERVER['SCRIPT_FILENAME']))
  die ('Please do not load this page directly. Thanks!');

class CommentPlusAdminInterface {
  function __construct() {
    add_action('do_meta_boxes', array($this,'do_meta_boxes'));
    add_action('save_post', array($this,'save_post'));
  }

  // Metaboxen

  function do_meta_boxes($object, $postition = 'normal', $post = null) {
    $id = 'commentplus_box';
    $title = 'Comment+';
    $context = 'normal';
    foreach (array('post', 'page') as $page)
      add_meta_box($id, $title, array($this,'add_box'), $page, $context);
  }

  function add_box() {
    global $commentplus, $post;
    $stream_defs = array('');
    foreach((array)$commentplus->stream_defs as $key => $value)
      $stream_defs[] = $key;
    $existing = get_post_meta($post->ID, '_commentplus', 1);
    if(empty($existing))
      $existing = '';

    echo '<h5><label for="commentplus_streamset">Stream set</label></h5>';
    echo '<p>';
    echo '<select id="commentplus_streamset" name="commentplus_streamset">';
    foreach($stream_defs as $key) {
      $selected = $key==$existing? ' selected="selected"' : '';
      echo "<option$selected>".htmlspecialchars($key).'</option>';
    }
    echo '</select>';
    echo '</p>';
  }

  function save_post($post_id) {
    global $commentplus;
    if(!isset($_POST['commentplus_streamset']))
      return;
    $streamset = $_POST['commentplus_streamset'];
    if(empty($streamset))
      delete_post_meta($post_id, '_commentplus');
    elseif(isset($commentplus->stream_defs->{$streamset}))
      if (!add_post_meta($post_id, '_commentplus', $streamset, true))
        update_post_meta($post_id, '_commentplus', $streamset);
  }
}

$commentplusadmininterface = new CommentPlusAdminInterface;

?>
