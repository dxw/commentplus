<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
  die ('Please do not load this page directly. Thanks!');

if ( post_password_required() ) {
  echo '<p class="nocomments">This post is password protected. Enter the password to view comments.</p>';
  return;
}
?>
<?php
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
    foreach($streams as $stream) {
?>
  <div class="commentplus_stream">
  <h4><?php echo htmlentities($stream) ?></h4>
  <div class="respond">
    <p>Respond</p>
  </div>
  <ol class="commentlist">
    <li>Comment list</li>
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
