<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //$table->engine = 'InnoDB';
            $table->integer('user_type')->nullable()->unsigned()->after('id');
            $table->integer('parent')->nullable()->unsigned()->after('user_type');

            $table->foreign('user_type')->references('id')->on('user_types')->onDelete('set null');
            $table->foreign('parent')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['parent', 'user_type']);

            $table->dropColumn('parent');
            $table->dropColumn('user_type');
        });
    }
}
