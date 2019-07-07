=== WP Customer Reviews ===
Contributors: bompus
Donate link: http://www.gowebsolutions.com/wp-customer-reviews/
Tags: business, google, hcard, schema.org, hproduct, hreview, microformat, microformats, mu, places, plugin, product, rating, ratings, rdfa, review, review box, review widget, reviews, seo, service, snippet, snippets, testimonial, testimonials, widget, wordpressmu, wpmu
Requires at least: 3.0.0
Tested up to: 5.0
Stable tag: 3.4.1
License: MIT
License URI: http://opensource.org/licenses/MIT

Allows your visitors to leave business / product reviews. Testimonials are in Microdata / Microformat and may display star ratings in search results.

== Description ==

There are many sites that are crawling for user-generated reviews now, including Google Places and Google Local Search. WP Customer Reviews allows you to setup a specific page on your blog to receive customer testimonials for your business/service OR to write reviews about a product.

**Big News! Version 3 has been released.** [Click here for details](http://www.gowebsolutions.com/wp-customer-reviews/?from=wpcr3_directory_notice_1 "Click here for details")

* WP Multisite and Multiuser (WPMU / WPMS / Wordpress MU) compatible.
* All submissions are moderated, which means that YOU choose which reviews get shown.
* Reviews are displayed to visitors in a friendly format, but search engines see the Schema.org microformat.
* Multiple anti-spam measures to prevent automated spambots from submitting reviews.
* Completely customizable, including which fields to ask for, require, and show.
* Shortcodes available for inserting reviews and review form on any page or widget.
* Works with caching plugins and custom themes.
* Includes an external stylesheet so you can modify it to better fit your theme.
* Reviews can be edited by admin for content and date.
* Admin responses can be made and shown under each review.
* Support for adding your own custom fields.
* The plugin can be used on more than one page, and can be used on posts.
* Supports both `Business` and `Product` review types.
* Shows aggregate reviews microformat.
* Fast and lightweight, even including the star rating image. This plugin will not slow down your blog.
* Validates as valid XHTML 1.1 (W3C) and valid Microformats (Rich Snippets Testing Tool).
* And much more...

Almost every new feature that has been added was due to the generous support and suggestions of our users. If you have a suggestion or question, do not hesitate to ask in our forum.

More information at: [**WP Customer Reviews**](http://www.gowebsolutions.com/wp-customer-reviews/)

== Installation ==

1. Upload contents of compressed file (wp-customer-reviews) to the `/wp-content/plugins/` directory. 
2. Activate the plugin through the `Plugins` menu in WordPress admin.
3. Create a WordPress page to be used specifically for gathering reviews or testimonials.
4. Go into settings for WP Customer Reviews and configure the plugin.

== Screenshots ==

1. Front-end display
2. Submit a review (1)
3. Submit a review (2)
4. Admin moderation (1)
5. Admin moderation (2)
6. Plugin settings
7. Submit form settings
8. Display settings
9. Enabling a page for Business reviews
10. Enabling a page for Product reviews

== Frequently Asked Questions ==
* Bug Report / Feature Requests: **Submit from Plugin Settings page**
* [**Community Support Forum**](http://wordpress.org/tags/wp-customer-reviews?forum_id=10)

== Changelog ==

= 3.4.0 / 3.4.1 =
* 11/19/2018
* [Update] 3.4.0 - Tested compatibility with WordPress 5.0 and Gutenberg editor.
* [Update] 3.4.1 - Forgot to update Tested up to: 5.0

= 3.3.0 =
* 10/08/2018
* [Update] 3.2.3 added the ability to assign a review to any post/page, not just WPCR-enabled posts. This caused some confusion when assigning reviews manually, so the "Reviewed Post / Page" dropdown has been refactored to display WPCR-enabled posts first, exclude posts with no title, and display the post slug. You can find the post you are looking for by clicking the dropdown, then begin to type the name of your post to search for and select it.

= 3.2.5 =
* 10/04/2018
* [Update] Backend checkboxes are now Yes/No select dropdowns. Developers everywhere rejoice with me eliminating non-form-submitting HTML checkboxes. Should eliminate reported issue with WPCR-enabled posts becoming non-enabled.
* [Update] Simplified some logic in the admin JS file.
* [Bugfix] The save_post hook will now only run our custom meta updating logic when saving a post via the standard WP post editing form. Should eliminate reported issue with WPCR-enabled posts becoming non-enabled.
* [Bugfix] The save_post hook no longer deletes/ignores updates to our custom meta on all falsy values. Submitted value must be an empty string to trigger the delete. This allows for a value of '0' to actually save. 
* [Bugfix] Fixed race condition in displaying Business/Product fields based on Review Format dropdown when editing posts. Previously, both Business and Product fields could sometimes display, which was confusing.
* [Bugfix] If a visitor submits a review with line breaks, they used to be stripped out during front-end display. These are now converted into HTML line breaks.

= 3.2.4 =
* 10/03/2018
* [Bugfix] Allow assiging reviews to posts that are not yet WPCR enabled (via checkbox on post edit form)
* [Bugfix] Fixed missing href in aggregate snippet when using shortcode to display reviews by specific POSTID.
* [Bugfix] If we detect that we are viewing the same page as the reviews being output are assigned to, do not link aggregate/reviews using href, because it links to the same page we are on.
* [Bugfix] Fixed 1x1 temporary review image to output absolute URL. For some reason, it was previously outputting relative, contrary to plugins_url() WP docs.

= 3.2.3 =
* 10/03/2018
* [Bugfix] Review holder container was overflowing boundaries on smaller screen sizes.
* [Update] When editing a post, business/product info fields would only display if WPCR was enabled for the post. They now display always.
* [Update] When using [WPCR_INSERT] shortcode, we now pretend the "Enable WPCR" checkbox is checked to allow reviews to output.
* [Update] When using [WPCR_SHOW] shortcode, we now pretend the "Enable WPCR" checkbox is checked on the selected POSTID="###" post to allow aggregate/reviews to output. This allows for some advanced usage of do_shortcode in themes for optimal placement.

= 3.2.1 =
* 07/27/2018
* [Update] Huge performance improvement for blogs with large postmeta db tables. Thanks gig8@github.

= 3.2.0 =
* 07/03/2018
* [Update] Prevent business phone and address microdata from outputting if they have not been filled out.
* [Update] To prevent Structured Data Testing Tool errors, a 1x1 image has been added to the microdata. We have plans to replace this in the future with an actual image. From our testing, this field is not actually required and made no difference, but some are concerned about the testing tool error, hence this partial fix.
* [Update] Instead of outputting "Blank Business Name" / "Blank Product Name" when the plugin is used improperly, we instead output the name of the site.
* [Update] Instead of outputting an empty business URL when the plugin is used improperly, we instead output the url of the site.
* [Update] Various performance improvements when outputting multiple reviews.

= 3.1.9 =
* 04/03/2018
* [Update] Allow HIDEREVIEWS=0 to be used in shortcode without needing the (unreleased) Pro version
* [Bugfix] Fixed an invalid CSS important rule on a label, making it valid and as intended.

= 3.1.8 =
* 03/30/2018
* [Bugfix] Compatibility with PHP 4.3+, apparently people still use PHP 4.3.x and 4.4.x

= 3.1.7 =
* 03/27/2018
* [Bugfix] To prevent conflicts with other plugins, we renamed our usage of select2 to wpcr3_select2, and are loading it locally instead of via a CDN.

= 3.1.6 =
* 03/24/2018
* [Update] Significant performance improvements when logged in or using WP admin. Blogs with many review-enabled posts should notice significantly faster page load times.
* [Bugfix] "Enable WP Customer Reviews for this page" form should now properly initialize when adding new posts. Previously, it only worked properly on existing posts.

= 3.1.5 =
* 11/16/2017
* [Update] compatible with WP 4.9

= 3.1.4 =
* 10/16/2017
* [Bugfix] Fixed validation issue which broke AMP

= 3.1.3 =
* 05/31/2017
* [Bugfix] css typo bug fix
* [Update] compatible with WP 4.8

= 3.1.2 =
* 04/21/2016
* [Update] Made activation process simpler
* [Update] Images losslessly compressed

= 3.1.1 =
* 04/19/2016
* [Bugfix] Fixed possible issues with WordPress 3.6

= 3.1.0 =
* 04/09/2016
* [Bugfix] Fixed possible issue with PHP 7.0

= 3.0.9 =
* 04/05/2016
* [Security] Prevented CSRF and XSS in admin tools.

= 3.0.8 =
* 01/04/2016
* [Bugfix] Fixed the appearance of a security hole with admin tools. Malicious action was not possible.
* [Bugfix] Fixed deprecation warning with wpseo_pre_analysis_post_content (Yoast SEO).

= 3.0.7 =
* 11/15/2015
* [Bugfix] In some installations, ajax requests were still failing. We are reverting to using admin-ajax once again.

= 3.0.6 =
* 11/12/2015
* [Bugfix] In some installations, a dynamic CSS file could not be written upon plugin activation.
* [Bugfix] In some installations, ajax requests to admin-ajax were failing. We are trying a new method.
* [Bugfix] In some installations, the number of reviews displayed for "Average Rating" was inflated.
* [Bugfix] Email notifications for new reviews were missing a timestamp in the subject line.
* [Bugfix] When using [WPCR_SHOW POSTID="123"] shortcode on the page ID 123, reviews would output twice.
* [Bugfix] When a page had 0 reviews, the average rating would show 2.5 stars instead of 0.
* [Feature] Added PAGINATE and PERPAGE as shortcode options.

= 3.0.5 =
* 10/19/2015
* [Bugfix] JavaScript will now work with older versions of jQuery

= 3.0.4 =
* 10/18/2015
* [Bugfix] Fixed post/page saving issue

= 3.0.3 =
* 10/18/2015
* [Bugfix] Fix for broken JavaScript

= 3.0.2 =
* 10/18/2015
* [Bugfix] Shortcode copied/pasted itno WP visual editor should now work better
* [Bugfix] Migrating from 2.x would sometimes duplicate imported reviews ( see "Tools" settings tab for fix )
* [Bugfix] Migrating from 2.x would sometimes skip importing reviews ( see "Tools" settings tab for fix )
* [Bugfix] When paginating reviews on the front-end, "reviewed on" page links would sometimes be not linked
* [Bugfix] Relaxed the human detection anti-spam rules a bit
* [Bugfix] Fixed "failed the spambot check" issue when WP back-end is SSL, but front-end is not
* [Bugfix] Fixed some PHP error notices
* [Bugfix] JavaScript will now work with older versions of jQuery
* [Update] "Tools" tab added to plugin settings. This will contain various methods for managing/fixing review data.
* [Update] When adding reviews manually in WP admin, the WP post title now matches user-added reviews
* [Update] You can now edit the WP post title of reviews

= 3.0.1 =
* 09/29/2015
* [Update] Enabled for custom post types
* [Update] Upgrading from 2.x should go smoother for some people

= 3.0.0 =
* 09/10/2015
* [Update] Complete code cleanup and rewrite
* [Update] Complete overhaul of settings and management interface

== Upgrade Notice ==

= 3.0.0 =
A complete overhaul of the codebase and management interface
