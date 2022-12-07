<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('username', 50)->unique();
            $table->string('password', 60);
        });
        DB::table('users')->insert([
            ['username' => 'admin', 'password' => '$2y$10$kInmDhOhBvPv1R2YbyvtB.KT58SFsVo4Hg/XCoGSdMXidIr/r7Dki'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
