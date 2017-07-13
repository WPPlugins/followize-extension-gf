<?php
/*
	Plugin Name: Followize Extension - Gravity Forms
	Plugin URI: https://www.followize.com.br/
	Version: 0.2.2
	Author: Followize
	Author URI: https://www.followize.com.br/
	Text Domain: followize-extension-gf
	Domain Path: /languages
	License: GPL2
	Description: Extensão do Gravity Forms para integração com o Followize. Desenvolvido para grandes, médias e pequenas empresas que recebem leads através da internet, o Followize é capaz de organizar, padronizar o processo de atendimento e analisar o desempenho da equipe comercial e ações de marketing de uma maneira objetiva, possibilitando mais produtividade e, é claro, mais lucros.
*/
namespace Followize\Extension\GF;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
	return;
}

class App
{
	const PLUGIN_SLUG = 'followize-extension-gf';
	const API_URL     = 'https://www.followize.com.br/api/v2/Leads/';
	const VERSION     = '0.2.2';

	public static function uses( $class_name, $location )
	{
		$locations = array(
			'Controller',
			'View',
			'Helper',
			'Widget',
			'Vendor',
		);

		$extension = 'php';

		if ( in_array( $location, $locations, true ) ) {
			$extension = strtolower( $location ) . '.php';
		}

		include "{$location}/{$class_name}.{$extension}";
	}

	public static function plugins_url( $path )
	{
		return plugins_url( $path, __FILE__ );
	}

	public static function plugin_dir_path( $path )
	{
		return plugin_dir_path( __FILE__ ) . $path;
	}

	public static function filemtime( $path )
	{
		return filemtime( self::plugin_dir_path( $path ) );
	}

	public static function load_textdomain()
	{
		load_plugin_textdomain( self::PLUGIN_SLUG, false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}
}

App::uses( 'core', 'Config' );

$core = new Core();

register_activation_hook( __FILE__, array( $core, 'activate' ) );
