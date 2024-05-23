<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifyUrlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notify_url', function (Blueprint $table) {
            $table->id();
            $table->string('operator_id',50)->comment('运营商ID');
            $table->string('type',50)->comment('回调类型');
            $table->string('url',100)->comment('回调url');
            $table->timestamp('created_at')->useCurrent()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate()->useCurrent()->comment('更新时间');
            $table->softDeletes()->comment('删除时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notify_url');
    }
}
