<div id="overlay">
    <div id="spinner_container">
      <input type="button" id="spinner-load">
    </div>
</div>

<?php
 echo "<script>
$('.searchbutton').click(function() {
  if($('.spinner_form').valid()) {
    $('.searchbutton').val(lang_processing);
    $('body').css('pointer-events', 'none');
    $('#overlay').css('display', 'block');
  }
});


  $('.downloadBtn').click(function() { 
    $('.downloadBtn').val(lang_processing);
    $('body').css('pointer-events', 'none');
    $('#overlay').css('display', 'block');
    setTimeout(function() { 
        $('#downloadBtn').val('Download');
        $('body').css('pointer-events', 'auto');
        $('#overlay').css('display', 'none');
    }, 12000);
  });
 </script>";
?>