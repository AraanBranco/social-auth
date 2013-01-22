<?php
/**
 * Facebook Authenticate
 *
 * @link          http://github.com/TiuTalk/social-auth
 * @license       CC BY 3.0 (http://creativecommons.org/licenses/by/3.0/)
 */

App::uses('BaseAuthenticate', 'Controller/Component/Auth');
App::import('Vendor', 'Facebook', array(
	'file' => 'facebook-php-sdk' . DS . 'src' . DS . 'facebook.php')
);


/**
 * Facebook Authenticate
 *
 * @author        Thiago Belem <contato@thiagobelem.net>
 * @package       SocialAuth.Controller.Component.Auth
 */
class FacebookAuthenticate extends BaseAuthenticate {

/**
 * Controller object
 *
 * @var object
 */
	protected $_Controller;

/**
 * Settings for this object
 *
 * @var array
 */
	public $settings = array(
		'userModel' => 'User',
		'fields' => array(
			'name' => 'name',
			'email' => 'email',
			'username' => 'username',
			'password' => 'password',
		),
		'scope' => array(),
		'recursive' => 0,
		'contain' => null,

		'facebook' => array(
			'appId' => null,
			'secret' => null,
			'scope' => array('email')
		)
	);

/**
 * Constructor
 *
 * @param ComponentCollection $collection The Component collection used on this request.
 * @param array $settings Array of settings to use.
 */
	public function __construct(ComponentCollection $collection, $settings) {
		$this->_Collection = $collection;
		$this->_Controller = $collection->getController();

		$this->settings = Hash::merge($this->settings, $settings);

		$this->Facebook = new Facebook(array(
			'appId' => $this->settings['facebook']['appId'],
			'secret' => $this->settings['facebook']['secret'],
		));
	}

/**
 * Authenticates the identity  using Facebook PHP SDK
 *
 * @param CakeRequest $request The request
 * @param CakeResponse $response Unused response object
 *
 * @return mixed.  False on login failure.  An array of User data on success.
 */
	public function authenticate(CakeRequest $request, CakeResponse $response) {
		if (!$this->_isFacebookLogin($request))
			return false;

		$user = $this->getUser($request);

		if (!$user) {
			$loginUrl = $this->Facebook->getLoginUrl(array(
				'scope' => join(',', $this->settings['facebook']['scope'])
			));

			$this->_Controller->redirect($loginUrl);
		} else {
			return $this->_findUser();
		}

		return false;
	}

/**
 * Get the current user from Facebook
 */
	public function getUser($request) {
		return $this->Facebook->getUser();
	}

/**
 * Retrieve the user data from facebook
 *
 * @param string $username Do not use
 * @param string $password Do not use
 *
 * @return Mixed Either false on failure, or an array of user data
 */
	protected function _findUser($username = null, $password = null) {
		try {
			$fields = Hash::remove($this->settings['fields'], 'password');

			return $this->Facebook->api('/me', array(
				'fields' => join(',', array_unique($fields))
			));
		} catch (FacebookApiException $e) {
			CakeLog::write('error', $e);
		}

		return false;
	}

/**
 * Check if the user is trying to login with Facebook
 *
 * @param CakeRequest $request The request
 *
 * @return boolean
 */
	protected function _isFacebookLogin(CakeRequest $request) {
		if ($this->_isFacebookLoginRequest($request))
			return true;

		if ($this->_isFacebookLoginCallback($request))
			return true;

		return false;
	}

/**
 * Check if the user is trying to login with Facebook
 *
 * @param CakeRequest $request The request
 *
 * @return boolean
 */
	private function __isFacebookLoginRequest(CakeRequest $request) {
		if (!$request->is('post'))
			return false;

		$model = $this->settings['userModel'];

		return isset($request->data[$model]['provider']) &&
			$request->data[$model]['provider'] == 'Facebook';
	}

/**
 * Check if the request is the callback from Facebook
 *
 * @param CakeRequest $request The request
 *
 * @return boolean
 */
	private function __isFacebookLoginCallback(CakeRequest $request) {
		if (!$request->is('get'))
			return false;

		return isset($request->query['state'], $request->query['code']);
	}

}