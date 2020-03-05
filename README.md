# WordPress IMDb Score plugin
This WordPress plugin can help you to display IMDb ratings score for a movie on your website. It uses the OMDb API to get needed info.
You're able to add it everywhere you want - in your theme, blog post or page.

## How to display IMDb score for a movie in your WordPress theme or plugin?
It could be accomplished just by adding the following code at the place where you want to display the IMDb score:  
`<?php do_action("display_imdb_score", "[imdb_movie_id]"); ?>`  
**_Example:_**  
`<?php do_action("display_imdb_score", "tt1264904"); ?>`  

## How to display IMDb score for a movie into your publications?
Just use the following shortcode:  
`[imdb_score id="imdb_movie_id"]`  
**_Example:_**  
`[imdb_score id="tt1264904"]`  
  
