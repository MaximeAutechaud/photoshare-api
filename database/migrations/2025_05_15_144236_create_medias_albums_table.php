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
        Schema::create('medias_albums', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('media_id');
            $table->unsignedBigInteger('album_id');
            $table->timestamps();
            $table->foreign('media_id')->references('id')->on('medias')->onDelete('cascade');
            $table->foreign('album_id')->references('id')->on('albums')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medias_albums', function (Blueprint $table) {
            $table->dropForeign(['album_id']);
            $table->dropForeign(['media_id']);
            $table->dropColumn('media_id');
            $table->dropColumn('album_id');
        });
        Schema::dropIfExists('medias_albums');
    }
};
