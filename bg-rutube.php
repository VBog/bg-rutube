<?php
/* 
    Plugin Name: Bg RuTube Embed
    Plugin URI: http://bogaiskov.ru
    Description: The plugin is the easiest way to embed RuTube videos in WordPress.
    Version: 1.1
    Author: VBog
    Author URI: https://bogaiskov.ru 
	License:     GPL2
*/

/*  Copyright 2022  Vadim Bogaiskov  (email: vadim.bogaiskov@yandex.ru)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*****************************************************************************************

	Блок загрузки плагина
	
******************************************************************************************/

// Запрет прямого запуска скрипта
if ( !defined('ABSPATH') ) {
	die( 'Sorry, you are not allowed to access this page directly.' ); 
}
define('BG_RUTUBE_VERSION', '1.1');

// Подключаем CSS и JS 
function bg_rutube_scripts() {
	wp_enqueue_style( 'bg_rutube_styles', plugins_url( '/css/bg_rutube.css', plugin_basename(__FILE__) ), array() , BG_RUTUBE_VERSION );
	wp_enqueue_script( 'bg_rutube_proc', plugins_url( '/js/bg_rutube.js', __FILE__ ), ['jquery'], BG_RUTUBE_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'bg_rutube_scripts' );

include_once( 'inc/options.php' );

/*****************************************************************************************

	Регистрируем шорт-код [rutube id="{uuid}" sort=""]
		id - ID плейлиста или фильма, или список ID фильмов через запятую
		sort='on' - сортировать плейлист по алфавиту
		
******************************************************************************************/
add_shortcode( 'rutube', 'rutube_player_sortcode' );

function rutube_player_sortcode( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'title' => '',
		'description' => '',
		'sort' => ''
	), $atts ) );

	// Формируем список фильмов с RuTube из шорткода
	if (empty($id)) return "";											// Фильм не указан
	elseif (strlen($id) < 32) $playlist = get_rutube_playlist ($id);	// Плейлист RuTube
	else $playlist = create_rutube_playlist ($id, $title, $description);// Фильм или список фильмов
	
	if(!$playlist || empty($playlist)) 
		return "<div class='tube-error'><p class='warning'>Простите, видеораздел временно не работает.</p></div>";

	// Сортировка плейлиста по алфавиту
	if ($sort) {
		usort ($playlist, function($a, $b) {
			 return strcmp($a["title"], $b["title"]);
		});
	}
	
	return rutube_playlist_show ( $playlist );
	    
}
/*****************************************************************************************

	Получить информацию о плейлисте RuTybe 
		
******************************************************************************************/
function get_rutube_playlist_info ( $playlist_id ) {
	$info = array();
	
	$val = get_option('bg_rutube_options');
	$transient = $val ? $val['transient'] : false;
	
	$key='rutube_playlist_info_'.$playlist_id;				// Проверяем обновления на RuTube раз в час
	if(false===($json=get_transient($key)) || !$transient) {
		$url = 'http://rutube.ru/api/playlist/custom/'.$playlist_id.'/';
		$result = wp_remote_get ($url,
			[
				'timeout' => 5,
				'httpversion' => '1.1',
				'user-agent'  => 'Mozilla/5.0 (compatible; Rigor/1.0.0; http://rigor.com)',
			]
		);
		if( is_wp_error( $result ) ) {
			error_log('Playlist info reading error from '.$playlist_id.' : ('.$result->get_error_code().') '.$result->get_error_message() , 0);
		} elseif( ($errorcode = wp_remote_retrieve_response_code( $result )) === 200 ) {
			$json = wp_remote_retrieve_body($result);
			set_transient( $key, $json, HOUR_IN_SECONDS );
		} else {
			error_log($url.'<br>'.'Playlist info. Error: '.$errorcode.' - '.wp_remote_retrieve_response_message( $result ) , 0);
		}
	}
	$info = json_decode($json, true);
	
	return $info;
}


