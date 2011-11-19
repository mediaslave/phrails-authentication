<?php
/**
 * Use the up method to describe the SQL that will be used to create/alter tables, columns.
 *
 * Use the down method to undo what the up method has described.
 */
/**
 * ClassBlock
 */
class Migration_20111118103852_UserSetting extends Migration{

	public function up(){
    $this->createTable('UserSetting');
    $this->string('slug', 'limit:50');
    $this->string('name');
    $this->text('description');
    $this->timestamps();
  }


  public function down(){
    $this->dropTable('UserSetting');
  }


}
