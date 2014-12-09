=== Gearside Developer Dashboard ===
Contributors: GreatBlakes
Donate link: http://gearside.com/wordpress-developer-information-dashboard/
Tags: developer, information, domain, registrar, hostname, server, version, speed, search, todo, manager
Requires at least: 3.0.1
Tested up to: 4.0
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add metaboxes on your WordPress Admin Dashboard for developer info and TODO Manager

== Description ==

This plugin allows developers to have at a glance information directly on the WordPress Admin Dashboard. From basics like domain and server IP to more advanced like registrar, expiration date, and page speed timing. Additionally, a second metabox is available which finds TODO comments in the theme and organizes them. It also allows for categorizing and prioritizing TODO tasks.

[Documentation for the TODO Manager](http://gearside.com/wordpress-dashboard-todo-manager/)
[Additional documentation for the Developer Information Metabox](http://gearside.com/wordpress-developer-information-dashboard/)

== Installation ==

1. Upload '/gearside-developer-dashboard/' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Do I need to change any settings to customize this plugin for my site? =
No.

= Do I need to use a special syntax for TODOs?
This plugin looks for "@TODO", so you won't need to change anything if that's how you do it already. Category and Priority are optional and should be after the @TODO. Example: //@TODO "Graphics" 3: Lorem ipsum dolor sit amet.

= Can I hide @TODO tasks from the manager?
Yes! Use a priority of 0 to hide them from the @TODO Manager.

=One of the detected developer info items is incorrect or broken.
The developer info does it's best to detect things that aren't consistent across web servers, so there may be certain instances where something is incorrect. If data is known to be incorrect, the metabox will hide it (which is why you may not see an domain expiration date). If something is blatantly incorrect or broken, contact me!


== Screenshots ==

1. screenshot-1.png

== Changelog ==

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.0 =
Initial release