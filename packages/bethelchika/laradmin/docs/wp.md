# Wordpress
Laradmin uses Wordpress for document publishing and menu graphical creation.

## Installation
Laradmin currently require that Wordpress is installed in /public folder of Laravel your app. You should follow the usual worpress installation process.  
## Configuration
After installation of Wordpress, you should configure a connection for the Wordpress database in your Laravel database config file. For example add the following connection:
```php
....
'corcel' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE_WP', 'wordpress'),
            'username' => env('DB_USERNAME_WP', 'root'),
            'password' => env('DB_PASSWORD_WP', 'root'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => 'wp_',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ],
....
```
The connection name should be 'corcel' as shown above.

in your environmental file tell Laradmin that you are ready with Wordpress by setting the following variables:
LARADMIN_WP_ENABLE=true
LARADMIN_WP_RPATH=/wp
LARADMIN_PAGE_URL_PREFIX=page

The first variable tells Laradmin to begin using Wordpress, the seconds defines the folder relative to the Laravel app's public folder, where Worpress is installed. And the third variable tells Laradmin the prefix to use when viewing pages from Wordpress.

## Plugin
After installing and enabling WP, login to Laradmin control panel and from the sidebar select 'Post installation'. Follow the instruction on the resulting screen to copy the Laradmin WP plugin and templates in to WP.

Now head to WP control panel to enable activate the plugin.

Note what you should reinstall the templates whenever you change to a new theme in WP since the templates a stored in a current theme's folder.  Also reinstall after updating the templates.


## Ready to go
Now go and start creating pages in Wordpress and create a menu in Wordpress named 'primary'. Add wordpress pages to this  menu and the should show up on your Laravel application.

See Laradmin webpage for documentation on how to using templates, template metas and page parts to modify pages.

## Factory data
From the homepage of the Laradmin WP plugin, you can load a factory data which contains examples pages and menus to get you started.

## Menu
By default laradmin attempts to load a menu named 'primary'. TO change this or add more menus to be loaded, see Laradmin config file.

## Pages

### Homepage 
The Laradmin WP plugin provides a custom post type called 'Homepage sections' which are used to create homepage sections. Similar to pages, you can use custom fields to change how these look and feel. You can create as many of these as needed and order their display using the post attribute 'Oder'. The one with the order of 0 is display first and it is the only one that can be a hero. You can set the 'hero_type' custom field to make it a hero.

### Page
Follow the usual WP method of creating pages. If you have followed the post installation to install templates, you should be to choose a template for your page. Available templates includes Base, Fullwidth,Hero, Index, Three col and With sidebar. You can try each templates to see how they make you page look. The no/default template is chosen the Index template will be used to display the page.

Addition to using templates you can use custom fields to change how the page should look. For a list of custom fields and their values see the comments on the 'page' method of the class  \\BethelChika\Laradmin\Http\Controllers\User\WPController. Some custom fields are only meaningful with a specific template. E.g. hero_type custom field only makes sense when set on a page with Hero template.

#### Hero page
This hero template allows you to create a beautiful and simple page which has a hero image.

1) The hero image is a featured image of the corresponding page in WP. This image is not required. You could instead specify a scheme or gradient using custom fields.
2) The first H1 on the page is assumed to be the title of the hero. A tagline for the title can be created by adding a small HTML element tag in the H1 tag thus: 
```html
<h1>This is the hero title    
        <small class="text-white">
                Small bit of the title which could serve as a tag line
        </small>
</h1>
```
3) Any of the following shortcodes can be used to create buttons inside the hero image:

        [hero_url url="http://www.itv.com" text="See the ITV"] 

        [hero_route name=user-profile text="User profile"] 

        [hero_url url="http://www.bbc.co.uk" text="See the BBC" ]

4) Any other content on the page will be placed right after the hero image.

Just like any other page, you can use custom fields to customise a hero page. There a set of custom field that are specific for hero pages. E.g you can use the `hero_content_width` custom field to set the width of the hero content

### Post
Although posts can be listed in laradmin, the display of posts are left for WP to handle as it is suitable to blogs.



## Site parts
The Laradmin WP plugin provides a custom post type called 'Page Parts' which are used to add partial contents to pages. The page parts named 
1) sidebar
2) rightbar
3) footer

> Note: If you  create a site part named *privacy*, it will be used for privacy policy in place of the default Wordpress privacy page. But you must set the config `laradmin.wp_use_privacy` for either the privacy site part and the default Wordpress privacy page would be considered. Note that the default Wordpress privacy page must have a slug of *privacy-policy* else it won't be seen at all.

have special meanings which relates to their names. The custom fields with the same names allow you to define custom page parts for the side bar, right bar and the footer.

You can create as many page parts as you need and include them in any page you want using.
 
        Tip: Use the [page_part] shortcode to include one or more page parts to any part of a page. e.g [page_part name=widgets] 

### Custom fields
Custom fields are extensively used to customize pages. For a list of custom fields and their values see the comments on the `page` method of the class \\BethelChika\Laradmin\Http\Controllers\User\WPController.

### Shortcodes
For all the available shortcodes and their usage please for now, refer to the class \\BethelChika\Laradmin\WP\Shortcodes\Shortcodes.




