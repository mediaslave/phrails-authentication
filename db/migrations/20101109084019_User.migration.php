<?php
/**
 * Use the up method to describe the SQL that will be used to create/alter tables, columns.
 *
 * Use the down method to undo what the up method has described.
 */
/**
 * ClassBlock
 */
class Migration_20101109084019_User extends Migration{
	
	public function up(){
		$this->createTable('User');
			$this->string('login', 'limit:25');
			$this->string('nick', 'limit:30');
			$this->string('email', 'limit:100');
			$this->text('password', 'limit:80');
			$this->string('salt', 'limit:60');
			$this->string('remember');
			$this->datetime('remember_expires_at');
			$this->string('activation_code', 'limit:255');
			$this->date('activated_at');
			$this->string('state', 'limit:30');
			$this->date('deactivated_at');
			$this->timestamps();
	}
	
	
	public function down(){
		
	}
	
	
}
