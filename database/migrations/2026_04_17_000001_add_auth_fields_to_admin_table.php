<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admin', function (Blueprint $table) {
            $table->string('email')->nullable()->after('id_user');
            $table->string('username')->nullable()->after('email');
            $table->string('password')->nullable()->after('username');
        });

        DB::table('admin')
            ->join('users', 'users.id_user', '=', 'admin.id_user')
            ->update([
                'admin.email' => DB::raw('users.email'),
                'admin.username' => DB::raw('users.username'),
                'admin.password' => DB::raw('users.password'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin', function (Blueprint $table) {
            $table->dropColumn(['email', 'username', 'password']);
        });
    }
};
