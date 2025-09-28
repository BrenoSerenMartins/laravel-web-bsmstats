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
        Schema::create('summoners', function (Blueprint $table) {
            $table->string('puuid')->primary();
            $table->string('gameName');
            $table->string('tagLine');
            $table->string('summonerId')->unique()->nullable();
            $table->string('accountId')->nullable();
            $table->integer('profileIconId');
            $table->bigInteger('summonerLevel');
            $table->bigInteger('revisionDate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('summoners');
    }
};
