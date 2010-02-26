jQuery(document).ready(function($){
  $('.commentplus_stream').each(function(){
    var stream = $(this);
    stream.find('.commenting').hide();
    stream.find('h4').click(function(){
      $(this).parent('.commentplus_stream').find('.commenting').slideToggle('slow');
    });
  });
});
