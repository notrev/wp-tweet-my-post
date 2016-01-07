<?php
/**
 * TwitterPublisher
 *
 * @package   TwitterPublisher
 * @author    Éverton Arruda <root@earruda.eti.br>
 * @license   GPL-2.0+
 * @link      http://earruda.eti.br
 * @copyright 2014 Éverton Arruda
 */

$file_path = plugin_dir_path( realpath( dirname( __FILE__ ) ) );

require_once( $file_path . 'lib/j7mbo/twitter-api-php/TwitterAPIExchange.php' );

/**
 * TwitterPublisher class. Handles Twitter API authentication and message
 * tweeting.
 *
 * @package TwitterPublisher
 * @author  Éverton Arruda <root@earruda.eti.br>
 */
class TwitterPublisher
{
	/**
	 * TwitterAPIExchange object
	 *
	 * @since   1.0.0
	 * @var     object
	 */
	private $twitter = null;

	/**
	 * Twitter API settings
	 *
	 * @since   1.0.0
	 * @var     array
	 */
	private $settings = array();

	/**
	 * Twitter API URL for publishing tweets
	 *
	 * @since   1.0.0
	 * @var     string
	 */
	private $apiUrl = 'https://api.twitter.com/1.1/statuses/update.json';

	/**
	 * Request method for the Twitter API
	 *
	 * @since   1.0.0
	 * @var     string
	 */
	private $requestMethod = 'post';

	/**
	 * Instantiates a TwitterAPIExchange object and connects to Twitter
	 *
	 * Instatiates a TwitterAPIExchange object if there are twitter connection
	 * settings. If there are no settings defined, raises an exception:
	 * 'No Twitter connection settings'.
	 *
	 * @since   1.0.0
	 */
	private function connect_to_twitter() {
		if ( ! empty( $this->settings ) ) {
			$this->twitter = new TwitterAPIExchange( $this->settings );
		} else {
			throw new Exception('No Twitter connection settings.');
		}
	}

	/**
	 * Sets the twitter app settings array.
	 *
	 * @since   1.0.0
	 * @param   array   $settings   Twitter API settings.
	 */
	public function set_settings( $settings = array() ) {
		$this->settings = $settings;
	}

	/**
	 * Publishes a message.
	 *
	 * @since   1.0.0
	 * @param   string  $message    Message to be published.
	 * @return  object
	 */
	public function publish( $message ) {
		$postFields = array(
			'status' => $message
		);

		$this->connect_to_twitter();

		$this->twitter->buildOauth( $this->apiUrl, $this->requestMethod );
		$this->twitter->setPostFields( $postFields );
		$response = $this->twitter->performRequest();

		return $response;
	}
}

?>
