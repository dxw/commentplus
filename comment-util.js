jQuery(document).ready(function($){
  // Make streams collapse
    if (false)
  $('.commentplus_stream').each(function(){
    var stream = $(this);
    stream.find('.commenting').hide();
    stream.find('h4').click(function(){
      $(this).parent('.commentplus_stream').find('.commenting').slideToggle('slow');
    });
  });

  // Remove .navigation and add our AJAH
  $('.navigation').remove();
  $('.commentlist').remove();
  $('.commenting').append('<div class="commentplus_ajah"></div>');

  var get_comments_url = $('link[rel="commentplus_ajah"]').attr('href');
  var post = $('input[name="comment_post_ID"]').attr('value');
  var cpage = $('meta[name="cpage"]').attr('content');
  $('.commenting').each(function(n){
    $(this).load(get_comments_url, 'post='+post+'&stream='+(n+1)+'&cpage='+cpage);
  });
});
