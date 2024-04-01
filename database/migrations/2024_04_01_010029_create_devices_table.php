<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('api_key');
            $table->string('device_accId');
            $table->string('device_cid');
            $table->string('device_id')->nullable();
            // $table->string('group_ID');
            // $table->string('room_name');
            $table->string('device_name');
            $table->string('device_model');
            $table->string('device_category');
            $table->string('device_manufacturer');
            $table->string('device_buildDate');
            $table->string('device_serialN');
            $table->string('device_firmwareVer');
            $table->string('device_occupancyStat');
            $table->string('device_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
