# CakePHP Social Auth

With this plugin your uses can login via Facebook, Twitter or Facebook while
using the default `AuthComponent`.

While using this plugin you can keep the default login via `AuthComponent`,
they can work together.

All these methods **do not create a new user** on your database, they just
login the user with the data provided via the API and store it on the
session `Auth.User`.

After the login, you can redirect the user to another page to finish the signup.

## Facebook Configuration

By default, the login via [Facebook](http://facebook.com) will fetch the
user **id**, **name** and **email** from Facebook.

First, add **SocialAuth.Facebook** to your `$this->Auth->authenticate`
configuration:

```php
<?php
class AppController extends Controller {

	public $components = array('Session', 'Auth');

	public function beforeFilter() {
		$this->Auth->authenticate = array(
			'Form', // Keep this if you want to use the default login via forms
			'SocialAuth.Facebook' => array(
				'facebook' => array(
					'appId' => 'YOUR APP ID',
					'secret' => 'YOUR APP SECRET',
				)
			)
		);

		return parent::beforeFilter();
	}

}
```

And then you can create the login button on the login screen:

```php
<?php echo $this->Form->create('User') ?>
	<?php echo $this->Form->hidden('provider', array('value' => 'Facebook')) ?>
	<?php echo $this->Form->submit('Login with Facebook') ?>
<?php echo $this->Form->end() ?>
```

Later, you can use $this->Auth->getFacebookUser()