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
  if ($streams) {
    foreach($streams as $stream) {
      echo "<p>$stream</p>\n";
    }
  }
} else {
  echo '<p class="nocomments">Comments are closed.</p>';
}

?>
