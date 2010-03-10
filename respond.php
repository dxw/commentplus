<?php
// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'respond.php' == basename($_SERVER['SCRIPT_FILENAME']))
  die ('Please do not load this page directly. Thanks!');

// You can start editing here

$n = $commentplus->n;
?>
<?php if ( comments_open() ) : ?>

<div class="respond">

<div class="cancel-comment-reply">
	<small><?php cancel_comment_reply_link(); ?></small>
</div>

<?php if ( get_option('comment_registration') && !is_user_logged_in() ) : ?>
<p>You must be <a href="<?php echo wp_login_url( get_permalink() ); ?>">logged in</a> to post a comment.</p>
<?php else : ?>

<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" class="commentform" id="commentform_<?php echo $n ?>">

<?php if ( is_user_logged_in() ) : ?>

<p>Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account">Log out &raquo;</a></p>

<?php else : ?>

<p><input type="text" class="author" name="author" id="author_<?php echo $n ?>" value="<?php echo esc_attr($comment_author); ?>" size="22" tabindex="<?php echo $n ?>1" <?php if ($req) echo "aria-required='true'"; ?> />
<label for="author_<?php echo$n ?>"><small>Name <?php if ($req) echo "(required)"; ?></small></label></p>

<p><input type="text" class="email" name="email" id="email_<?php echo $n ?>" value="<?php echo esc_attr($comment_author_email); ?>" size="22" tabindex="<?php echo $n ?>2" <?php if ($req) echo "aria-required='true'"; ?> />
<label for="email_<?php echo$n ?>"><small>Mail (will not be published) <?php if ($req) echo "(required)"; ?></small></label></p>

<p><input type="text" class="url" name="url" id="url_<?php echo $n ?>" value="<?php echo esc_attr($comment_author_url); ?>" size="22" tabindex="<?php echo $n ?>3" />
<label for="url_<?php echo$n ?>"><small>Website</small></label></p>

<?php endif; ?>

<!--<p><small><strong>XHTML:</strong> You can use these tags: <code><?php echo allowed_tags(); ?></code></small></p>-->

<?php $commentplus->render_questions() ?>

<p><textarea name="comment" class="comment" id="comment_<?php echo $n ?>" cols="58" rows="10" tabindex="<?php echo $n ?>4"></textarea></p>

<p><input name="submit" class="submit" type="submit" id="submit_<?php echo $n ?>" tabindex="<?php echo $n ?>5" value="Submit Comment" />
<?php comment_id_fields(); ?>
</p>
<?php do_action('comment_form', $post->ID); ?>
<input type="hidden" name="commentplus_stream" value="<?php echo $commentplus->stream ?>" id="commentplus_stream_<?php echo $n ?>" />

</form>

<?php endif; // If registration required and not logged in ?>
</div>

<?php endif; // if you delete this the sky will fall on your head ?>
