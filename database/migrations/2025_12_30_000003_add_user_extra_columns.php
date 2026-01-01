<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable()->after('email');
            $table->foreignId('type_id')->nullable()->constrained('user_types')->nullOnDelete()->after('role');
            $table->string('phone')->nullable()->after('type_id');
            $table->string('team_id')->nullable()->after('phone');
            $table->string('avatar_url')->nullable()->after('team');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropColumn(['role', 'type_id', 'phone', 'team_id', 'avatar_url']);
        });
    }
};
