<?php
namespace Followize\Extension\GF;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

class Utils
{
	public static function get_api_fields()
	{
		return array(
			'name' => array(
				'label'    => esc_html__( 'Nome do cliente', App::PLUGIN_SLUG ),
				'required' => true,
			),
			'email' => array(
				'label'    => esc_html__( 'E-mail do cliente', App::PLUGIN_SLUG ),
				'required' => true,
			),
			'message' => array(
				'label'    => esc_html__( 'Mensagem', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'phone' => array(
				'label'    => esc_html__( 'Telefone do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'cellPhone' => array(
				'label'    => esc_html__( 'Celular do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'addressLine1' => array(
				'label'    => esc_html__( 'Endereço do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'addressLine2' => array(
				'label'    => esc_html__( 'Complemento do endereço do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'neighborhood' => array(
				'label'    => esc_html__( 'Bairro do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'city' => array(
				'label'    => esc_html__( 'Cidade do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'state' => array(
				'label'    => esc_html__( 'Estado do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'country' => array(
				'label'    => esc_html__( 'País do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'zipCode' => array(
				'label'    => esc_html__( 'CEP do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'registrationNumber' => array(
				'label'    => esc_html__( 'CPF do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'jobTitle' => array(
				'label'    => esc_html__( 'Cargo do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'companyName' => array(
				'label'    => esc_html__( 'Nome da empresa do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'companyWebsite' => array(
				'label'    => esc_html__( 'Website do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'companyRegistrationNumber' => array(
				'label'    => esc_html__( 'CNPJ da empresa do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'socialTwitter' => array(
				'label'    => esc_html__( 'Usuário no Twitter do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'socialFacebook' => array(
				'label'    => esc_html__( 'Usuário no Facebook do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'socialLinkedin' => array(
				'label'    => esc_html__( 'Usuário no LinkedIn do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'socialGoogleplus' => array(
				'label'    => esc_html__( 'Usuário no Google Plus do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'socialSkype' => array(
				'label'    => esc_html__( 'Usuário no Skype do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'socialWhatsapp' => array(
				'label'    => esc_html__( 'Número de telefone cadastrado no Whatsapp do cliente', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'emailOptIn' => array(
				'label'    => esc_html__( 'Cliente aceita ou não receber email marketing', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'productId' => array(
				'label'    => esc_html__( 'ID do produto cadastrado no Followize', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'productTitle' => array(
				'label'    => esc_html__( 'Título do produto', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'productRefer' => array(
				'label'    => esc_html__( 'ID de referência do produto cadastrado no site e/ou ERP próprio', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'categoryId' => array(
				'label'    => esc_html__( 'ID da categoria cadastrada no Followize', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'categoryTitle' => array(
				'label'    => esc_html__( 'Título da categoria', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'categoryRefer' => array(
				'label'    => esc_html__( 'ID de referência da categoria cadastrada no site e/ou ERP próprio', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'brandId' => array(
				'label'    => esc_html__( 'ID da marca cadastrada no Followize', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'brandTitle' => array(
				'label'    => esc_html__( 'Título da marca', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'brandRefer' => array(
				'label'    => esc_html__( 'ID de referência da marca cadastrada no site e/ou ERP próprio', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'locationId' => array(
				'label'    => esc_html__( 'ID da unidade cadastrada no Followize', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'locationTitle' => array(
				'label'    => esc_html__( 'Título da unidade', App::PLUGIN_SLUG ),
				'required' => false,
			),
			'locationRefer' => array(
				'label'    => esc_html__( 'ID de referência da unidade cadastrada no site e/ou ERP próprio', App::PLUGIN_SLUG ),
				'required' => false,
			),
		);
	}

	public static function get_error_string( $id )
	{
		$errors = array(
			'4000' => __( 'Um ou mais campos obrigatórios não enviados.', App::PLUGIN_SLUG ),
			'4001' => __( 'Chave de cliente inválida.', App::PLUGIN_SLUG ),
			'4002' => __( 'Chave de equipe inválida.', App::PLUGIN_SLUG ),
			'4003' => __( 'Falha ao cadastrar o contato.', App::PLUGIN_SLUG ),
			'4004' => __( 'Nenhum atendente encontrado na equipe enviada.', App::PLUGIN_SLUG ),
			'4005' => __( 'Falha ao cadastrar a conversão.', App::PLUGIN_SLUG ),
			null   => __( 'Erro não tratado.', App::PLUGIN_SLUG ),
		);

		return sprintf( '%s - %s', $id, $errors[ $id ] );
	}

	public static function array_to_string( $fields )
	{
		if ( is_array( $fields ) ) {
			return implode( ', ', array_map( __METHOD__, $fields ) );
		}

		return $fields;
	}
}