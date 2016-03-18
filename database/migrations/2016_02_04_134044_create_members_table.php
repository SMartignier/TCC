<?php

	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;

	class CreateMembersTable extends Migration
	{
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up()
		{
			Schema::create('members', function(Blueprint $table)
			{
				$table->increments('id');

				$table->string('last_name');
				$table->string('first_name');
				$table->string('address');
				$table->integer('zip_code');
				$table->string('city');
				$table->string('email')->unique();
				$table->string('mobile_phone');
				$table->string('home_phone');
				$table->date('birth_date');
				$table->string('token');
				$table->string('password');
				$table->string('login'); 
				$table->rememberToken();

				$table->boolean('active');
				$table->boolean('administrator');
				$table->boolean('validate');

				$table->timestamps();
			});
		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down()
		{
			Schema::drop('members');
		}

	}
