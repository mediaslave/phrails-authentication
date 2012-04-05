<?php
/**
 * Use the up method to describe the SQL that will be used to create/alter tables, columns.
 *
 * Use the down method to undo what the up method has described.
 */
/**
 * ClassBlock
 */
class Migration_20120403092237_UserTrace extends Migration{

	public function up(){
		$this->createTable('UserTrace');
			$this->references('User');
			$this->string('name');
			$this->string('controller');
			$this->string('action');
			$this->string('view_type');
			$this->text('namespace');
			$this->text('request_uri');
			$this->text('path');
			$this->text('params');
			$this->text('post');
			$this->timestamps();		
	}


	public function down(){
		$this->dropTable('UserTrace');
	}


}