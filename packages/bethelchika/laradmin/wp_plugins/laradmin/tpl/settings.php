<form method="post" action="">
    <fieldset>
        <legend><strong>Where to show blogpost</strong></legend>
        <label>
            <input name="wp_blogpost_on_laravel" type="radio" value="1" <?php if(get_option('wp_blogpost_on_laravel')){echo 'checked';}?>>
            Let Laravel show blogpost
        </label>
        <br>
        <label>
            <input name="wp_blogpost_on_laravel" type="radio" value="0" <?php if(!get_option('wp_blogpost_on_laravel')){echo 'checked';}?>>
            Let Wordpress show blogpost
        </label>
    </fieldset>
  <br><br>
    <button type="submit" class="button button-primary" >Save</button>

</form>