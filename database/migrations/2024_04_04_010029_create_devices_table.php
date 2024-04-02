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
            $table->unsignedBigInteger('AccountID');
            $table->foreign('AccountID')->references('id')->on('accounts');
            $table->string('DeviceAccID');
            $table->string('DeviceCID');
            $table->string('DeviceID')->nullable();
            // $table->string('group_ID');
            // $table->string('room_name');
            $table->string('DeviceName');
            $table->string('DeviceMdoel');
            $table->string('DeviceCategory');
            $table->string('DeviceManufacturer');
            $table->string('DeviceBuildDate');
            $table->string('DeviceSerialN');
            $table->string('DeviceFirmwareVer');
            $table->string('DeviceOccupancyStat');
            $table->string('DeviceStatus');
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
