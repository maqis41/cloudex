<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('filename');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('file_type'); // document/image
            $table->string('mime_type');
            $table->integer('file_size');
            $table->text('description')->nullable();
            $table->text('tags')->nullable(); // JSON format untuk multiple tags
            $table->boolean('approved')->default(false);
            $table->boolean('edit_approved')->default(false);
            $table->boolean('delete_approved')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('files');
    }
};