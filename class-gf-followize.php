<?php
namespace Followize\Extension\GF;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use GFForms;
use GFFeedAddon;
use GFFormsModel;

GFForms::include_feed_addon_framework();

class GFFollowizeAddOn extends GFFeedAddon
{
	protected $_version                  = App::VERSION;
	protected $_min_gravityforms_version = '1.9';
	protected $_slug                     = 'followize-extension-gf';
	protected $_path                     = 'followize-extension-gf/followize-extension-gf.php';
	protected $_full_path                = __FILE__;
	protected $_title                    = 'Followize Extension - Gravity Forms';
	protected $_short_title              = 'Followize';

	/**
	 * Members plugin integration
	 */
	protected $_capabilities = array( 'gravityforms_followize', 'gravityforms_followize_uninstall' );

	/**
	 * Permissions
	 */
	protected $_capabilities_settings_page = 'gravityforms_followize';
	protected $_capabilities_form_settings = 'gravityforms_followize';
	protected $_capabilities_uninstall     = 'gravityforms_followize_uninstall';

	private static $_instance = null;

	public static function get_instance()
	{
		if ( self::$_instance === null ) {
			self::$_instance = new GFFollowizeAddOn();
		}

		return self::$_instance;
	}

	public function scripts()
	{
		$scripts = array(
			array(
				'handle'    => 'gf_followize',
				'src'       => $this->get_base_url() . '/assets/javascripts/gf-followize.js',
				'version'   => $this->_version,
				'deps'      => array( 'jquery' ),
				'in_footer' => true,
				'enqueue'   => array(
					'callback'  => array( $this, 'requires_script' ),
				),
			),
		);

		return array_merge( parent::scripts(), $scripts );
	}

	public function requires_script()
	{
		return ! is_admin();
	}

	public function plugin_settings_fields()
	{
		return array(
			array(
				'title'       => esc_html__( 'Chave de integração', App::PLUGIN_SLUG ),
				'description' => sprintf(
					esc_html__( 'As chaves necessárias para a integração podem ser geradas e encontradas através desta URL: %s', App::PLUGIN_SLUG ),
					'<a href="https://www.followize.com.br/app/integracao/chaves" target="_blank">https://www.followize.com.br/app/integracao/chaves</a>'
				),
				'fields' => array(
					array(
						'name'       => 'clientKey',
						'tooltip'    => esc_html__( 'Configure aqui a chave "Cliente".', App::PLUGIN_SLUG ),
						'label'      => esc_html__( 'Cliente', App::PLUGIN_SLUG ),
						'type'       => 'text',
						'input_type' => 'password',
						'class'      => 'medium',
					),
				),
			),
		);
	}

	public function can_create_feed()
	{
		return rgar( $this->get_plugin_settings(), 'clientKey' );
	}

	public function feed_list_columns()
	{
		return array(
			'feedName' => esc_html__( 'Nome', App::PLUGIN_SLUG ),
			'teamKey'  => esc_html__( 'Chave da Equipe', App::PLUGIN_SLUG ),
		);
	}

	public function feed_settings_fields()
	{
		return array(
			array(
				'title'  => esc_html__( 'Followize Feed Configurações', App::PLUGIN_SLUG ),
				'fields' => array(
					array(
						'name'          => 'feedName',
						'label'         => esc_html__( 'Nome', App::PLUGIN_SLUG ),
						'type'          => 'text',
						'required'      => true,
						'default_value' => $this->get_default_feed_name(),
						'tooltip'       => '<h6>'. esc_html__( 'Nome', App::PLUGIN_SLUG ) .'</h6>' . esc_html__( 'Entre com um nome único para identificar este feed.', App::PLUGIN_SLUG ),
					),
					array(
						'name'     => 'teamKey',
						'label'    => esc_html__( 'Chave da Equipe', App::PLUGIN_SLUG ),
						'type'     => 'text',
						'required' => true,
						'class'    => 'medium',
						'tooltip'  => '<h6>'. esc_html__( 'Chave da Equipe', App::PLUGIN_SLUG ) .'</h6>' . esc_html__( 'Ex.: Vendas: abbf8070add9022944a0be8ee55ce1a2', App::PLUGIN_SLUG ),
					),
					array(
						'name'          => 'conversionGoal',
						'label'         => esc_html__( 'Ponto de conversão', App::PLUGIN_SLUG ),
						'type'          => 'text',
						'required'      => true,
						'class'         => 'medium',
						'default_value' => 'API v2.0',
						'tooltip'       => '<h6>'. esc_html__( 'Ponto de conversão', App::PLUGIN_SLUG ) .'</h6>' . esc_html__( 'Ex.: Página de produtos', App::PLUGIN_SLUG ),
					),
					array(
						'name'      => 'mappedFields',
						'label'     => esc_html__( 'Campos mapeados', App::PLUGIN_SLUG ),
						'type'      => 'field_map',
						'field_map' => $this->_merge_vars_field_map(),
						'tooltip'   => '<h6>' . esc_html__( 'Campos mapeados', App::PLUGIN_SLUG ) . '</h6>' . esc_html__( 'Associe os campos da Followize com os campos apropriados do seu formulário.', App::PLUGIN_SLUG ),
					),
					array(
						'name'      => 'mappedCustomFields',
						'label'     => esc_html__( 'Campos personalizados', App::PLUGIN_SLUG ),
						'type'      => 'dynamic_field_map',
						'tooltip'   => '<h6>' . esc_html__( 'Campos personalizados', App::PLUGIN_SLUG ) . '</h6>' . esc_html__( 'Crie cada campo personalizado com um título para identifica-lo, então associe com campos apropriados do seu formulário.', App::PLUGIN_SLUG ),
					),
					array(
						'name'    => 'optinCondition',
						'label'   => esc_html__( 'Condicional lógica', App::PLUGIN_SLUG ),
						'type'    => 'feed_condition',
						'tooltip' => '<h6>' . esc_html__( 'Condicional lógica', App::PLUGIN_SLUG ) . '</h6>' . esc_html__( 'Quando a condicional logica está ativada, o lead só será capturado quando as condições forem atendidas.', App::PLUGIN_SLUG ),
					),
				),
			),

		);
	}

