<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('name');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('image')->nullable()->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('photo');
        });
    }
};
