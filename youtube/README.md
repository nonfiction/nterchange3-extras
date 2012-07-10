# YouTube asset

Create a text or image link to a popup page view of a youtube video

### Installing

- Copy files and run db/youtube.sql on your database.
- Add the asset to nterchange and enable the default view in some container
- Create a page (blank/shadowbox/etc) with a code caller to display the actual video
    {call controller=youtube action=embed_video}
- Change $embed_page in youtube_controller to the page_id of the page with the code caller

### Notes:

- The controller modifies some of the embed_html that YouTube gives you, so you can display
  all videos at a consistent size. See the controller for some options.