/*****************************************************************************************

	Получить плейлист с RuTybe 
		
******************************************************************************************/
function get_rutube_playlist ( $playlist_id ) {
	$playlist = array();
	$tracks = array();
	
	$page = 1;			// номер страницы, с которой начинается закачка
	$has_next = true;
	$val = get_option('bg_rutube_options');
	$transient = $val ? $val['transient'] : false;
	
	while($has_next) {
		$key='rutube_playlist_'.$playlist_id.'_'.$page;	// Проверяем обновления на RuTube раз в час
		if(false===($json=get_transient($key)) || !$transient) {
			$url = 'http://rutube.ru/api/playlist/custom/'.$playlist_id.'/videos/?page='.$page;
			$result = wp_remote_get ($url,
				[
					'timeout' => 5,
					'httpversion' => '1.1',
					'user-agent'  => 'Mozilla/5.0 (compatible; Rigor/1.0.0; http://rigor.com)',
				]
			);
			if( is_wp_error( $result ) ) {
				error_log('Playlist data reading error from '.$playlist_id.' : ('.$result->get_error_code().') '.$result->get_error_message() , 0);
				break;
			} elseif( ($errorcode = wp_remote_retrieve_response_code( $result )) === 200 ) {
				$json = wp_remote_retrieve_body($result);
				set_transient( $key, $json, HOUR_IN_SECONDS );
			} else {
				error_log($url.'<br>'.'Playlist. Error: '.$errorcode.' - '.wp_remote_retrieve_response_message( $result ) , 0);
				break;
			}
		}
		$videos = json_decode($json, true);

		$page = $videos['page'];
		$has_next = $videos['has_next'];
		foreach($videos['results'] as $videoData) {
			$track['uuid'] = $videoData['id'];
			$track['url'] = "https://rutube.ru/video/embed/".$videoData['id']."/";
			$track['length'] = $videoData['duration'];
			$track['artist'] = "";
			$track['title'] = $videoData['title'];
			$track['description'] = $videoData['description'];
			$track['thumbnail'] = $videoData['thumbnail_url'];
			$tracks[] = $track;
		}
		$page++;
	}
	if (!empty($tracks)) {
		$info = get_rutube_playlist_info ( $playlist_id );
		$playlist['title'] = $info['title'];
		$playlist['description'] = $info['description'];
		$playlist['thumbnail'] = $info['thumbnail_url'];
		$playlist['count'] = $info['videos_count'];;
		$playlist['tracks'] = $tracks;
		
		if (!$playlist['description']) $playlist['description'] = $playlist['title'];
	}
	return $playlist;
}

/*****************************************************************************************

	Формируем плейлист на основе списка треков RuTube 
	
******************************************************************************************/
function create_rutube_playlist ($ids, $title='', $description='') {
	$playlist = array();
	$tracks = array();
	
	$videoList = explode ( ',' , $ids );
	$val = get_option('bg_rutube_options');
	$transient = $val ? $val['transient'] : false;
	
	foreach ($videoList as $videoID) {
		$videoID = strip_tags($videoID);
		$videoID = trim($videoID);
		$key='rutube_'.$videoID;	// Проверяем обновления на RuTube раз в час
		if(false===($json=get_transient($key)) || !$transient) {
			$url = 'https://rutube.ru/api/video/'.$videoID;
			$result = wp_remote_get ($url,
				[
					'timeout' => 5,
					'httpversion' => '1.1',
					'user-agent'  => 'Mozilla/5.0 (compatible; Rigor/1.0.0; http://rigor.com)',
				]
			);
			$json = '';
			if( is_wp_error( $result ) ) {
				error_log('Metadata reading error from '.$videoID.' : ('.$result->get_error_code().') '.$result->get_error_message() , 0);
			} elseif( ($errorcode = wp_remote_retrieve_response_code( $result )) === 200 ) {
				$json = wp_remote_retrieve_body($result);
				set_transient( $key, $json, HOUR_IN_SECONDS );
			} else {
				error_log($url.'<br>'.'Metadata. Error: '.$errorcode.' - '.wp_remote_retrieve_response_message( $result ) , 0);
			}
		}
		$videoData = json_decode($json, true);
		
		if ($videoData) {
			$track['uuid'] = $videoID;
			$track['url'] = "https://rutube.ru/video/embed/".$videoID."/";
			$track['length'] = $videoData['duration'];
			$track['artist'] = "";
			$track['title'] = $videoData['title'];
			$track['description'] = $videoData['description'];
			$track['thumbnail'] = $videoData['thumbnail_url'];
			$tracks[] = $track;
		} else {
			error_log("Metadata. Empty or error answer from RuTube: ". $json." for ".$videoID, 0); 
		}
	}
	if (!empty($tracks)) {
		$playlist['thumbnail'] = $tracks[0]['thumbnail'];
		$playlist['tracks'] = $tracks;
		$playlist['count'] = count($tracks);
		
		if ($playlist['count'] == 1) {
			$playlist['title'] = trim($tracks[0]['title']);
			$playlist['description'] = trim($tracks[0]['description']);
			$playlist['count'] = "";
		} else {
			$playlist['title'] = $title;
			$playlist['description'] = $description;
		}
		
		if (!$playlist['description']) $playlist['description'] = $playlist['title'];
	}
	return $playlist;
}

