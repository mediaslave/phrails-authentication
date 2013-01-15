<?php
/**
 * Namespace for models
 */
namespace net\mediaslave\authentication\app\models;
/**
 * PageBlock
 */
/**
 *
 */
class User extends \Model{

	const state_initial = 'initial';
	const state_active = 'active';
	const state_suspended = 'suspended';

	const USERNAME_MESSAGE = 'Username should be between 6 and 15 characters in length.  Additionally, it can only contain characters (a-z or capital A-Z), period (.) and hyphen (-).';
	const PASSWORD_MESSAGE = 'Password should be between 6 and 15 characters in length.  Additionally, it must contain one upper case letter, one lower case letter, and one digit. It may not contain spaces.';

	/**
	 * This is because the constant CRYPT_SALT_LENGTH does not work
	 * on all versions of PHP.
	 */
	const crypt_salt_length = 37;

	public $uroles = array();

	protected $role_ids = array();

	public $_settings;

	function __construct($array=array()) {
		parent::__construct($array);
		//Do we need to trace the user?
		$settings = \Registry::get('pr-plugin-phrails-authentication');
		if($settings->global->trace){
			$trace = new UserTrace();
			$trace->user_id = $this->id;
			$trace->create();
		}
	}

	/**
	 * Add rules for this model.
	 *
	 * @author
	 */
	public function init(){
		$this->filters()->beforeSave('encrypt');

		$s = $this->schema();

		$s->hasMany('roles')
		  ->className('net\mediaslave\authentication\app\models\Role', true)
			->thru('net\mediaslave\authentication\app\models\UserRole', true);

		$s->hasMany('user_roles')
			->className('net\mediaslave\authentication\app\models\UserRole', true);

		$s->hasMany('settings')
			->className('net\mediaslave\authentication\app\models\UserSetting', true)
			->thru('net\mediaslave\authentication\app\models\UserSettingThruUser', true);

		$this->prepareRoles();
		$s->required('login', 'email', 'password');

		$s->rule('login', new \AlphaExtraRule('\-\s\.0-9', '%s can include any alphanumeric character, hyphen, space and period.'));
		$s->rule('login', new \LengthRangeRule(6, 15));
		$s->rule('login', new \UniqueDatabaseRule());
		$s->rule('email', new \EmailRule());
		$s->rule('email', new \UniqueDatabaseRule());

		$s->rule('password', new \PregRule('%^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).{4,13}$%', 'Password should include one lower case letter, one upper case letter, one digit, 6-15 characters in length, and no spaces.'));
	}
	/**
	 * Does the user have the appropriate roles?
	 *
	 * This is an exclusive check (AND).
	 *
	 * @return void
	 * @author Justin Palmer
	 **/
	public function hasRole($roles)
	{
		$roles = func_get_args();
		$diff = array_diff($roles, $this->uroles);
		return (count($diff) == 0);
	}
	/**
	 * Does the user have any one of the roles listed
	 *
	 * @return void
	 * @author Justin Palmer
	 **/
	public function hasAnyRole($roles)
	{
		$roles = func_get_args();
		$intersect = array_intersect($roles, $this->uroles);
		return (count($intersect) > 0);
	}
	/**
	 * Static method to create a new user
	 *
	 * @return void
	 * @author Justin Palmer
	 **/
	public static function createNew(array $array=array())
	{
		$u = new User($array);
		$u->activation_code = self::token();
		$u->state = self::state_initial;
		return $u;
	}

	/**
	 * Set the remind token for a user
	 *
	 * @return void
	 * @author Justin Palmer
	 **/
	public function remind()
	{
		$this->remember = $this->token();
		$this->remember_expires_at = new \Expression('DATE_ADD( NOW() , INTERVAL 2 HOUR )');
		return $this;
	}

	/**
	 * Authenticate the user
	 *
	 * @return void
	 * @author Justin Palmer
	 **/
	public function authenticate($active_state = true)
	{
		$ret = false;
		try{
			if($active_state){
				$u = $this->findByLoginAndState($this->login, self::state_active);
			}else{
				$u = $this->findByLogin($this->login);
			}
			$ret = ($u->password == $u->encrypt($this->password)) ? $u : false;
			$u->password = '';
			$u->salt = '';
			return $ret;
		}catch(\RecordNotFoundException $e){}
		return false;
	}


	/**
	 * Encrypt the password
	 *
	 * @return void
	 * @author Justin Palmer
	 **/
	protected function encrypt($password=null)
	{
		if($password !== null){
			return crypt($password, $this->salt);
		}
		if($this->password !== null){
			srand();
			$salt = '$2a$10$' . substr(hash('whirlpool', md5($this->login . rand() . md5(date('y-m-d')) . $this->email)), 0, self::crypt_salt_length) . '$';
			$this->password =  crypt($this->password, $salt);
			$this->salt = $salt;
		}
	}

	/**
	 * Generate an activation or remember token
	 *
	 * @return void
	 * @author Justin Palmer
	 **/
	public static function token()
	{
		srand();
		return sha1(sha1(sha1(sha1(time() . md5(rand() . date('Y-m-d'))))));
	}

	/**
	 * Prepare the roles for comparison
	 *
	 * @return void
	 * @author Justin Palmer
	 **/
	public function prepareRoles()
	{
		if(count($this->uroles) > 0)
			return;
		$roles = $this->roles;
		foreach($roles as $role){
			$this->uroles[] = $role->name;
			$this->role_ids[] = $role->id;
		}
	}

	/**
	 * Get the role id's
	 * 
	 * @return array
	 */
	public function getRoleIds(){
		return $this->role_ids;
	}

	/**
	 *
	 * Get the setting specified and retur it
	 *
	 * @param string $set
	 * @return mixed string || \Hash
	 **/
	public function setting($set=null)
	{
		if(!($this->_settings instanceof \Hash)){
			$this->_settings = new \Hash;
		}
		$this->prepareRoles();
		//load settings
		//convert to hash
		//return one or all depending on the param
		foreach($this->settings as $setting){
			foreach($setting->roles as $role){
				if($this->hasRole($role->name)){
					//Yes we have the role required for this setting
					//add it.
					try {
						$value = UserSettingThruUser::noo()->findByUserSettingIdAndUserId($setting->id, $this->id);
						$this->_settings->set($setting->slug, $value->value);
					} catch (\RecordNotFoundException $e) {/*don't add the setting*/}
				}
			}
		}

		if($set === null){
			return $this->_settings;
		}
		return $this->_settings->get($set);
	}
}
