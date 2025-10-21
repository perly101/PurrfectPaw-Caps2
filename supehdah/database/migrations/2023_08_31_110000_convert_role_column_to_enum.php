<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ConvertRoleColumnToEnum extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// This migration was empty in the repo. Keep a safe no-op that
		// ensures the migrator can instantiate the expected class.
		// If you need to convert the `role` column to an ENUM, implement
		// the conversion here. For now we just check existence.
		if (!Schema::hasTable('users')) {
			return;
		}

		// Ensure the 'role' column exists before proceeding.
		if (!Schema::hasColumn('users', 'role')) {
			return;
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// No-op reverse. Implement if up() is changed to perform alterations.
	}
}
