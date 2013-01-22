# CakePHP Social Auth

With this plugin your users can login via **Facebook**, **Twitter** or **Facebook** while using the default `AuthComponent` without extra configuration.

Don't worry! You can keep the default login via `AuthComponent`, they can work together.

All these methods **do not create a new user** on your database, they just login the user with the data provided via the API and store it on the `Auth.User` session value, just like `AuthComponent`.

After the login, you can redirect the user to another page to finish the signup.

### Installation

TODO

### Generic configuration

You'll need to change your `login()` method to something like this:

```php
<?php
public function login() {
	if ($this->Auth->login()) {
		$this->redirect($this->Auth->redirect());
	} else if ($this->request->is('post')) {
		$this->Session->setFlash(__("We couldn't login you, please try again"));
	}
}
```

Basicaly you need to run Auth->login() **without requiring** the request to be a POST.

## Facebook configuration

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

By default, the login via Facebook will fetch the user **id**, **name** and **email** from Facebook. You can add [more fields](https://developers.facebook.com/docs/reference/api/user/) on the `SocialAuth.Facebook.fields` list, with something like this on your `AppController`:

```php
<?php
$this->Auth->authenticate = array(
	'SocialAuth.Facebook' => array(
		'fields' => array('name', 'username', 'gender', 'email', 'link')
	)
);
```



## Twitter configuration

TODO

## Google configuration

TODO