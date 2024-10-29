=== Author Showcase ===
Contributors: raynfall
Donate link: https://www.claireryanauthor.com
Tags: book, author, display, covers, amazon, kobo, ibooks, smashwords, lulu, barnes, noble
Requires at least: 4.0
Tested up to: 5.5
Stable tag: 1.4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Author Showcase plugin allows authors to display their books, with buy links, on their website in a nicely formatted style.

== Description ==

<p>Author Showcase is an all-in-one plugin for authors to show off their work on their site.</p>
<p>The Author Showcase plugin was written by an author, for authors. It was created in order to make it easy for authors to display their books, along with the buy links, in a number of different formats.</p>
<p>You can include the title, subtitle, cover, author name, series name, and blurb, as well as any number of links to various websites where each book may be bought.</p>
<p>You can then display your books on a page or post using a shortcode, or in a widget. All the messy HTML is taken care of, and each book is nicely formatted (and it should use your theme's CSS).</p>
<ul>
<li>Multiple display options using shortcodes - choose icons or text links, change the icon size</li>
<li>Advanced sidebar widget included</li>
<li>Column, Grid, List, and Single display modes, controlled by shortcodes</li>
<li>Include Amazon and Goodreads reviews for each book</li>
<li><a href="https://claireryanauthor.com/btbe-user-manual/">User manual available</a></li>
<li>Developed with the help and feedback of the <a target="_blank" href="http://www.kboards.com/index.php/topic,218602.50.html">Kboards Writer's Cafe community!</a></li>
</ul>

<p>Suggestions for new functionality welcome! Please contact me here:</p>

<a href="https://www.twitter.com/@aetherlev">Twitter</a><br />
<a href="https://www.claireryanauthor.com">Website</a></p>

== Installation ==

1. Upload the zip file to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Look at your WordPress Admin menu; you should see a new link at the end called Author Showcase Book List.
4. Click on Add New Book to begin adding your books. Remember to add a series of books in the order in which they were published!
5. On the Book List screen, note the Book ID numbers. You'll use these to display your books.
6. Go to the Appearance -> Widgets screen. You should now see a new widget called Author Showcase. Click and drag it over to your chosen sidebar, and it’ll open up automatically.
7. Fill in the header. This is the widget title, and it’ll display above your books.
8. Enter a single Book ID, or a comma separated list of Book IDs in the order in which you want them to appear. They'll be listed on the same row. You can choose to display more fields than just the cover and sales links. (The links will be hidden in a slideout menu until you click on the cover.)
9. Save the widget. You’re done! Check your site and click on the cover images to see the menu slide out from the bottom.
10. If you want to use the shortcodes, please refer to the <a href="https://claireryanauthor.com/btbe-user-manual/">user manual</a>.

== Screenshots ==
screenshot1.png
screenshot2.png

== Changelog ==

= 1.4.3 =
* Added a Javascript fix to make it compatible with WP5.5

= 1.4.2 =
* Minor text fixes, testing with latest version of WP

= 1.4.1 =
* Fixed an issue with GoodReads reviews not loading over HTTPS

= 1.4 =
* Added French and Portuguese (Brazil) translations
* Fixed a bug that interfered with pagination in the Book List

= 1.3.3 =
* Fixed a bug which affected the display of the short blurb in the sidebar widget

= 1.3.2 =
* Fixed a bug which caused sales links to be improperly loaded, added and deleted

= 1.3.1 =
* Fixed an issue with absolute URLs being saved for icons and covers; images now save relative URLs

= 1.3 =
* Added a new 'links_only' mode to the display shortcode in order to display a set of text buy links

= 1.2 =
* Bugfix - changed the requirements so that only a title is needed for a valid record; also checks through Javascript, so this stops added links from being removed on page load

= 1.1 =
* Bugfix - fixed an issue with FireFox users not being able to add sales links
* Bugfix - subtitles are now optional in each book listing; previously books were not saved properly unless a subtitle was present