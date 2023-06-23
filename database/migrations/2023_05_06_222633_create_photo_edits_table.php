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
        Schema::create('photo_edits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('photo_id');
            // $table->integer('ind');
            $table->string('original_img')->nullable();
            $table->string('bg_removed_img')->nullable();
            $table->string('bg_custom')->nullable();
            $table->integer('bg_custom_status')->default(0);
            $table->text('edit_img')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_edits');
    }
};
