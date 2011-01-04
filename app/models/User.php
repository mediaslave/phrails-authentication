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
	
	/**
	 * This is because the constant CRYPT_SALT_LENGTH does not work
	 * on all versions of PHP.
	 */
	const crypt_salt_length = 37;
	
	public $uroles = array();
	/**
	 * Add rules for this model.
	 *
	 * @author
	 */
	public function init(){
		$this->filters()->beforeSave('encrypt');
		
		$s = $this->schema();
		
		$s->hasMany('roles')->thru('net\mediaslave\authentication\app\models\UserRole', true)->className('net\mediaslave\authentication\app\models\Role', true);
		
		$this->prepareRoles();
		$s->required('login');
		
		$s->rule('login', new \NameRule());
		$s->rule('email', new \EmailRule());
		$s->rule('password', new \PregRule('%^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).{4,10}$%', 'Password should include one lower case letter, one upper case letter, one digit, 6-15 length, and no spaces.'));		
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
	public static function create(array $array=array())
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
	public function authenticate()
	{
		$ret = false;
		$u = $this->where('login = ? && state = ?', $this->login, self::state_active)->findAll(false);
		if($u instanceof User){
			$ret =  ($u->password == $u->encrypt($this->password)) ? $u : false;
		
			$u->password = '';
			$u->salt = '';
		}
		return $ret;
	}
	
	
	/**
	 * Encrypt the password
	 *
	 * @return void
	 * @author Justin Palmer
	 **/
	public function encrypt($password=null)
	{
		if($password !== null)
			return crypt($password, $this->salt);
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
		}
	}
}