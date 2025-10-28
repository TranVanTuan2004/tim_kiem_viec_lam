<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->after('password');
            $table->string('facebook_id')->nullable()->after('google_id');
            $table->string('provider_name')->nullable()->after('facebook_id'); // tên nhà cung cấp (google/facebook)
            $table->string('provider_token')->nullable()->after('provider_name'); // token (nếu cần)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'facebook_id', 'provider_name', 'provider_token']);
        });
    }
};
