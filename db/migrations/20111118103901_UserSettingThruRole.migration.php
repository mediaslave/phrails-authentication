<?php
/**
 * Use the up method to describe the SQL that will be used to create/alter tables, columns.
 *
 * Use the down method to undo what the up method has described.
 */
/**
 * ClassBlock
 */
class Migration_20111118103901_UserSettingThruRole extends Migration{

	public function up(){
    $this->createTable('UserSettingThruRole');
    $this->references('UserSetting');
    $this->references("Role");
  }


  public function down(){
    $this->dropTable('UserSettingThruRole');

  }


}
