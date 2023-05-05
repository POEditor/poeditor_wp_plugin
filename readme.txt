=== POEditor ===
Contributors: 
Donate link: 
Tags: localization, translate, api
Requires at least: 3.5
Tested up to: 6.2
Stable tag: 0.9.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin will let you manage your POEditor translations directly from Wordpress via the POEditor API.

== Description ==

This plugin will let you manage your POEditor translations directly from WordPress via the POEditor API.

It will fetch all your projects from POEditor.com, based on your API KEY, and it will scan your /wp-content/ folder for gettext language files (.po and .pot). After this, you can assign files from your WordPress themes or plugins to your POEditor language files and you can import, export or sync your files with the POEditor translations.

You can find step by step instructions on how to use the POEditor plugin in this [localization guide](http://blog.poeditor.com/how-to-translate-wordpress-themes-and-plugins-using-the-poeditor-localization-plugin/).

If you want to contribute to the localization of this plugin, please go to the [plugin's translation page](https://poeditor.com/join/project/90c87a6885fac137e7810f31a1a5296f) and join the translation effort.

You can also contribute to plugin's development on [GitHub](https://github.com/POEditor/poeditor_wp_plugin).

== Installation ==

1. Upload the contents of the zip file to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Tools > POEditor to use the plugin
4. Add the API KEY provided to you in your POEditor.com account

== Frequently asked questions ==

= Why can't the plugin write the file from the API? =

If the import gives you a writing permissions error, you should go to the path the file is located and set the proper writing permissions


== Screenshots ==

1. Main plugin page
2. Assign local file to POEditor language
