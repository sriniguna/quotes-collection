=== Quotes Collection ===
Contributors: SriniG
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=HDWT2K8TXXHUN
Tags: quotes collection, quotes, quotations, random quote, sidebar, widget, ajax, shortcode
Requires at least: 3.1
Tested up to: 4.9.5
Stable tag: trunk
License: GNU General Public License

Quotes Collection plugin with Ajax powered Random Quote sidebar widget helps you collect and display your favourite quotes in your WordPress website.

== Description ==

Quotes Collection plugin helps you collect, manage and display your favourite quotations in your WordPress website or blog.

**New in 2.0**

* New and improved admin interface
* Import/Export your collection of quotes in JSON format
* Multi-widget support
* Cleaner uninstall
* Many other improvements

*Note: If you are upgrading to 2.0 from an older version, you will have to re-add the widget and set the widget options once again after upgradation.*


**Features and notes**

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
* **Shortcode**: Quotes can be displayed in a WordPress page by placing a `[quotcoll]`shortcode. Few examples are provided below. For more examples and the full list of arguments, please refer 'other notes'.
	* Placing `[quotcoll]` in the page displays all quotes.
	* `[quotcoll author="Somebody"]` displays quotes authored by Somebody.
	* `[quotcoll tags="tag1,tag2,tag3"]` displays quotes tagged tag1 or tag2 or tag3, one or more or all of these
	* `[quotcoll orderby="random" limit=1]` displays a random quote
* **The template function**: To code the random quote functionality directly into a template file, the template function `quotescollection_quote()` can be used. Please refer the plugin homepage or 'other notes' for details.
* **Import/Export** your collection of quotes in JSON format *(new in 2.0)*.
* The plugin suppports localization. Refer the plugin page or 'other notes' for the full list of available languages and the respective translators. 


== Installation ==

**Method 1**

1. Go to *Plugins -> Add New* in your WordPress admin area
1. Type 'quotes collection' in the search box available and hit the 'Enter' key
1. Locate the 'Quotes Collection' plugin authored by Srini G, and click 'Install Now'

**Method 2**

1. Dowload the latest version of the plugin from WordPress plugin directory
1. Go to *Plugins -> Add New* in your WordPress admin area
1. Click on the 'Upload Plugin' button at the top, near 'Add Plugins'
1. Browse and select the zip file you just downloaded, and click 'Install Now'

**Method 3**

1. Dowload the latest version of the plugin from WordPress plugin directory
1. Extract the zip file
1. Using a FTP client or something similar, upload the `quotes-collection` directory to the `~/wp-content/plugins/` directory of your WordPress installation.

After installation, the plugin can be activated from *Plugins -> Installed Plugins* in your WordPress admin area. Once activated, the *Quotes Collection* menu will be visible in your admin menu.


== Frequently Asked Questions ==


= How to hide the 'Next quote »' link? = 

You can do this by turning off the 'Ajax Refresh' feature in widget options.

= How to change the link text from 'Next quote »' to something else? =

This text can be changed from *Quotes Collection -> Options* in your WordPress admin area

= The 'Next quote »' link is not working. Why? =

Make sure your theme's header.php file has the code `<?php wp_head(); ?>` just before `</head>`. If you still experience the problem, [contact](http://srinig.com/wordpress/contact/) the plugin author.

= I have added a number of quotes, but some of the quotes never get displayed in the widget. Why? =

If you want all of the quotes to display, make sure all all the quotes fall within the 'Character limit'. There is an option named 'Character limit' for the widget (bottom most, under the 'advanced options') with a default value of '500'. The value can be changed, or simply removed and the field left blank so that none of the quotes get filtered out based on length.

= I have a long list of quotes, and the `[quotcoll]` shortcode puts all of the quotes in a single page. Is there a way to introduce pagination and break the long list of quotes into different pages? =

Yes, pagination is supporterd in versions 1.5 and greater. `paging` and `limit_per_page` attributes can be used to achieve this. For example, `[quotcoll paging=true limit_per_page=30]` will introduce pagination with a maximum of 30 quotes per page.


== Screenshots ==

