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
        Schema::create('work_order_histories', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->foreignId('work_order_id')->comment('关联工单ID')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->comment('操作人ID')->constrained()->cascadeOnDelete();
            $table->string('action')->comment('操作描述');
            $table->string('from_status')->nullable()->comment('操作前状态');
            $table->string('to_status')->nullable()->comment('操作后状态');
            $table->text('comment')->nullable()->comment('操作备注');
            $table->timestamps();

            $table->comment('工单历史记录表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_histories');
    }
};
