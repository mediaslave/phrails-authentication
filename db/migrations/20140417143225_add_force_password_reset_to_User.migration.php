<?php
/**
 * Use the up method to describe the SQL that will be used to create/alter tables, columns.
 *
 * Use the down method to undo what the up method has described.
 */
/**
 * ClassBlock
 */
class Migration_20140417143225_add_force_password_reset_to_User extends Migration{

	public function up(){
		$this->alterTable('User');
    $this->boolean('force_password_reset', 'after:activated_at');
    $this->text('force_password_reset_message', 'after:force_password_reset');
	}


	public function down(){
		$this->alterTable('User');
    $this->drop('force_password_reset');
    $this->drop('force_password_reset_message');
	}


}
