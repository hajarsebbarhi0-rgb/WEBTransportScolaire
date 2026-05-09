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
    public function up(): void
    {
        Schema::table('arrets', function (Blueprint $table) {
            $table->boolean('is_reached')
                  ->default(false)
                  ->after('order_number');
        });
    }

    public function down(): void
    {
        Schema::table('arrets', function (Blueprint $table) {
            $table->dropColumn('is_reached');
        });
    }
};
