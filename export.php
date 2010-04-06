<?php
header('Content-type: text/plain');

include dirname(__FILE__).'/../../../wp-load.php';

if (!current_user_can('create_users'))
  die('You are not authorised to see this. Please log in and try again.');

// Why does fputcsv not work correctly like this does?
// http://www.php.net/manual/en/function.fputcsv.php#87120
function fputcsv2 ($fh, array $fields, $delimiter = ',', $enclosure = '"', $mysql_null = false) {
  $delimiter_esc = preg_quote($delimiter, '/');
  $enclosure_esc = preg_quote($enclosure, '/');

  $output = array();
  foreach ($fields as $field) {
    if ($field === null && $mysql_null) {
      $output[] = 'NULL';
      continue;
    }

    $output[] = preg_match("/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field) ? (
      $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure
    ) : $field;
  }

  fwrite($fh, join($delimiter, $output) . "\n");
}

// Now we can actually write some code

$zipfold = strftime('%Y-%m-%d_comments');

// Zip file
$filename = tempnam('/tmp','zip');
$zip = new ZipArchive();
$zip->open($filename,ZIPARCHIVE::OVERWRITE);

$sets = $commentplus->stream_defs;

foreach ($sets as $set_id => $streams) {
  foreach ($streams as $stream) {

    // compose the heading
    $heading = array('comment_ID','Datetime (UTC)','Publishable?','Name','Email','Web site','Comment');
    if (isset($stream->fields))
      foreach($stream->fields as $field)
        $heading[] = $field->name;

    // print heading
    $f = fopen('php://memory', 'w');
    fputcsv2($f, $heading);

    // go through all comments appropriate to this stream
    // foreach comments
    //   if stream == Streamx && comment.post.stream == Streamx
    //TODO: optimise for time
    $comment_ids = $wpdb->get_col('SELECT comment_id FROM wp_comments');
    foreach ($comment_ids as $comment_id) {
      $comment = get_comment($comment_id);

      if (get_comment_meta($comment_id,'_commentplus_stream',1) == $stream->name && get_post_meta($comment->comment_post_ID,'_commentplus',1) == $set_id) {

        // print out the contents appropriate to the header
        $publishable = get_comment_meta($comment_id,'_commentplus_notforpublication',1) === '1' ? 'No' : 'Yes';
        $line = array($comment_id, $comment->comment_date_gmt, $publishable, $comment->comment_author, $comment->comment_author_email, $comment->comment_author_url, $comment->comment_content);

        if (isset($stream->fields)) {
          $extra = json_decode(get_comment_meta($comment_id,'_commentplus_extra',1));
          foreach($stream->fields as $field)
            if (isset($extra->{$field->name}))
              $line[] = $extra->{$field->name};
            else
              $line[] = 'No response';
        }

        fputcsv2($f, $line);
      }

    }

    fseek($f, 0);
    $csv = '';
    while ($buf = fread($f, 100))
      $csv .= $buf;
    fclose($f);

    $zip->addFromString("$zipfold/$set_id/$stream->name.csv", $csv);
  }
}

$zip->close();
$fp = fopen($filename, 'r');

# Output
header('Pragma: public');
header('Cache-control: max-age=0');
header("Content-Type: application/zip");
header('Content-Disposition: attachment; filename='.$zipfold.'.zip');
echo(stream_get_contents($fp));

unlink($filename);

?>