1. Admin interface (in WordPress 4.2)
2. 'Random Quote' widget options (WordPress 4.2)
3. A random quote in the sidebar


== The [quotcoll] shortcode ==
Quotes can be displayed in a page by placing the shortcode `[quotcoll]`. This will display all the public quotes ordered by the quote id.

Different attributes can be specified to customize the way the quotes are displayed. Here's the list of attributes:

* **id** *(integer)*
	* For example, `[quotcoll id=3]` displays a single quote, the id of which is 3. If there is no quote with the id 3, nothing is displayed.
	* This overrides all other attributes. That is, if id attribute is specified, any other attribute specified is ignored.
	
* **author** *(string)*
	* `[quotcoll author="Somebody"]` displays all quotes authored by 'Somebody'.

* **source** *(string)*	
	* `[quotcoll source="Something"]` displays all quotes from the source 'Something'.

* **tags** *(string, comma separated)*	
	* `[quotcoll tags="tag1"]` displays all quotes tagged 'tag1'.
	* `[quotcoll tags="tag1, tag2, tag3"]` displays quotes tagged 'tag1' or 'tag2' or 'tag3', one or more or all of these.
	* `[quotcoll author="Somebody" tags="tag1"]` displays quotes authored by 'Somebody' AND tagged 'tag1'.

* **orderby** *(string)*
	* When multiple quotes are displayed, the quotes or ordered based on this value. The value can be either of these:
		* 'quote_id' (default)
		* 'author'
		* 'source'
		* 'time_added'
		* 'random'
	
* **order** *(string)* 
	* The value can be either 'ASC' (default) or 'DESC', for ascending and descending order respectively.
	* For example, `[quotcoll orderby="time_added" order="DESC"]` will display all the quotes in the order of date added, latest first and the earliest last.
	
* **paging** *(boolean)*
	* The values can be:
		* false (or 0) (default)
		* true (or 1) -- introduces paging. This is used in conjunction with `limit_per_page` (see below).
	* For example, `[quotcoll paging=true limit_per_page=30]` will introduce paging with maximum of 30 quotes per page.
	* Note: if `orderby="random"` is used, paging is ignored.

* **limit_per_page** *(integer)*
	* The maximum number of quotes to be displayed in a page when paging is introduced, as described above.
	* The defualt value is 10. For example, `[quotcoll paging=true]` will introduce paging with maximum of 10 quotes per page.

* **limit** *(integer)*
	* The maximum number of quotes to be displayed in a single page ie., when paging is 'false'.
	* This can be used, for example, to display just a random quote. `[quotcoll orderby="random" limit=1]`
	
== The quotescollection_quote() template function ==

The quotescollection_quote() template function can be used to display a random quote in places other than sidebar.

Usage: `<?php quotescollection_quote($arguments); ?>`

The list of parameters (arguments) that can be passed on to this function:

* **show_author** *(boolean)*
	* To show/hide the author name
		* `true` - shows the author name (default)
		* `false` - hides the author name

* **show_source** *(boolean)*
	* To show/hide the source field
		* `true` - shows the source 
		* `false` - hides the source (default)

* **ajax_refresh** *(boolean)*
	* To show/hide the 'Next quote' refresh link
		* `true` - shows the refresh link (default)
		* `false` - hides the hides the refresh link
		
* **random** *(boolean)*
	* Refresh the quote in random or sequential order
		* `true` - random refresh (default)
		* `false` - sequential, with the latest quote first
		
* **auto_refresh** *(boolean/integer)*
	* To refresh the quote automatically
		* `true` - auto refresh every 5 seconds
		* `false` - auto refresh is off (default)
		* `integer` - auto refresh is on, and the number provided will be the refresh interval, in seconds.
			* For example, `<?php quotescollection_quote( array( 'auto_refresh' => 3 ) ); ?>` will refresh the quote every 3 seconds.
	
* **tags** *(string)*
	* Comma separated list of tags. Only quotes with one or more of these tags will be shown.
 
* **char_limit** *(integer)*
	* Quotes with number of characters more than this value will be filtered out. This is useful if you don't want to display long quotes using this function. The default value is 500.

