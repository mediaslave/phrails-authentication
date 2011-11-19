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
class UserSettingThruUser extends \Model{


	/**
	 * Add rules for this model.
	 *
	 * @author
	 */
	public function init(){
    $s = $this->schema();

    $s->belongsTo('setting')
      ->className('net\mediaslave\authentication\app\models\UserSetting', true);

    $s->belongsTo('user')
      ->className('net\mediaslave\authentication\app\models\User', true);
	}
}
