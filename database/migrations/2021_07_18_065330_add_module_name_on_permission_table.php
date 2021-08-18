<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModuleNameOnPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');
        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->string('module')->nullable()->after('guard_name');
        });

        $permissions = array(
            array('id' => '1','name' => 'create_role','guard_name' => 'web','module' => 'role','created_at' => '2021-08-02 11:01:12','updated_at' => '2021-08-16 05:44:21'),
            array('id' => '3','name' => 'delete_role','guard_name' => 'web','module' => 'role','created_at' => '2021-08-11 12:28:32','updated_at' => '2021-08-11 12:28:32'),
            array('id' => '4','name' => 'update_role','guard_name' => 'web','module' => 'role','created_at' => '2021-08-11 12:28:44','updated_at' => '2021-08-11 12:28:44'),
            array('id' => '5','name' => 'view_role','guard_name' => 'web','module' => 'role','created_at' => '2021-08-11 12:28:56','updated_at' => '2021-08-11 12:28:56'),
            array('id' => '6','name' => 'create_post','guard_name' => 'web','module' => 'post','created_at' => '2021-08-11 12:29:21','updated_at' => '2021-08-16 05:43:10'),
            array('id' => '7','name' => 'delete_post','guard_name' => 'web','module' => 'post','created_at' => '2021-08-11 12:29:33','updated_at' => '2021-08-11 12:29:33'),
            array('id' => '8','name' => 'update_post','guard_name' => 'web','module' => 'post','created_at' => '2021-08-11 12:29:42','updated_at' => '2021-08-11 12:29:42'),
            array('id' => '9','name' => 'view_post','guard_name' => 'web','module' => 'post','created_at' => '2021-08-11 12:29:52','updated_at' => '2021-08-11 12:29:52'),
            array('id' => '10','name' => 'create_category','guard_name' => 'web','module' => 'category','created_at' => '2021-08-16 05:49:51','updated_at' => '2021-08-16 05:49:51'),
            array('id' => '11','name' => 'update_category','guard_name' => 'web','module' => 'category','created_at' => '2021-08-16 05:50:07','updated_at' => '2021-08-16 05:50:07'),
            array('id' => '12','name' => 'delete_category','guard_name' => 'web','module' => 'category','created_at' => '2021-08-16 05:50:24','updated_at' => '2021-08-16 05:50:24'),
            array('id' => '13','name' => 'view_category','guard_name' => 'web','module' => 'category','created_at' => '2021-08-16 05:50:48','updated_at' => '2021-08-16 05:50:48'),
            array('id' => '14','name' => 'create_permission','guard_name' => 'web','module' => 'permission','created_at' => '2021-08-17 07:03:55','updated_at' => '2021-08-17 07:03:55'),
            array('id' => '15','name' => 'update_permission','guard_name' => 'web','module' => 'permission','created_at' => '2021-08-17 07:04:06','updated_at' => '2021-08-17 07:04:06'),
            array('id' => '16','name' => 'delete_permission','guard_name' => 'web','module' => 'permission','created_at' => '2021-08-17 07:04:18','updated_at' => '2021-08-17 07:04:18'),
            array('id' => '17','name' => 'view_permission','guard_name' => 'web','module' => 'permission','created_at' => '2021-08-17 07:04:31','updated_at' => '2021-08-17 07:04:31'),
            array('id' => '19','name' => 'create_page','guard_name' => 'web','module' => 'page','created_at' => '2021-08-17 09:37:52','updated_at' => '2021-08-17 09:37:52'),
            array('id' => '20','name' => 'update_page','guard_name' => 'web','module' => 'page','created_at' => '2021-08-17 09:38:02','updated_at' => '2021-08-17 09:38:02'),
            array('id' => '21','name' => 'delete_page','guard_name' => 'web','module' => 'page','created_at' => '2021-08-17 09:38:14','updated_at' => '2021-08-17 09:38:14'),
            array('id' => '22','name' => 'view_page','guard_name' => 'web','module' => 'page','created_at' => '2021-08-17 09:38:30','updated_at' => '2021-08-17 09:38:30')
        );
        DB::table($tableNames['permissions'])->insert($permissions);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('module');
        });
    }
}
