<?php
/**
 * Use the up method to describe the SQL that will be used to create/alter tables, columns.
 *
 * Use the down method to undo what the up method has described.
 */
/**
 * ClassBlock
 */
class Migration_20111118103910_UserSettingThruUser extends Migration{

	public function up(){
    $this->createTable('UserSettingThruUser');
    $this->references('UserSetting');
    $this->references("User");
    $this->text('value');
    $this->timestamps();
  }


  public function down(){
    $this->dropTable('UserSettingThruUser');
  }


}
