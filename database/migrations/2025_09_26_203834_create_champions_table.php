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
        Schema::create('champions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('title');
            $table->text('blurb');
            $table->json('info');
            $table->json('image');
            $table->json('tags');
            $table->string('partype');
            $table->json('stats');
            $table->json('spells');
            $table->json('passive');
            $table->longText('lore');
            $table->json('allytips');
            $table->json('enemytips');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('champions');
    }
};
