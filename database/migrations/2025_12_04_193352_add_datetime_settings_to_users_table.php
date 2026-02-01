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
            $table->string('avatar')->nullable()->after('email');
            $table->string('locale', 5)->nullable()->after('avatar');
            $table->string('country', 2)->nullable()->after('locale'); // ISO 3166-1 alpha-2
            $table->string('currency', 3)->nullable()->after('country'); // ISO 4217 alpha-3
            $table->string('timezone')->nullable()->after('currency');
            $table->string('date_format', 50)->nullable()->after('timezone');
            $table->string('time_format', 50)->nullable()->after('date_format');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'locale', 'country', 'currency', 'timezone', 'date_format', 'time_format']);
        });
    }
};
