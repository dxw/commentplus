<?php
// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
  die ('Please do not load this page directly. Thanks!');
if ( post_password_required() )
  return print('<p class="nocomments">This post is password protected. Enter the password to view comments.</p>');

// You can start editing here

global $commentplus;
if (comments_open() || have_comments()): ?>

<div id="comments">
  <div class="navigation">
    <p class="alignleft"><?php previous_comments_link() ?></p>
    <p class="alignright"><?php next_comments_link() ?></p>
  </div>

<?php if($commentplus->has_streams()): while($commentplus->next_stream()): ?>

  <div id="commentplus_stream_<?php h($commentplus->stream_id) ?>" class="commentplus_stream">
  <h4><?php h($commentplus->stream) ?></h4>
  <div class="commenting">

<?php $commentplus->respond() ?>

    <ol class="commentlist">
      <?php $commentplus->wp_list_comments() ?>
    </ol>
  </div>
  </div>

<?php endwhile; endif ?>

  <div class="navigation">
    <p class="alignleft"><?php previous_comments_link() ?></p>
    <p class="alignright"><?php next_comments_link() ?></p>
  </div>
</div>

<?php else: ?>
<p class="nocomments">Comments are closed.</p>
<?php endif ?>
