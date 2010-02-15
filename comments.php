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
$streams = json_decode(get_post_meta($post->ID, 'commentplus',1));

if (comments_open() || have_comments()) {
?>
<div id="comments">
  <div class="navigation">
    <p class="alignleft"></p>
    <p class="alignright"></p>
  </div>
<?php
  if ($streams) {
    $n = 0;
    foreach($streams as $stream) {
      $n++;
      $stream = htmlentities($stream);
?>
  <div id="commentplus_stream_<?php echo $n ?>" class="commentplus_stream">
  <h4><?php echo $stream ?></h4>
  <div class="respond">
<?php include "respond.php" ?>
  </div>
  <ol class="commentlist">
    <?php wp_list_comments('', $commentplus->get_comments($stream)) ?>
  </ol>
  </div>
<?php
    }
  }
?>
  <div class="navigation">
    <p class="alignleft"></p>
    <p class="alignright"></p>
  </div>
</div>
<?php
} else {
?>
<p class="nocomments">Comments are closed.</p>';
<?php
}

?>
