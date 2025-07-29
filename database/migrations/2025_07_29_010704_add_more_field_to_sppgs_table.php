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
        Schema::table('sppgs', function (Blueprint $table) {
            $table->string('district')->nullable();
            $table->string('regency')->nullable();
            $table->string('province')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppgs', function (Blueprint $table) {
            $table->dropColumn('district');
            $table->dropColumn('regency');
            $table->dropColumn('province');
        });
    }
};
