# Bg RuTube Embed 

Contributors: VBog

Donate link: http://bogaiskov.ru/about-me/donate/

Tags: video, playlist, channel, rutube, videohosting

Requires PHP: 5.3

Requires at least: 3.0.1

Tested up to: 5.9.3

Stable tag: trunk

License: GPLv2

License URI: http://www.gnu.org/licenses/gpl-2.0.html


The plugin is the easiest way to embed RuTube videos in WordPress.

## Description

Плагин позволяет вставить видео с [RuTube](https://rutube.ru/) в WordPress. Для этого достаточно указать **uuid** плейлиста или видео в шорткоде. Можно также указать список **uuid** через запятую.

`[rutube id="{uuid}" title="" description="" sort=""]`

*	`id` - **uuid** плейлиста или фильма, или список **uuid** фильмов через запятую;
*	`title` - название плейлиста (только для списка **uuid**);
*	`description` - описание плейлиста (только для списка **uuid**);
*	`sort="on"`- сортировать плейлист по алфавиту (по умолчанию `sort=""` - не сортировать).

Для встраивания одного видео достаточно указать URL (https://rutube.ru/video/{**uuid**}/) на отдельной строке.

## Installation

1. Upload 'bg-rutube' directory to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

## Frequently Asked Questions

Спрашивайте. Ответим. :)

## Screenshots

![Плейлист на странице записи](http://bogaiskov.ru/test/wp-content/plugins/bg-rutube/images/screenshot-1.jpg "1. Плейлист на странице записи.")

![Информация о видео на странице архива](http://bogaiskov.ru/test/wp-content/plugins/bg-rutube/images/screenshot-2.jpg "2. Информация о видео на странице архива/метки/рубрики (Включена опция Видео только на страницах записей).")

![Экран настроек плагина](http://bogaiskov.ru/test/wp-content/plugins/bg-rutube/images/screenshot-3.jpg "3. Экран настроек плагина.")


## Changelog

### 1.2.1

* Fixed small bugs.

### 1.2

* Added the ability to localize the plugin.
* You could just the URL on its own line to embed one video.
* Fixed some bugs and mistakes.

### 1.1

* Added the ability to embed a RuTube playlist or create a playlist from several videos.

### 1.0

* Starting version

## License

GNU General Public License v2

