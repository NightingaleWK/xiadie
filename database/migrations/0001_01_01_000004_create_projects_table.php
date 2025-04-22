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
        Schema::create('projects', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('name')->comment('项目名称（中文）');
            $table->string('name_en')->nullable()->comment('项目名称（英文）');
            $table->string('code')->unique()->comment('项目标识符/编码');
            $table->text('description')->nullable()->comment('项目描述');
            $table->date('start_date')->nullable()->comment('立项日期');
            $table->date('operation_date')->nullable()->comment('转运维日期');
            $table->date('end_date')->nullable()->comment('项目结束日期');
            $table->string('project_manager')->nullable()->comment('项目经理姓名');
            $table->string('manager_phone')->nullable()->comment('项目经理联系电话');
            $table->string('client_name')->nullable()->comment('客户单位名称');
            $table->string('client_contact')->nullable()->comment('客户联系人');
            $table->string('client_phone')->nullable()->comment('客户联系电话');
            $table->string('status')->default('planning')->comment('项目状态');
            $table->text('remarks')->nullable()->comment('备注');
            $table->timestamps();

            $table->comment('项目表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