/*****************************************************************************************

	Отображение плейлиста, используя RuTube Embed API
		
******************************************************************************************/
function rutube_playlist_show ( $playlist) {
	// Выводим на экран
	if (empty($playlist)) return "";
	$val = get_option('bg_rutube_options');
	$singular = $val ? $val['singular'] : false;

	ob_start();	
	if (is_singular() || !$singular) {
		$uuid = '_'. random_int(1000, 9999);
	
?>
<div id="<?php echo $uuid; ?>" class="playlistContainer">
	<div class="centerVideo">
		<div id="video_container<?php echo $uuid; ?>" class="video_container">
			<iframe id="player<?php echo $uuid; ?>" class="rutube-video" src="https://rutube.ru/video/embed/<?php echo $playlist['tracks'][0]['uuid']; ?>" allowfullscreen frameborder="0"></iframe>
		</div>
	</div>
	
<?php if ($playlist['count'] > 1) : ?>
	
	<table id="nav<?php echo $uuid; ?>" class="nav_movies">
		<tr>
			<td id="prev_movie<?php echo $uuid; ?>" align="left">
				<span class="nav_button">&#9204; Предыдущий<span>
			</td>
			<td id="next_movie<?php echo $uuid; ?>" align="right">
				<span class="nav_button">Следующий &#9205;</span>
			</td>
		</tr>
	</table>

	<table class="videoPlayListTable">
	<?php foreach ($playlist['tracks'] as $track): ?>
		<tr class="showRuTubeVideoLink<?php echo $uuid; ?>" title="Воспроизвести: <?php echo $track['title'];?>">
			<td style="background-image: url('<?php echo $track['thumbnail'];?>');">
				<input type="hidden" value="<?php echo $track['uuid'];?>" />
			</td>
			<td>
				<span class='track_title'><?php echo $track['title'];?></span> 
			</td>
			<td align="right">
				<span class='track_length'><?php echo bg_videolist_sectotime ($track['length']);?></span> 
			</td>
		</tr>
	<?php endforeach; ?>
	</table>

<?php endif; ?>
	
</div>
<?php
		
	} else {
?>
	<table class="videoPlayListInfo">
		<tr class="showRuTubeVideoInfo" title="<?php echo $playlist['description'];?>">
			<td style="background-image: url('<?php echo $playlist['thumbnail'];?>');"></td>
			<td>
				<span class='playlist_title'><?php echo $playlist['title'];?></span>
			<?php if ($playlist['count']) { ?>
					<span class='playlist_count'> (<?php echo $playlist['count'];?>)</span> 
			<?php } ?>
			</td>
		</tr>
	</table>
<?php			
	}

	return ob_get_clean();
}

/*****************************************************************************************

	Переводит секунды в часы, минуты, секунды

******************************************************************************************/
function bg_videolist_sectotime ($seconds) {
	$seconds = (int)$seconds;
	if ($seconds < 0) return "";
	$minutes = floor($seconds / 60);		// Считаем минуты
	$hours = floor($minutes / 60); 			// Считаем количество полных часов
	$minutes = $minutes - ($hours * 60);	// Считаем количество оставшихся минут
	$seconds = $seconds - ($minutes + ($hours * 60))*60;// Считаем количество оставшихся секунд
	return  ($hours?($hours.":"):"").sprintf("%02d:%02d", $minutes, $seconds);
}


