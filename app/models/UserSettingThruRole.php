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
class UserSettingThruRole extends \Model{


	/**
	 * Add rules for this model.
	 *
	 * @author
	 */
	public function init(){
    $s = $this->schema();

    $s->belongsTo('setting')
      ->className('net\mediaslave\authentication\app\models\UserSetting', true);

    $s->belongsTo('role')
      ->className('net\mediaslave\authentication\app\models\Role', true);
	}
}
