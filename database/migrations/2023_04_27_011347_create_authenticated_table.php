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
        Schema::create('authenticated', function (Blueprint $table) {
            $table->id();
            $table->string('uid');
            $table->string('name');
            $table->string('username');
            $table->string('user');
            $table->string('user_email');
            $table->string('territory');
            $table->string('department');
            $table->string('domain');
            $table->string('login');
            $table->string('scope');
            $table->string('access_token');
            $table->string('expires_in');
            $table->string('token_type');
            $table->string('refresh_token');
            $table->string('client_id');
            $table->string('apiversion');
            $table->string('api_password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authenticated');
    }
};
