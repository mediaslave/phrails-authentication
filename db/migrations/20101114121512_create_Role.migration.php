<?php
/**
 * Use the up method to describe the SQL that will be used to create/alter tables, columns.
 *
 * Use the down method to undo what the up method has described.
 */
/**
 * ClassBlock
 */
class Migration_20101114121512_create_Role extends Migration{
	
	public function up(){
		$this->createTable('Role');
			$this->string('name', 'limit:70');
			$this->text('description');
			$this->timestamps();
	}
	
	
	public function down(){
		
	}
	
	
}