<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments_ajah.php' == basename($_SERVER['SCRIPT_FILENAME']))
  die ('Please do not load this page directly. Thanks!');

if ( post_password_required() ) {
  echo '<p class="nocomments">This post is password protected. Enter the password to view comments.</p>';
  return;
}

global $commentplus, $stream;
?>
<div id="comments">
<div class="commentlist_container">
  <div class="navigation">
    <p class="alignleft"><?php previous_comments_link() ?></p>
    <p class="alignright"><?php next_comments_link() ?></p>
  </div>
  <ol class="commentlist">
    <?php wp_list_comments('', $commentplus->get_comments()) ?>
  </ol>
<?php
// Helpfully, wp_list_comments overwrites $wp_query->max_num_comment_pages
$commentplus->fiddle_max_num_comment_pages();
?>
  <div class="navigation">
    <p class="alignleft"><?php previous_comments_link() ?></p>
    <p class="alignright"><?php next_comments_link() ?></p>
  </div>
</div>
