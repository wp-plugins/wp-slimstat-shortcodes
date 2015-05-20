=== WP SlimStat Shortcodes ===
Contributors: coolmann
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=Z732JS7KQ6RRL&lc=US&item_name=WP%20SlimStat&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted
Tags: chart, analytics, visitors, users, spy, shortstat, tracking, reports, seo, referers, analyze, wassup, geolocation, online users, spider, tracker, pageviews, world map, stats, maxmind, flot, stalker, statistics, google+, monitor, seo
Requires at least: 3.8
Tested up to: 4.2
Stable tag: 2.6

== Description ==
This plugin has been discontinued. Shortcodes are now available directly in [Slimstat 4](https://wordpress.org/plugins/wp-slimstat/). Go get your copy today!

== Changelog ==

= 2.6 =
* Discontinued. Functionality has been rolled into main plugin.

= 2.5.1 =
* Smarter handling of the permalink structure (thank you, [Tomcat0754](https://wordpress.org/support/topic/lcpost_link-does-not-show-full-link))

= 2.5 =
* Code has been cleaned up and updated to leverage the recent updates in our APIs

= 2.4.4 =
* Fixed a compatibility issue with the new WP SlimStat API

= 2.4.3 =
* Fixed a compatibility issue with the new WP SlimStat API

= 2.4.2 =
* Fixed a bug in calculating NOW() when the DB server's timezone was different from WP's timezone

= 2.4.1 =
* Added support for natural language date ranges: day equals today, year equals last year, etc. Have fun!

= 2.4 =
* **Major change**: in order to avoid a conflict with a character used to define regular expressions, the SEPARATOR has changed from | to &&& (three & chars). Please make sure to update all your shortcodes accordingly!

= 2.3 =
* Fixed: bug with strtotime filter related to the new DB Library (thank you, [STONE5572](http://wordpress.org/support/topic/shortcodes-not-working-39))

= 2.2 =
* Updated: Source code now leverages the new DB Library introduced in WP SlimStat 3.5.2

= 2.1 =
* Added: shortcode to count all pageviews recorded so far (not just the current month, thank you [Zeb](http://wordpress.org/support/topic/total-pageviews))
* Fixed: minor bugs in interpreting certain values

= 2.0 =
* Code has been almost completely rewritten
* New filters have been added
* Syntax has been simplified