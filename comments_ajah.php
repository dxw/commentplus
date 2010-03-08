<?php
// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments_ajah.php' == basename($_SERVER['SCRIPT_FILENAME']))
  die ('Please do not load this page directly. Thanks!');
if ( post_password_required() )
  return print('<p class="nocomments">This post is password protected. Enter the password to view comments.</p>');

// You can start editing here

global $commentplus; ?>

<div class="commentlist_container">
  <div class="navigation">
    <p class="alignleft"><?php previous_comments_link() ?></p>
    <p class="alignright"><?php next_comments_link() ?></p>
  </div>

  <ol class="commentlist">
    <?php $commentplus->wp_list_comments() ?>
  </ol>

  <div class="navigation">
    <p class="alignleft"><?php previous_comments_link() ?></p>
    <p class="alignright"><?php next_comments_link() ?></p>
  </div>
</div>
