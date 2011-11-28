<?php
/**
 * Namespace for models
 */
namespace net\mediaslave\authentication\app\models;
/**
 * PageBlock
 */
/**
 * ClassBlock
 */
class UserSetting extends \Model{
	/**
	 * Add rules for this model.
	 *
	 * @author
	 */
	public function init(){
    $s = $this->schema();

    $s->hasMany('roles')
      ->className('net\mediaslave\authentication\app\models\Role', true)
      ->thru('net\mediaslave\authentication\app\models\UserSettingThruRole', true);

    $s->hasMany('users')
      ->className('net\mediaslave\authentication\app\models\User', true)
      ->thru('net\mediaslave\authentication\app\models\UserSettingThruUser', true);


  }
}
