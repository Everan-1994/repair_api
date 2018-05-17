<?php

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Migrations\Migration;

class SeedRolesAndPermissionsData extends Migration
{
    public function up()
    {
        // 清除缓存
        app()['cache']->forget('spatie.permission.cache');

        // 先创建权限
        Permission::create(['name' => 'manage_users']);
        Permission::create(['name' => 'settings']);
        Permission::create(['name' => 'manage_orders']);

        // 创建站长角色，并赋予权限
        $founder = Role::create(['name' => 'Founder']);
        $founder->givePermissionTo('manage_users');
        $founder->givePermissionTo('settings');

        // 创建管理员角色，并赋予权限
        $maintainer = Role::create(['name' => 'Maintainer']);
        $maintainer->givePermissionTo('settings');

        // 创建客户角色
        $maintainer = Role::create(['name' => 'Customer']);
        $maintainer->givePermissionTo('manage_orders');
    }

    public function down()
    {
        // 清除缓存
        app()['cache']->forget('spatie.permission.cache');

        // 清空所有数据表数据
        $tableNames = config('permission.table_names');

        Model::unguard();
        DB::table($tableNames['role_has_permissions'])->delete();
        DB::table($tableNames['model_has_roles'])->delete();
        DB::table($tableNames['model_has_permissions'])->delete();
        DB::table($tableNames['roles'])->delete();
        DB::table($tableNames['permissions'])->delete();
        Model::reguard();
    }
}
