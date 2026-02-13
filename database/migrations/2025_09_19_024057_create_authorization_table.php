<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('authorization', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->boolean('DeleteUser')->default(false);
            $table->boolean('EditRooms')->default(false);
            $table->boolean('EditPricing')->default(false);
            $table->boolean('EditImages')->default(false);
            $table->boolean('RevokeAccess')->default(false);
        });

        // Insert authorization levels
        DB::table('authorization')->insert([
            [
                'id' => 1,
                'DeleteUser' => true,
                'EditRooms' => true,
                'EditPricing' => true,
                'EditImages' => true,
                'RevokeAccess' => true,
            ],
            [
                'id' => 2,
                'DeleteUser' => false,
                'EditRooms' => true,
                'EditPricing' => true,
                'EditImages' => true,
                'RevokeAccess' => false,
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authorization');
    }
};
