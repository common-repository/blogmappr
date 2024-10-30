<?php
/*
Plugin Name: blogmappr
Plugin URI: http://wordpress.org/extend/plugins/blogmappr/
Description: Bindet die Meta-Tags und Geo-Tags, die Blogmappr indexiert in den <head> ein.
Version: 2.1
Author: blogmappr.net
Author URI: http://www.blogmappr.net
*/

function blogmappr_addadminpages(){
  add_submenu_page("options-general.php", "Blogmappr Konfiguration", "Blogmappr", 9, basename(__FILE__), 'blogmappr_admin');
}

function blogmappr_postping(){
  $curl_handle=@curl_init();
  @curl_setopt($curl_handle,CURLOPT_URL,'http://api.blogmappr.net/post.ping/' . get_option('home'));
  @curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
  @curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
  $buffer = @curl_exec($curl_handle);
  @curl_close($curl_handle);
}

function blogmappr_ping(){
  $curl_handle=@curl_init();
  @curl_setopt($curl_handle,CURLOPT_URL,'http://api.blogmappr.net/blog.ping/' . get_option('home'));
  @curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
  @curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
  $buffer = @curl_exec($curl_handle);
  @curl_close($curl_handle);
  return $buffer;
}

function blogmappr_admin(){
$kategorien = array("Computerblog", "Corporateblog", "Fotoblog", "Funblog", "Gourmetblog", "Hobbyblog", "Internetblog", "Kulturblog", "Kunstblog", "Medizinblog" , "Musikblog", "Politikblog", "Privatblog", "Seoblog", "Sonstigerblog", "Sportblog", "Tierblog", "Watchblog", "Wirtschaftsblog", "Wissenschaftsblog");

if ($_GET['updated'] == true) { 
  $buffer = blogmappr_ping();
  if (empty($buffer)){ ?><div class="error"><p><b>Klicken Sie nun <a href="http://www.blogmappr.net/Ping?url=<?php bloginfo('url'); ?>" target="_blank">hier</a></b>, um Blogmappr anzupingen, damit die Änderungen wirksam werden.</p></div>
    <?php }
  else { ?><div class="updated"><p>Blogmappr Statuscode: <?php echo $buffer; ?></p></div>
    <?php }
  } ?>
<div class="wrap">
  <h2>Blogmappr Konfiguration</h2>
  <form method="post" action="options.php">
    <?php wp_nonce_field('update-options'); ?>
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="blogmappr_kategorie,blogmappr_lat,blogmappr_lng,blogmappr_ort,blogmappr_sland,blogmappr_sbland,blogmappr_track" />
    <table class="form-table">
			<tr>
				<th scope="row" valign="top">Blog-Informationen</th>
				<td colspan="2">
					<div style="font-size: 15px;"><?php bloginfo('name'); ?></div>
					<p><b><?php bloginfo('description'); ?></b></p>

					<label for="blogmappr_kategorie">Kategorie des Blogs:</label><br/>	
          <select name="blogmappr_kategorie" id="blogmappr_kategorie">
            <?php foreach($kategorien as $kategorie) : ?>
						  <option<?php if($kategorie == get_option('blogmappr_kategorie')){ echo ' selected="selected"'; } ?>><?php echo $kategorie; ?></option>
						<?php endforeach; ?>
					</select><br/>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top">Geo-Informationen</th>
				<td colspan="2">
					<label for="blogmappr_lat">Fügen sie hier die Koordinaten des Blogs ein, welche Sie <a href="http://www.blogmappr.net/Eintragen/Koordinaten" target="_blank">hier bestimmen können</a>:</label><br/>
					Latitude: <input size="20" type="text" id="blogmappr_lat" name="blogmappr_lat" value="<?php echo get_option('blogmappr_lat'); ?>" /> Longitude: <input size="20" type="text" id="blogmappr_lng" name="blogmappr_lng" value="<?php echo get_option('blogmappr_lng'); ?>" /><br/>

					<label for="blogmappr_ort">Standort des Blogs (Bsp: Berlin):</label><br/>
					<input size="50" type="text" id="blogmappr_ort" name="blogmappr_ort" value="<?php echo get_option('blogmappr_ort'); ?>" /><br/>
					
					<label for="blogmappr_sland"><a href="http://de.wikipedia.org/wiki/ISO_3166-2:DE" target="_blank">Zweistellige Kürzel von Land und Bundesland</a> (Bsp: "DE-NW" für Deutschland - Nordrhein-Westfalen):</label><br/>	
					<input size="2" maxlength="2" type="text" id="blogmappr_sland" name="blogmappr_sland" value="<?php echo get_option('blogmappr_sland'); ?>" />-<input size="2" maxlength="2" type="text" id="blogmappr_sbland" name="blogmappr_sbland" value="<?php echo get_option('blogmappr_sbland'); ?>" /><br/>
				</td>
			</tr>
    </table>
    <p class="submit"><input type="submit" name="submit" value="<?php _e('Save Changes') ?>" /></p>
  </form>
</div>
<?php
}
function blogmappr_meta(){
  $kategorie = get_option('blogmappr_kategorie');
  if(empty($kategorie)) {
    $kategorie = "Sonstigerblog";
  }
?>
  <!-- Blogmappr Start -->
  <meta name="blogmappr.name" content="<?php bloginfo('name'); ?>" />
  <meta name="blogmappr.beschreibung" content="<?php bloginfo('description') ?>" />
  <meta name="blogmappr.kategorie" content="<?php echo $kategorie; ?>" />
  <meta name="blogmappr.feedurl" content="<?php echo bloginfo('rss2_url') ?>" />
    <!-- Geo-Tags Start -->
    <meta name="geo.region" content="<?php echo get_option('blogmappr_sland') ?>-<?php echo get_option('blogmappr_sbland') ?>" />
    <meta name="geo.placename" content="<?php echo get_option('blogmappr_ort') ?>" />
    <meta name="geo.position" content="<?php echo get_option('blogmappr_lat') ?>;<?php echo get_option('blogmappr_lng') ?>" />
    <meta name="ICBM" content="<?php echo get_option('blogmappr_lat') ?>;<?php echo get_option('blogmappr_lng') ?>" />
    <!-- Geo-Tags Ende -->
  <!-- Blogmappr Ende -->
<?php
}
add_action('publish_post', 'blogmappr_postping');
add_action('admin_menu', 'blogmappr_addadminpages');
add_action('wp_head', 'blogmappr_meta');
?>