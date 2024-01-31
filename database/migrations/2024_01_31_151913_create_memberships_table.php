<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('membership_picture')->nullable();
            $table->text('description')->nullable();
            $table->integer('duration_months');
            $table->decimal('monthly_fee', 8, 2);
            $table->boolean('personal_training')->nullable();
            $table->integer('personal_training_sessions_per_week')->nullable();
            $table->boolean('secure_locker')->nullable();
            $table->boolean('guest_access')->nullable();
            $table->boolean('pay_as_you_go')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
