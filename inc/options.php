<?php
/**
 * Страница настроек плагина
 */
add_action('admin_menu', 'add_bg_rutube_options_page');
function add_bg_rutube_options_page(){
	add_options_page( 'Настройки Bg RuTube Embed', 'Bg RuTube Embed', 'manage_options', 'bg_rutube_slug', 'bg_rutube_options_page_output' );
}

function bg_rutube_options_page_output(){
	?>
	<div class="wrap">
		<h2><?php echo get_admin_page_title() ?></h2>

		<form action="options.php" method="POST">
			<?php
				settings_fields( 'bg_rutube_option_group' );     // скрытые защитные поля
				do_settings_sections( 'bg_rutube_page' ); // секции с настройками (опциями). У нас она всего одна 'section_id'
				submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Регистрируем настройки.
 * Настройки будут храниться в массиве, а не одна настройка = одна опция.
 */
add_action('admin_init', 'bg_rutube_plugin_settings');
function bg_rutube_plugin_settings(){
	// параметры: $option_group, $bg_rutube_options, $bg_rutube_sanitize_callback
	register_setting( 'bg_rutube_option_group', 'bg_rutube_options', 'bg_rutube_sanitize_callback' );

	// параметры: $id, $title, $callback, $page
	add_settings_section( 'bg_rutube_section_id', '', '', 'bg_rutube_page' );

	// параметры: $id, $title, $callback, $page, $section, $args
	add_settings_field('bg_rutube_field1', 'Видео только на страницах записей', 'fill_bg_rutube_field1', 'bg_rutube_page', 'bg_rutube_section_id' );
	add_settings_field('bg_rutube_field3', 'Вкл. кеширование запросов к API RuTube', 'fill_bg_rutube_field3', 'bg_rutube_page', 'bg_rutube_section_id' );
}

## Заполняем опцию 1
function fill_bg_rutube_field1(){
	$val = get_option('bg_rutube_options');
	$singular = $val ? $val['singular'] : null;
	?>
	<label><input type="checkbox" id="singular" name="bg_rutube_options[singular]" value="1" <?php checked( 1, $singular ); ?> />  (пост, страница, свой тип записи, вложение)</label>
	<?php
}

## Заполняем опцию 3
function fill_bg_rutube_field3(){
	$val = get_option('bg_rutube_options');
	$transient = $val ? $val['transient'] : null;
	?>
	<label><input type="checkbox" id="transient" name="bg_rutube_options[transient]" value="1" <?php checked( 1, $transient ); ?> /> (ускоряет открытие страницы, время жизни кэш 1 час)</label>
	<?php
}

## Очистка данных
function bg_rutube_sanitize_callback( $options ){
	// очищаем
	foreach( $options as $name => & $val ){
		$val = intval( $val );
	}
	return $options;
}