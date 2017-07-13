<?php
namespace Followize\Extension\GF;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use GFAddOn;

App::uses( 'utils', 'Helper' );
require_once __DIR__ . '/../class-gf-followize.php';

class Core
{
	public function __construct()
	{
		add_action( 'plugins_loaded', array( __NAMESPACE__ . '\App', 'load_textdomain' ) );
		add_action( 'wp_head', array( &$this, 'enqueue_tracker_code' ) );
		add_action( 'gform_loaded', array( &$this, 'load' ), 5 );
	}

	public function load()
	{
		GFAddOn::register( __NAMESPACE__ . '\GFFollowizeAddOn' );
	}

	public function activate()
	{

	}

	public function enqueue_admin_scripts()
	{
		wp_enqueue_script(
			'admin-script-' . App::PLUGIN_SLUG,
			App::plugins_url( '/assets/javascripts/built.js' ),
			array( 'jquery' ),
			App::filemtime( 'assets/javascripts/built.js' ),
			true
		);
	}

	public function styles_admin()
	{
		wp_enqueue_style(
			'admin-style-' . App::PLUGIN_SLUG,
			App::plugins_url( 'assets/stylesheets/style.css' ),
			array( 'admin-css-apiki-wp-api' ),
			App::filemtime( 'assets/stylesheets/style.css' )
		);
	}

	public function enqueue_tracker_code()
	{
		?>
		<!-- FOLLOWIZE :: START TRACKER -->
		<script>
			(function() {
				var hub = document.createElement('script'); hub.type = 'text/javascript'; hub.async = true;
				hub.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'www.followize.com.br/api/utmz.min.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(hub, s);
			})();
			window.onload=function(){function t(t){for(var n=t+"=",u=document.cookie.split(";"),e=0;e<u.length;e++){for(var i=u[e];" "==i.charAt(0);)i=i.substring(1,i.length);if(0==i.indexOf(n))return i.substring(n.length,i.length)}return null}try{for(hubUtmz =document.getElementsByName("hubUtmz"),i=0;i< hubUtmz.length;i++) hubUtmz[i].value=t("hub_utmz")}catch(n){}};
		</script>
		<!-- FOLLOWIZE :: END TRACKER -->
		<?php
	}
}
