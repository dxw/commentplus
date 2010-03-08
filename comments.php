<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
  die ('Please do not load this page directly. Thanks!');

if ( post_password_required() ) {
  echo '<p class="nocomments">This post is password protected. Enter the password to view comments.</p>';
  return;
}
?>
<?php
global $commentplus;
$streams = json_decode(get_post_meta($post->ID, '_commentplus',1));

if (comments_open() || have_comments()) {
?>
<div id="comments">
  <div class="navigation">
    <p class="alignleft"><?php previous_comments_link() ?></p>
    <p class="alignright"><?php next_comments_link() ?></p>
  </div>
<?php
  if ($streams) {
    $commentplus->streams = $streams;
    $n = 0;
    foreach($streams as $stream) {
      $n++;
      $commentplus->n = $n - 1;
      $stream = htmlentities($stream);
      $stream_id = preg_replace('/[^A-Za-z0-9_:.-]/','',$stream);
?>
  <div id="commentplus_stream_<?php echo $stream_id ?>" class="commentplus_stream">
  <h4><?php echo $stream ?></h4>
  <div class="commenting">
<?php include "respond.php" ?>
    <ol class="commentlist">
      <?php wp_list_comments('', $commentplus->get_comments()) ?>
    </ol>
  </div>
  </div>
<?php
    }
  }
  // Helpfully, wp_list_comments overwrites $wp_query->max_num_comment_pages
  $commentplus->fiddle_max_num_comment_pages();
?>
  <div class="navigation">
    <p class="alignleft"><?php previous_comments_link() ?></p>
    <p class="alignright"><?php next_comments_link() ?></p>
  </div>
</div>
<?php
} else {
?>
<p class="nocomments">Comments are closed.</p>
<?php
}

?>
