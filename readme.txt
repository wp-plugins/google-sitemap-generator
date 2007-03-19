=== Google Sitemap Generator for WordPress ===
Contributors: arnee
Donate link: http://www.arnebrachhold.de/redir/sitemap-paypal
Tags: google, sitemaps, google sitemaps, yahoo, man, xml sitemap
Stable tag: 2.7.1

This plugin will create a google sitemaps compliant XML-Sitemap of your WordPress blog.

== Description ==

This plugin will create a google sitemaps compliant XML-Sitemap of your WordPress blog. Currently homepage, posts, static pages, categories and archives are supported. The priority of a post depends on its comments. More comments, higher priority! If you have external pages which don't belong to your blog, you can also add them to the list. This plugin will automatically notice Google whenever the sitemap gets regenerated. You can also visit the plugin homepage for the latest beta version.

== Installation ==

1. Upload the full directory into your wp-content/plugins directory
2. Make your blog directory writeable OR create two files named sitemap.xml and sitemap.xml.gz and make them writeable via CHMOD In most cases, your blog directory is already writeable.
3. Double make sure that your blog directory is writable or two writable files named sitemap.xml and sitemap.xml.gz exist!
4. Activate it in the Plugin options
5. Edit or publish a post or click on Rebuild Sitemap on the Sitemap Administration Interface

== Frequently Asked Questions == 

= I have no comments (or disabled them) and all my postings have a priority of zero! =

Disable automatic priority calculation and define a static priority for posts!

= Do I always have to click on "Rebuild Sitemap" if I modified a post? =

No! If you edit/publish/delete a post, your sitemap gets regenerated!

= So much configuration options… Do I need to change them? =

No! Only if you want. Default values should be ok!

= Works it with all WordPress versions? =

I’m sorry I only tested it on 1.5.1.1. Some users reported problems with Wordpress 1.5. You should consider an update of your blog to the current WordPress Version which also contains "an important security fix".

= I get an fopen error and / or permission denied =

If you get permission errors make sure that the script has writing rights in your blog directory. Try to create the sitemap.xml resp. sitemap.xml.gz at manually and upload them with a ftp program and set the rights with CHMOD. Then restart sitemap generation on the administration page. A good tutorial for changing file permissions can be found on the WordPress Codex.

= Which MySQL Versions are supported? =

MySQL 4 works with all version, MySQL 3 support was added in version 2.12

= Do I really need to use this plugin? =

Maybe not if Google knows you page very well and visits your blog every day. If not, it's a good method to tell google about your pages and the last change of them. This makes Google possible to refresh the page only if it's needed and you save your bandwidth.

== Screenshots ==

1. Administration interface in WordPress 1.5. Check the latest beta for WordPress 2.0 Style.