=== Quotes Collection ===
Contributors: SriniG
Donate link: https://www.paypal.me/srinigcom/20
Tags: quotes, quotations, random quote, widget, gutenberg, quote rotator
Requires at least: 4.6
Tested up to: 5.1
Requires PHP: 5.6
Stable tag: trunk
License: GNU General Public License

Quotes Collection plugin helps you collect, manage and display your favourite quotes in your WordPress website.

== Description ==

Quotes Collection plugin helps you collect, manage and display your favourite quotes in your WordPress website or blog.


**Features**

* **Admin interface**: A robust admin interface to add, edit, import, export and generally manage the collection of quotes.
* **Sidebar widget**: A random quote from your collection can be displayed in any widget area using the ‘Random Quote’ widget. Includes options to refresh the quote displayed in the widget manually or automatically, randomly or sequentially.
* **Gutenberg blocks**: The plugin includes two blocks that can be added in pages and posts. ***(NEW in version 2.5)***
	* ‘Quotes’ block to display all the quotes or a set of quotes, with presentation, filtering, paging and other options.
	* ‘Random Quote’ block that functions similarly to the Random Quote widget, with additional presentation options.
* **Shortcode**: All quotes or a set of quotes can be displayed on a WordPress page by placing a `[quotcoll]`shortcode. Few examples are provided below. For more examples and the full list of arguments, visit the [Plugin Home Page](http://srinig.com/wordpress/plugins/quotes-collection/).
	* Placing `[quotcoll]` in the page displays all quotes.
	* `[quotcoll author="Somebody"]` displays quotes authored by Somebody.
	* `[quotcoll tags="tag1,tag2,tag3"]` displays quotes tagged tag1 or tag2 or tag3, one or more or all of these
	* `[quotcoll orderby="random" limit=1]` displays a random quote
	* `[quotcoll ajax_refresh=true]` displays a random quote that will automatically refreshed every 5 seconds.
* **The template function**: To code the random quote functionality directly into a template file, the template function `quotescollection_quote()` can be used. Please visit the [Plugin Home Page](http://srinig.com/wordpress/plugins/quotes-collection/) for details.
* **Import/Export** your collection of quotes in JSON format.

== Frequently Asked Questions ==


= How to hide the 'Next quote »' link in the widget? =

You can do this by turning off the 'Ajax Refresh' feature in widget options.

= How to change the link text from 'Next quote »' to something else? =

This text can be changed from *Quotes Collection -> Options* in your WordPress admin area

= The 'Next quote »' link is not working. Why? =

Make sure your theme's header.php file has the code `<?php wp_head(); ?>` just before `</head>`. If you still experience the problem, [contact](http://srinig.com/wordpress/contact/) the plugin author.

= I have added a number of quotes, but some of the quotes never get displayed in the widget. Why? =

If you want all of the quotes to display, make sure all all the quotes fall within the 'Character limit'. There is an option named 'Character limit' for the widget (bottom most, under the 'advanced options') with a default value of '500'. The value can be changed, or simply removed and the field left blank so that none of the quotes get filtered out based on length.

= I have a long list of quotes, and the `[quotcoll]` shortcode puts all of the quotes in a single page. Is there a way to introduce pagination and break the long list of quotes into different pages? =

Yes, pagination is supporterd in versions 1.5 and greater. `paging` and `limit_per_page` attributes can be used to achieve this. For example, `[quotcoll paging=true limit_per_page=30]` will introduce pagination with a maximum of 30 quotes per page.

= Can an existing shortcode be converted to a Gutenberg block? =

Yes, the existing shortcode will appear as 'Classic' block in the block editor interface. All you need to do is click on the 'More options' (three vertical dots on the top right corner) and click 'Convert to Blocks' in order to convert the shortcode into a 'Quotes' block.


== Screenshots ==

1. Admin interface
2. 'Random Quote' widget options
3. A random quote in the sidebar
4. 'Quotes' block editor interface
5. 'Random Quote' block editor interface, customized with block controls

== Localization ==

You can translate the plugin in your language at [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/quotes-collection).

==Changelog==

* **2019-03-06: Version 2.5.2**
	* Fixed issues with auto_refresh in shortcode.

* **2019-03-05: Version 2.5.1**
	* Fix for an issue where JS arguments break HTML loop when widget is used inside certain page builders.

* **2019-02-23: Version 2.5**
	* Gutenberg blocks included. *Note: to make use of these blocks, you should have WP 5.0 or above, or have the [Gutenberg plugin](https://wordpress.org/plugins/gutenberg/) installed and activated.*
		* 'Random Quote' block that functions similarly to the widget, with additional presentation options.
		* 'Quotes' block to display all the quotes or a set of quotes. With presentation, filtering, paging and other options.
		* Existing shortcodes can be transformed into the 'Quotes' block, with all the attributes intact.
	* The shortcode now includes refresh options and options to hide author and/or source.
	* Option to specify the minimum user level required to add and manage quotes. Previously, anyone with 'Editor' or greater credentials could add and manage quotes.
	* Fixed the bug that included a dummy 'Quotes Collection' menu in admin menus for unauthorized users.
	* Localization is now entirely handled at [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/quotes-collection).

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

= 2.5.2 =
Fixed issues with auto_refresh in shortcode.

= 2.5.1 =
Comes with a fix for an issue where widget breaks HTML loop in some cases. Upgrade recommended.

= 2.5 =
Major update. Gutenberg blocks included, refresh options for shortcode added, other improvements and fixes.

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
