<?php /*
**************************************************************************

File:         shortcode.php
What it does: creates a shortcode that will read from the api endpoint and will display the movies whenever is called
**************************************************************************/


add_shortcode('moxie-press', 'moxie_press_shortcode');

function moxie_press_shortcode(){
  $endpoint = get_home_url() . '/movies.json';
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
}









?>
