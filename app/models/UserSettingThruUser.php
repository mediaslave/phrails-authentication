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

  /**
   *
   * Find the user settings
   *
   * @return array
   * @author Justin Palmer
   **/
  public function findAllSettings($id)
  {
    return $this->findBySql('SELECT user_setting.*
                            FROM `user_setting_thru_users` AS `user_setting`
                            INNER JOIN  `users` AS `user`
                                ON `user`.id = `user_setting`.user_id
                            INNER JOIN `user_roles` AS `role`
                                ON `user`.id = `role`.user_id
                            INNER JOIN `user_setting_thru_roles` AS `rthru`
                                ON `role`.role_id = `rthru`.role_id
                            INNER JOIN `user_settings` AS `setting`
                                ON `rthru`.user_setting_id = `setting`.id
                            WHERE `user`.id = ?
                            GROUP BY `user_setting`.user_id, `user_setting`.user_setting_id',
                            array($id));
  }
}
