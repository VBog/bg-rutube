jQuery(document).ready(function(){
	
	// Если на странице нет Видео, не выводить
	if (!jQuery('.playlistContainer')[0]) {
		return false;
	}
	var rutube_url = 'https://rutube.ru/video/embed/';

	jQuery('.playlistContainer').each(function() {
		var uuid = jQuery(this).attr('id');
		
	// Если в списке больше одного видео, показываем список
		if (jQuery('.showRuTubeVideoLink'+uuid).length > 1) {
			
		// Следующий фильм
			jQuery('#next_movie'+uuid).click(function(){
				var movie_id = jQuery('iframe#player'+uuid).attr('src').replace(rutube_url,'');
				var el = find_movie(movie_id, uuid);
				if (el === false) return false;
				el = el.next('.showRuTubeVideoLink'+uuid);
				if (!el.length) el = jQuery('.showRuTubeVideoLink'+uuid).first();
				show_movie(el, uuid);
				return false;
			});
		// Предыдущий фильм
			jQuery('#prev_movie'+uuid).click(function(){
				var movie_id = jQuery('iframe#player'+uuid).attr('src').replace(rutube_url,'');
				var el = find_movie(movie_id, uuid);
				if (el === false) return false;
				el = el.prev('.showRuTubeVideoLink'+uuid);
				if (!el.length) el = jQuery('.showRuTubeVideoLink'+uuid).last();
				show_movie(el, uuid);
				return false;
			});
		// Выбор видео из плейлиста
			jQuery('.showRuTubeVideoLink'+uuid).click(function(){
				show_movie(jQuery(this), uuid);
				return false;
			});
		}

	// Найти элемент по индексу фильма
		function find_movie(id, uuid) {
			var el = false;
			jQuery('.showRuTubeVideoLink'+uuid).each(function() {
				this_id = jQuery(this).find('input[type="hidden"]').val();
				if (this_id == id) {
					el = jQuery(this);
					return false;
				}
			});
			return el;
		}
	// Показать фильм из списка
		function show_movie(el, uuid){
			movie_id = el.find('input[type="hidden"]').val();
			jQuery('iframe#player'+uuid).attr('src',rutube_url+movie_id);
			// Перемещаемся вверх к фрейму. Фрейм по центру экрана
			var margin = (jQuery(window).height() - jQuery('iframe#player'+uuid).height())/2;
			var scrollTop = jQuery('#'+uuid).offset().top - margin;
			if (scrollTop < 0) scrollTop = 0;
			jQuery( 'html, body' ).animate( {scrollTop : scrollTop}, 800 );
		}
	});
});

