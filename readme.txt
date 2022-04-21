=== Bg RuTube Embed ===

Contributors: VBog
Donate link: http://bogaiskov.ru/about-me/donate/
Tags: video, playlist, channel, rutube, videohosting
Requires PHP: 5.3
Requires at least: 3.0.1
Tested up to: 5.9.3
Stable tag: 1.2
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html


The plugin is the easiest way to embed RuTube videos in WordPress.

== Description ==

Plugin allowed to embed [RuTube](https://rutube.ru/) videos in WordPress.  Just specify the **uuid** of the playlist or video in the shortcode. You can also specify a list of **uuid** separated by commas.

`[rutube id="{uuid}" title="" description="" sort=""]`

*	`id` - **uuid** of the playlist or video, or list of videos **uuid** separated by commas;
*	`title` - playlist title (for list of **uuid** only);
*	`description` - playlist description (for list of **uuid** only);
*	`sort="on"`- sort playlist by ABC (default: `sort=""` - don't sort).

To embed one video you could just the URL (https://rutube.ru/video/{**uuid**}/) on its own line.

== Installation ==

1. Upload 'bg-rutube' directory to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

Ask me. I'll answer. :)

== Screenshots ==

1. Playlist embedded on the post page.
2. Info about the video on the archive/tag/category page (Enabled "Video only on post pages" option).
3. Plugin settings screen.


== Changelog ==

= 1.2.1 =

* Fixed small bugs.

= 1.2 =

* Added the ability to localize the plugin.
* You could just the URL on its own line to embed one video.
* Fixed some bugs and mistakes.

= 1.1 =

* Added the ability to embed a RuTube playlist or create a playlist from several videos.

= 1.0 =

* Starting version

== License ==

GNU General Public License v2

