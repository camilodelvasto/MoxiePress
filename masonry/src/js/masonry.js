/**************************************************************************

File:         masonry/src/masonry.js
What it does: consumes the service and renders movies on the page
**************************************************************************/

$(document).ready(function(){
  console.log(moxie_press_vars.endpoint);
  function getPosts(query){
    var url = 'http://crossorigin.me/http://lalo.startics.com/?json=' + query;
    $.ajax({
      url: url,
      cache: true,
      dataType: 'json',
      success: function(data){
        $.each(data.posts,function(index,item){
          if(item.type == 'foto') loadPic(item,buildPicCard);
          if(item.type == 'video') loadVideo(item,buildVideoCard);
        })
        
      },
      error: function(data){
        console.log('error:',data);
      }
    })
  }

/*
  echo '<h2>Movies</h2><div class="moxie_press_container"></div>';
  ?>
  <script>
    var r = new XMLHttpRequest();
    r.open("POST", "<?php echo $endpoint; ?>", true);
    r.onreadystatechange = function () {
      if (r.readyState != 4 || r.status != 200) return;
      var container = document.querySelector(".moxie_press_container");
      var html = '';
      var myObj = JSON.parse(r.responseText);
      if(myObj.data.length == 0) { html = "no movies found" }
      myObj.data.map(function(item){
        html += '<div class="moxie_press_movie">' + item.title + '</div>';
      });
      container.innerHTML = html;
    };
    r.send();
  </script>
  <?php
  */
});

