<?php
/**
 * Namespace for models
 */
//namespace net\mediaslave\phrails\authentication\app\models;
/**
 * PageBlock
 */
/**
 * 
 */
class User extends Model{
	
	const state_initial = 'initial';
	const state_active = 'active';
	const state_suspended = 'suspended';
	/**
	 * Add rules for this model.
	 *
	 * @author
	 */
	public function init(){
		$this->filters()->beforeSave('encrypt');
		
		$s = $this->schema();
		
		$s->required('login');
		
		$s->rule('login', new \NameRule());
		$s->rule('email', new \EmailRule());
		$s->rule('password', new \PregRule('%^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).{4,10}$%', 'Password should include one lower case letter, one upper case letter, one digit, 6-15 length, and no spaces.'));		
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
		$u->state = 'initial';
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
		$this->remember_expires_at = new Expression('DATE_ADD( NOW() , INTERVAL 2 HOUR )');
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
			$salt = '$2a$10$' . substr(hash('whirlpool', md5($this->login . rand() . md5(date('y-m-d')) . $this->email)), 0, CRYPT_SALT_LENGTH) . '$';
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
}