* **echo** *(boolean)*
	* To `echo` or `return` the quote
		* `true` - the quote is echoed, ie., printed out
		* `false` - the quote block is returned as a string, the user can catch the string in a variable and output it wherever they please.

**Example usage:**

* `<?php quotescollection_quote(); ?>`

	* Uses the default values for the parameters. Shows author, hides source, shows the 'Next quote' link, no tags filtering, no character limit, displays the quote.

* `<?php quotescollection_quote( array( 'show_author' => false, 'show_source' => true, 'tags' => 'fun,fav' ) ); ?>`

	* Hides author, shows source, only quotes tagged with 'fun' or 'fav' or both are shown. 'Next quote' link is shown (default) and no character limit (default).

* `<?php quotescollection_quote( array( 'ajax_refresh' => false, 'char_limit' => 300 ) ); ?>`

	* The 'Next quote' link is not shown, quotes with number of characters greater that 300 are left out.
	
== Localization ==

Versions 1.1 and greater support localization. As of the current version, localization is available in the following languages (code / language / author):

* `ar` / Arabic / [Ahmed Alharfi](http://www.alharfi.com/)
* `be_BY` / Belarusian / [Alexander Ovsov](http://webhostinggeeks.com/)
* `bg_BG` / Bulgarian / [Martin Petrov](http://mpetrov.net/)
* `bs_BA` / Bosnian / Vukasin Stojkov
* `cs_CZ` / Czech / Josef Ondruch
* `da_DK` / Danish / [Rune Clausen](http://www.runemester.dk/)
* `de_DE` / German / [Tobias Koch](http://tobias.kochs-online.net/2008/05/multilingual-blogging-using-wordpress/)
* `el` / Greek / [Spiros Doikas](http://www.translatum.gr/)
* `es_ES` / Spanish / [Germán L. Martínez (Gershu)](http://www.gershu.com.ar/)
* `et_EE` / Estonian / [Iflexion](http://iflexion.com/)
* `fa_IR` / Persian / [Ehsan SH](http://mastaneh.ir/)
* `fi_FI` / Finnish / [Jussi Ruokomäki](http://jussi.ruokomaki.fi/)
* `fr_FR` / French / [psykotik](http://www.ikiru.ch/blog), Laurent Naudier
* `he_IL` / Hebrew / Tailor Vijay
* `hi_IN` / Hindi / [Ashish J.](http://outshinesolutions.com/)
* `hr_HR` / Croatian / [1984da](http://faks.us/)
* `hu_HU` / Hungarian / [KOOS, Tamas](http://www.koosfoto.hu/)
* `id_ID` / Bahasa Indonesia / [Kelayang](http://kelayang.com/)
* `it_IT` / Italian / [Gianni Diurno  (aka gidibao)](http://gidibao.net/index.php/2008/05/26/quotes-collection-in-italiano/)
* `ja` / Japanese / [Urepko Asaba](http://www.urepko.net/)
* `lt_LT` / Lithuanian / Lulilo
* `lv_LV` / Latvian / [Maris Svirksts](http://www.moskjis.com/)
* `mk_MK` / Macedonian / [Diana](http://wpcouponshop.com/)
* `nb_NO` / Norwegian (Bokmål) / [Christian K. Nordtømme](http://nextpage.no/)
* `nl_NL` / Dutch / Guido van der Leest, [Kristof Vercruyssen](http://www.simplit.be/)
* `pl_PL` / Polish / Marcin Gucia
* `pt_BR` / Brazilian Portugese / Tzor More
* `pt_PT` / Portugese / [Djamilo Jacinto](http://www.maxibim.net/)
* `ro_RO` / Romanian / Alexander Ovsov
* `ru_RU` / Russian / Andrew Malarchuk
* `sk_SK` / Slovak / [Stefan Stieranka](http://www.itec.sk/)
* `sr_RS` / Serbian / Vukasin Stojkov
* `sv_SE` / Swedish / [Julian Kommunikation](http://julian.se/)
* `ta_IN` / Tamil / [Srini](http://srinig.com/)
* `tr_TR` / Turkish / [Gürkan Gür](http://seqizz.net/)
* `uk_UA` / Ukrainian / Stas
* `zh_CN` / Simplified Chinese / [天毅许](http://www.freewarecn.com/)

You can translate the plugin in your language if it's not done already. The localization template file (quotes-collection.pot) can be found in the 'languages' folder of the plugin. After translating send the localized files to the [plugin author](http://srinig.com/wordpress/contact/) so that it's included in the next update. If you are not sure how to go about translating, contact the plugin author.

==Changelog==

* **2018-04-07: Version 2.0.10**
	* Removing apply_filters('the_title') for the widget title as it's out of place here, also could be problematic without argument 2.

* **2017-04-17: Version 2.0.9**
	* CSS fix.

* **2016-11-24: Version 2.0.8**
	* Security updates

* **2016-11-23: Version 2.0.7**
	* Security fixes

* **2016-04-04: Version 2.0.5**
	* Changed footer elements in widget back to div to prevent HTML validation errors
	* Minor modifications in admin page header markup and styling

* **2015-05-25: Version 2.0.4**
	* Updates to localization in Hungarian and Swedish languages

* **2015-04-29: Version 2.0.3**
	* Dutch localization updates by Guido

* **2015-04-25: Version 2.0.2**
	* Update to Turkish localization by Gürkan Gür

* **2015-04-20: Version 2.0.1**
	* Fix for unformatted output on refresh.

* **2015-04-20: Version 2.0**
	* Complete overhaul of the plugin. File organization modified, code refactored, the code is more class based.
	* New and improved admin interface with
		* Improved quotes list table using the `WP_List_Table` class
		* Screen options to customize the number of quotes displayed per page and show/hide columns
		* Search functionality
		* Option to import/export quotes in `JSON` format
		* An options page with options to customize the 'Next Quote' text, specify the maximum number of iterations for the auto-refresh feature, and enable dynamic fetching of the first random quote in cached websites.
	* Multi-widget support added. Now as many instances of the widget can be added.
	* Not-so-noticable, but important improvements to the markup generated for the front-end.
	* `uninstall.php` added. Now, when the plugin is deleted, the plugin's database table, plugin options, all will be removed... no trace left behind.
	* The translation template file `quotes-collection.pot` updated. Many of the translation strings have changed. Many new strings added, many old strings given up.
	* Updated localization in Tamil, Hebrew, Slovak, French, Ukrainian, German and Norwegian (Bokmål) languages

* **2012-12-16: Version 1.5.9**
	* Fix for cases where random refresh always fetches only two quotes
	* Bugfix for widget

* **2012-12-10: Version 1.5.8**
	* Modified html tags filtering for 'quote'. Now all html tags allowed for blog posts can be used in the 'quote' field.
	* Fixed quotes count display in admin so that plural shows as 'quotes' and not as 'quote'.
	* Fixed Ajax refresh bug.

* **2012-12-08: Version 1.5.7**
	* Localization in Macedonian language added, Persian language updated.
	* Code improvements
	* Documentation changes (FAQ updated)

* **2012-07-02: Version 1.5.6**
	* Security fix (pointed out by Charlie Eriksen via Secunia SVCRP)

* **2012-03-28: Version 1.5.5.1**
	* Minor fix (the missing semicolon in <code>&amp;nbsp;</code>)
	
* **2012-03-27: Version 1.5.5**
	* Security fixes
	* Shortcode output pagination issue fixed
	* Shortcode: 'time_added' value for 'orderby' parameter fixed.
	* Localization in Estonian, Greek, Belarusian and Romanian languages added.
	
* **2011-08-31: Version 1.5.4**
	* 30 and 60 seconds added to widget auto refresh time option.
	* Updates for Italian and Japanese localizations.

* **2011-08-08: Version 1.5.3**
	* Hebrew localization added
	* id attribute added for blockquote tags for shortcode quotes.

* **2011-07-18: Version 1.5.2**
	* Slovak localization added
	* Fixes
	
* **2011-07-01: Version 1.5.1**
	* Bahasa Indonesia localization updated
	
* **2011-06-30: Version 1.5**
	* Shortcodes revamp. The new shortcode `[quotcoll]` uses the WordPress shortcode API and comes with various options. The old `[quote]` is deprecated, but will still work as a measure of backwards compatibility.
	* Ajax calls are now made to `wp-admin/admin-ajax.php`. This could potentially fix problems some websites had with the older system.
	* Pagination in admin page. Other minor improvements in the admin page.
	* Fixes for deprecated functions and undefined variables. Various other minor fixes and improvements.
	* Bahasa Indonesia (id_ID) localization added. Tamil localization updated.
	* The `.po` template file `quotes-collection.pot` is updated. New strings added, few strings have become obsolete.

* **2010-12-03: Version 1.4.4**
	* Updated Simplified Chinese localization
	
* **2010-11-26: Version 1.4.3**
	* Norwegian translation added
	* French and Simplified Chinese localizations updated
	
* **2010-06-24: Version 1.4.2**
	* Italian localization updated
	
* **2010-06-19: Version 1.4.1**
	* Compatibility with WP 3.0 multi-site functionality
	* Tamil localization updated
	
* **2010-06-17: Version 1.4**
	* Added ability to refresh quotes sequentially in the order added instead of random refresh.
	* Added ability to refresh quotes automatically in a specified time interval
	* The widget has two additional options (random refresh and auto refresh (+ time interval))
	* 'Quotes Collection' admin panel is now listed as a first-level menu from being a sub-menu under 'Tools' 
	* Other minor fixes, changes and improvements
	
* **2010-06-06: Version 1.3.8**
	* Fix for the backslashes issue.

* **2010-03-02: Version 1.3.7**
	* Localization in Hindi added.
	
* **2009-11-10: Version 1.3.6**
	* Localization in Bulgarian and Czech languages added.

* **2009-09-22: Version 1.3.5**
	* Brazilian Portugese localization added.
	* Modifications in quotes-collection.js (for better debugging in case of error)
	
* **2009-08-24: Version 1.3.4**
	* Finnish localization added.
	* FAQ updated.

* **2009-08-12: Version 1.3.3**
	* Localization in Simplified Chinese added.

* **2009-06-12: Version 1.3.2**
	* Latvian translation added. Hungarian translation updated.

* **2009-05-29: Version 1.3.1**
	* Bug fix (URL parsing issue)
	* Lithuanian translation added. Spanish and Russian updated

* **2009-05-28: Version 1.3**
	* Uses jQuery instead of SACK library for the AJAX refresh functionality
	* New widget option to filter based on tags
	* New widget option to set character limit for the random quote
	* Template function changed to `quotescollection_quote()`. The old function `quotescollection_display_randomquote()` will still work.
	* Parameters now passed in string format in the template function
	* Hungarian, Belarusian translations added. Swedish, Italian, Croatian, Turkish, Japanese, Persian, French and Tamil updated.
	* If you insert a url in quote, author, source, it becomes clickable in the random quote and  in quotes pages.
	* Other minor improvements
	
* **2009-04-20: Version 1.2.8**
    * Correcting a mistake in the previous update.

* **2009-04-20: Version 1.2.7**
    * Added localization in Portugese language
    * Fix to handle directory paths in windows servers

* **2009-04-14: Version 1.2.6**
    * Added localization in Serbian, Bosnian, Dutch and Persian languages

* **2009-02-27: Version 1.2.5**
    * Added localization in Swedish language
    * Minor tweaks and fixes

* **2009-02-04: Version 1.2.4**
	* Added translation in Danish, Croatian and Japanese languages
    * Minor fixes
    * FAQ section added in readme.txt to answer the frequently asked questions.

* **2008-11-08: Version 1.2.3**
    * Added Ukrainian translation (thanks to Stas for the translation)
    * Tested the plugin for the new admin interface that comes with WordPress 2.7 and a few tweaks. The plugin will work just fine in older WP versions

* **2008-10-06: Version 1.2.2**
    * Security fix, HTML tidy fix, other fixes
    * Updated Turkish trasnlation

* **2008-09-24: Version 1.2.1**
    * Arabic translation added
    * Minor fix (quotes-collection.js: errotext -> errortext)

* **2008-09-22: Version 1.2**
    * All javascript code moved to quotes-collection.js. This makes the code neater.
    * Translations for French, Polish and Turkish languages added.
    * Italian and Russian translations updated.
    * A few minor fixes and small improvements.

* **2008-07-02: Version 1.1.4**
    * Bug fixes. The plugin was not handling properly apostrophes in author and source fields. This is fixed now.
    * Other small fixes.

* **2008-06-05: Version 1.1.3.1**
    * Added Spanish translation.
    * Updated Italian translation.

* **2008-06-01: Version 1.1.3**
    * Improvements
    * Updated German translation
    * Added Russian translation

* **2008-05-28: Version 1.1.2.1**
    * VARCHAR(256) -> VARCHAR(255) (VARCHAR(256) doesnt work with MySQL 4.0)

* **2008-05-28: Version 1.1.2**
    * Modifications in the automatic database update functionality
    * Fixed problem with German translation
    * Added Italian translation

* **2008-05-25: Version 1.1.1**
    * security fix

* **2008-05-25: Version 1.1**
    * Tagging feature
    * Internationalization
    * Fixes and improvements

* **2008-03-11: Version 1.0**
    * Compatible with WordPress 2.5
    * Bug fixes and various other improvements

* **2008-02-06: Version 0.9.5**
    * Fixed problem with non English characters in author names while using the tag `[quote|author=]`

* **2008-01-16: Version 0.9.4**
    * Support for utf-8 characters
    * Fixed problem with linebreaks

* **2007-12-19: Version 0.9.3**
    * Fixed a JavaScript issue
    * Removed unnecessary `<h2></h2>` tags above random quote when title field is left blank in widget control options. `<h2>` tags displayed only when there is a title.

* **2007-12-18: Version 0.9.2**
    * Provision to add random quote anywhere in the template.

* **2007-12-16: Version 0.9.1**
    * Bug fix

* **2007-12-15: Version 0.9**
    * Initial release


== Upgrade Notice ==

= 2.0.10 =
Do upgrade if you get a "Warning: Missing argument 2 for WC_Template_Loader::unsupported_theme_title_filter() in ..." message.

= 2.0.9 =
CSS fix. If you are running a version < 2.0.8, please upgrade as the lower versions may run into security issues. If you upgrade from a version prior to 2.0, you will have to re-add the widget and set the widget options once again after upgrading.

= 2.0.8 =
Important security fixes. Upgrade highly recommended. If you upgrade from a version prior to 2.0, you will have to re-add the widget and set the widget options once again after upgrading.

= 2.0.5 =
If you upgrade from a version prior to 2.0, you will have to re-add the widget and set the widget options once again after upgrading. Version 2.0 is a major update.

= 2.0.4 =
If you upgrade from a version prior to 2.0, you will have to re-add the widget and set the widget options once again after upgrading. Version 2.0 is a major update. v2.0.1 fixes unformatted output on refresh. v2.0.2, 2.0.3 and 2.0.4 are localization updates.

= 2.0.3 =
If you upgrade from a version prior to 2.0, you will have to re-add the widget and set the widget options once again after upgrading. Version 2.0 is a major update. v2.0.1 fixes unformatted output on refresh. v2.0.2 and v2.0.3 updates Turkish and Dutch localizations respectively.

= 2.0.2 =
If you upgrade from a version prior to 2.0, you will have to re-add the widget and set the widget options once again after upgrading. Version 2.0 is a major update with new features and plenty of improvements. v2.0.1 fixes unformatted output on refresh. v2.0.2 updates Turkish localization.

= 2.0.1 =
If you upgrade from a version prior to 2.0, you will have to re-add the widget and set the widget options once again after upgrading. Version 2.0 is a major update with new features and plenty of improvements. v2.0.1 fixes unformatted output on refresh.

= 2.0 =
Please note that you will have to re-add the widget and set the widget options once again after upgrading to this version. Nothing much otherwise will be affected. This is a major update with new features and plenty of improvements, upgrade highly recommended if you use WP 3.1 and above.
