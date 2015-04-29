Quotes Collection
=================

Quotes Collection Plugin for [WordPress](https://wordpress.org/) helps you collect, manage and display your favourite quotes in your WordPress website or blog.


Features and Notes
------------------

* **Admin interface**: An admin interface to add, edit, import, export and generally manage the collection of quotes. 
* **Sidebar widget**: The Random Quote sidebar widget that will display a random quote from your collection and a refresh link at the bottom. As many number of instances of the widget can be added. Following is the list of options in the widget control panel:
	* Widget title
	* Option to show/hide quote author
	* Option to show/hide quote source
	* Turn on/off the refresh feature
	* Choose random or sequential order for refresh
	* Option to refresh the quote automatically
	* Show only quotes with certain tags
	* Specify a character limit and filter out bigger quotes
* **Shortcode**: Quotes can be displayed in a WordPress page by placing a `[quotcoll]`shortcode. Few examples are provided below. For more examples and the full list of arguments, please refer the [plugin homepage](http://srinig.com/wordpress/plugins/quotes-collection/).
	* Placing `[quotcoll]` in the page displays all quotes.
	* `[quotcoll author="Somebody"]` displays quotes authored by Somebody.
	* `[quotcoll tags="tag1,tag2,tag3"]` displays quotes tagged tag1 or tag2 or tag3, one or more or all of these
	* `[quotcoll orderby="random" limit=1]` displays a random quote
* **The template function**: To code the random quote functionality directly into a template file, the template function `quotescollection_quote()` can be used. Please refer the plugin homepage for details.
* **Import/Export** your collection of quotes in JSON format *(new in 2.0)*.
* The plugin suppports localization. Refer the plugin page or readme.txt for the full list of available languages and the respective translators. 

For more information, visit the [plugin homepage](http://srinig.com/wordpress/plugins/quotes-collection/).


Installation
------------

*Note:* The stable version of the plugin can be downloaded from the [WordPress plugin directory](https://wordpress.org/plugins/quotes-collection/). The latest development version can be downloaded from GitHub, but it may not be stable. 

### Method 1 ###

1. Go to *Plugins -> Add New* in your WordPress admin area
1. Type 'quotes collection' in the search box available and hit the 'Enter' key
1. Locate the 'Quotes Collection' plugin authored by Srini G, and click 'Install Now'

### Method 2 ###

1. Dowload the latest version of the plugin
1. Go to *Plugins -> Add New* in your WordPress admin area
1. Click on the 'Upload Plugin' button at the top, near 'Add Plugins'
1. Browse and select the zip file you just downloaded, and click 'Install Now'

### Method 3 ###

1. Dowload the latest version of the plugin
1. Extract the zip file
1. Using a FTP client or something similar, upload the `quotes-collection` directory to the `~/wp-content/plugins/` directory of your WordPress installation.

After installation, the plugin can be activated from *Plugins -> Installed Plugins* in your WordPress admin area. Once activated, the *Quotes Collection* menu will be visible in your admin menu.


Links
-----

* [Plugin home page](http://srinig.com/wordpress/plugins/quotes-collection/)
* [Plugin at the WordPress plugin directory](https//wordpress.org/plugins/quotes-collection/)
* [Support at the WordPress support forums](https://wordpress.org/support/plugin/quotes-collection)
* [Development at GitHub](https://github.com/sriniguna/quotes-collection/)

