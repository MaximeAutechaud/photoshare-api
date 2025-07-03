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
        Schema::table('medias', function (Blueprint $table) {
            $table->string('s3_url', 255)->after('user_id');
            $table->dateTime('taken_at')->nullable()->after('s3_url');
            $table->integer('height')->nullable()->after('taken_at');
            $table->integer('width')->nullable()->after('height');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medias', function (Blueprint $table) {
            $table->dropColumn('s3_url');
            $table->dropColumn('taken_at');
            $table->dropColumn('height');
            $table->dropColumn('width');
        });
    }
};
