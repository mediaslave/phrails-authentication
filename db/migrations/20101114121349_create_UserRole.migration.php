<?php
/**
 * Use the up method to describe the SQL that will be used to create/alter tables, columns.
 *
 * Use the down method to undo what the up method has described.
 */
/**
 * ClassBlock
 */
class Migration_20101114121349_create_UserRole extends Migration{
	
	public function up(){
		$this->createTable('UserRole');
			$this->references('User');
			$this->references('Role');
			$this->timestamps();
	}
	
	
	public function down(){
		
	}
	
	
}