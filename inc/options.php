<?php
/**
 * Страница настроек плагина
 */
add_action('admin_menu', 'add_bg_rutube_options_page');
function add_bg_rutube_options_page(){
	add_options_page( __('Bg RuTube Embed Settings', 'bg_rutube'), 'Bg RuTube Embed', 'manage_options', 'bg_rutube_slug', 'bg_rutube_options_page_output' );
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
	add_settings_field('bg_rutube_field1', __('Videos on post pages only', 'bg_rutube'), 'fill_bg_rutube_field1', 'bg_rutube_page', 'bg_rutube_section_id' );
	add_settings_field('bg_rutube_field3', __('Caching response of the RuTube API', 'bg_rutube'), 'fill_bg_rutube_field3', 'bg_rutube_page', 'bg_rutube_section_id' );
}

## Заполняем опцию 1
function fill_bg_rutube_field1(){
	$val = get_option('bg_rutube_options');
	$singular = $val ? $val['singular'] : null;
	?>
	<label><input type="checkbox" id="singular" name="bg_rutube_options[singular]" value="1" <?php checked( 1, $singular ); ?> /> <?php _e( '(post, page, custom post type, attachment)', 'bg_rutube'); ?></label>
	<?php
}

## Заполняем опцию 3
function fill_bg_rutube_field3(){
	$val = get_option('bg_rutube_options');
	$transient = $val ? $val['transient'] : null;
	?>
	<label><input type="checkbox" id="transient" name="bg_rutube_options[transient]" value="1" <?php checked( 1, $transient ); ?> /> <?php _e( '(speeds up page opening, cache lifetime is 1 hour)', 'bg_rutube'); ?></label>
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