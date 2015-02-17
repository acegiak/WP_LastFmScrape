=== Plugin Name ===
Contributors: acegiak
Donate link: http://acegiak.net/
Tags: lastfm,pesos,scrape
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Scrapes a lastfm account for recently listened songs and posts them to wordpress

== Description ==

LastFmScrape will poll your recently listened songs with WP_Cron every 15 minutes and then post any new songs to your blog in the category defined by it's slug in the LastFmScrape settings.

If you have the plugin Indieweb Post Kinds installed it will make posts of the "listen" kind. I have no idea what will happen if you don't have that installed

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. configure settings->LastFmScrape settings

== Frequently Asked Questions ==

= What is the last.fm api key and how do I get it? =

The last.fm api key is what last.fm uses to identify who is asking for the songlist information.
You can get one from:
http://www.last.fm/api/account/create




== Changelog ==

= 0.02 =
* fixing timestamps
* preventing posting before scrobble completes

= 0.01 =
* very first version