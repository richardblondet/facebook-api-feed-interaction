<?php  
/**
* Plugin de Facebook
*
* Haremos un buffer del timeline de facebook para mostrarlo
* luego en la página de Inicio y ser menos intrusivos
*
* @since 0.2
*
* @package cluster-soft
* @subpackage social-plugin
*/


/* Incluiremos los archivos necesarios */
require('vendor/Facebook/facebook/src/facebook.php');
require('vendor/Facebook/facebook.class.php');

/* Verificamos que esta clase no exista */
if(! class_exists( 'adv_facebook' )) {

	/**
	* Facebook class.
	*
	* Esta clase nos permitirá manejar el plugin de facebook
	* para clustersoft sin ser tan intrusivos en el template;
	*
	* @package cluster-soft
	*
	* @category social-plugin
	* 
	* @author Richard Blondet <richard@adventures.do> @richardblondet
	* @since 0.2.1
	*
	* @see FacebookFeed es la clase de la que depende
	* @link vendor/Facebook/facebook.class.php
	*
	*/
	class adv_facebook {

		/**
		* Usuario.
		*
		* @since 0.2.1
		* @access public
		* @var String $user El usuario de facebook
		*/
		public $user;

		/**
		* App Id.
		*
		* @since 0.2.1
		* @access public
		* @var String $facebook_app_id El identificador de nuestra aplicación
		*/
		public $facebook_app_id;

		/**
		* Facebook app secret
		*
		* @since 0.2.1
		* @access private
		* @var String $facebook_app_secret La clave secreta de la aplicación
		*/
		private $facebook_app_secret;

		/**
		* Construct nuestra clase.
		*
		* @since 0.2.1
		* @access public
		* @param $user;
		* @param $facebook_app_id;
		* @param $facebook_app_secret;
		* @param $access_token;
		* @param $access_token_secret;
		*/
		public function __construct ( $user = null, $facebook_app_id = null, $facebook_app_secret = null, $posts = 2 ) {

			/* Verificaciones */
			if ( null == $user ) throw new Exception("Facebook Error: debe proveer un usuario.", 1);
			if ( null == $facebook_app_id ) throw new Exception("Facebook Error: debe proveer un 'app id'.", 1);
			if ( null == $facebook_app_secret ) throw new Exception("Facebook Error: debe proveer un app secret.", 1);

			$this->user  				= $user;
			$this->facebook_app_id 		= $facebook_app_id;
			$this->facebook_app_secret 	= $facebook_app_secret;
			
			$this->plugin( $posts );
		}

		/**
		* El plungin que construiremos
		*
		* @since 0.2.1
		* @access public
		* @param $posts_to_load es la cantidad de posts a mostrar, por default son 2
		*/
		public function plugin( $posts_to_load ) {

			/* Facebook Feed Class */
			$facebook = new FacebookFeed(array(
			 	"facebook_app_id" 			=> $this->facebook_app_id,
				"facebook_app_secret" 		=> $this->facebook_app_secret,
				"limit" 					=> $posts_to_load,
				"user" 						=> $this->facebook_user
			));

			/* cargamos los posts */
			$facebook_data = $facebook->get_feeds();

			/* Inicializamos el buffer */
			ob_start(); ?>

			<?php /* Facebook Template */ ?>
			<div class="iconbox-icon">
				<i class="fa fa-facebook"></i>
			</div>
			<div id="facebook" class="panel panel-default">
				<div class="panel-heading">
					Facebook <span class="green-arrow"></span>
					<a href="http://facebook.com/<?php echo $this->user; ?>" target="_blank" class="btn btn-follow">
						seguir
					</a>
				</div>
				<?php if( count($facebook_data) > 0 ): ?>
				<ul class="list-group posts">
					<?php foreach($facebook_data as $post => $data): ?>
					<li class="post">
						<p>
							<!-- <span class="time-past"> 4,505 People like We Do </span> -->
						</p>
						<?php  
							/* Verificamos el tamaño longitud del posts y los limitamos */
							if ( strlen($data["message"]) > 140 ){
								$salida = "";
								$sms = explode(" ", $data["message"]);
								foreach ($sms as $v) {
									if ( strlen($salida) < 140 ){
										$salida .= $v." ";
									} else {
										break;
									}
								}
								$data["message"] = trim($salida)."...";
							}


							$text = $facebook->hash_it( $data["message"] );
							$text = $facebook->link_it( $text );
						?>
						<?php if ( '' !== $text ): ?>
							<p>
								<span class="post-text">
									<?php echo $text; ?>
								</span>
							</p>
						<?php elseif( '' !== $data['name'] ): ?>
							<span class="post-text">
								<?php echo htmlspecialchars($data['name'], ENT_QUOTES, "UTF-8"); ?> <a href="<?php echo $data['link']; ?>" class="special-underline trans-2"> <?php echo htmlspecialchars("ver aquí", ENT_QUOTES, "UTF-8"); ?></a>
							</span>
						<?php endif ?>
						<p>
							<a href="<?php echo $data['link']; ?>" target="_blank" class="post-btn">
								<i class="fa fa-reply"></i> View
							</a>
						</p>
					</li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</div>
			<?php
			/* El resultado de lo anterior lo mostramos */
			$output = ob_get_contents();
			ob_end_clean();
			echo $output;
		}
	}
}
?>