	public function process_feed( $feed, $entry, $form )
	{
		$api = $this->get_api_credentials( $this->get_plugin_settings(), $feed );

		if ( ! $api['clientKey'] ) {
			return;
		}

		$fields   = $this->_get_posted_fields( $feed, $entry, $form );
		$response = wp_remote_post(
			App::API_URL,
			array(
				'body'      => json_encode( $api + $fields ),
				'sslverify' => false,
			)
		);

		if ( is_wp_error( $response ) ) {
			$this->log_error( __METHOD__ . __( '(): a requisição falhou!', App::PLUGIN_SLUG ) );
			return;
		}

		$response = json_decode( $response['body'], true );

		if ( ! @$response['success'] ) {
			$this->log_error(
				sprintf(
					__( '%s (): Fields => %s, erro: %s', App::PLUGIN_SLUG ),
					__METHOD__,
					json_encode( $fields ),
					Utils::get_error_string( @$response['error'] )
				)
			);
		}
	}

	public function get_api_credentials( $settings, $feed )
	{
		$api = wp_parse_args(
			$this->get_plugin_settings(),
			array(
				'clientKey'      => '',
				'teamKey'        => $feed['meta']['teamKey'],
				'conversionGoal' => $feed['meta']['conversionGoal'],
			)
		);

		$api['teamKey'] = preg_replace( '/.+:\s?/', '', $api['teamKey'] );

		return $api;
	}

	public function get_field_label( $form, $field_id )
	{
		switch ( $field_id ) {

			case 'form_title' :
				return esc_html__( 'Form Title', 'gravityforms' );

			case 'date_created' :
				return esc_html__( 'Entry Date', 'gravityforms' );

			case 'ip' :
				return esc_html__( 'User IP', 'gravityforms' );

			case 'source_url' :
				return esc_html__( 'Source Url', 'gravityforms' );

			case 'id' :
				return esc_html__( 'Entry ID', 'gravityforms' );

			default :
				$field = GFFormsModel::get_field( $form, $field_id );
				return GFFormsModel::get_label( $field );

		}
	}

	private function _get_posted_fields( $feed, $entry, $form )
	{
		$fields_map        = $this->get_field_map_fields( $feed, 'mappedFields' );
		$custom_fields_map = $this->get_dynamic_field_map_fields( $feed, 'mappedCustomFields' );

		$fields        = $this->_get_fields( $fields_map, $form, $entry );
		$custom_fields = $this->_get_custom_fields( $custom_fields_map, $form, $entry );
		$hidden        = $this->_get_hidden_fields();

		return $this->_merge_fields( $fields, $custom_fields, $hidden );
	}

	private function _get_fields( $fields_map, $form, $entry )
	{
		$fields = array();

		if ( ! $this->is_valid_array( $fields_map ) ) {
			return $fields;
		}

		foreach ( $fields_map as $key => $value ) {
			$fields[ $key ] = Utils::array_to_string( $this->get_field_value( $form, $entry, $value ) );
		}

		return array_filter( $fields );
	}

	private function _get_custom_fields( $custom_fields_map, $form, $entry )
	{
		$fields = array();

		if ( ! $this->is_valid_array( $custom_fields_map ) ) {
			return $fields;
		}

		foreach ( $custom_fields_map as $key => $value ) {
			$fields[ $key ] = Utils::array_to_string( $this->get_field_value( $form, $entry, $value ) );
		}

		return array_filter( $fields );
	}

	private function _get_hidden_fields()
	{
		return array( 'hubUtmz' => Utils::array_to_string( @$_POST['hubUtmz'] ) );
	}

	private function _merge_fields( $fields, $custom_fields, $hidden )
	{
		$fields = wp_parse_args(
			$fields,
			array( 'message' => '' )
		);

		if ( $custom_fields ) {
			$fields['message'] .= __( ' | Campos customizados: ', App::PLUGIN_SLUG ) . $this->_format_custom_fields( $custom_fields );
		}

		return ( $fields + $hidden );
	}

	private function _format_custom_fields( $custom_fields )
	{
		$str = '';

		foreach ( $custom_fields as $key => $value ) {
			$str .= sprintf( '%s: %s, ', $key, Utils::array_to_string( $value ) );
		}

		return rtrim( $str, ', ' );
	}

	private function _merge_vars_field_map()
	{
		foreach ( Utils::get_api_fields() as $key => $value )
		{
			$list[] = array(
				'name'     => $key,
				'label'    => $value['label'],
				'required' => $value['required'],
			);
		}

		return $list;
	}

	public function is_valid_array( $array ) {
		return ( is_array( $array ) && ! empty( $array ) );
	}
}
