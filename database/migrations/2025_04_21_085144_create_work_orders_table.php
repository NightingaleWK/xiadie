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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('title')->comment('工单标题');
            $table->text('description')->comment('工单描述');
            $table->string('status')->comment('当前状态（状态机标识符）');
            $table->foreignId('creator_user_id')->comment('发起人ID')->constrained('users');
            $table->foreignId('assigned_user_id')->nullable()->comment('当前处理人/维修人ID')->constrained('users');
            $table->foreignId('reviewer_user_id')->nullable()->comment('审核人ID')->constrained('users');
            $table->text('repair_details')->nullable()->comment('维修过程/结果记录');
            $table->text('rejection_reason')->nullable()->comment('驳回原因');
            $table->timestamp('completed_at')->nullable()->comment('完成时间');
            $table->timestamp('archived_at')->nullable()->comment('归档时间');
            $table->timestamps();

            $table->comment('工单表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
