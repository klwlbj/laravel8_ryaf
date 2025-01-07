<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceCacheCommandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('device_cache_commands', function (Blueprint $table) {
            $table->id();
            $table->string('imei');
            $table->string('msg_id')->default('')->comment('回复时绑定的消息id');
            $table->tinyInteger('type')->default(1)->unsigned()->comment('命令类型');
            $table->text('json')->nullable()->comment('命令json');
            $table->tinyInteger('is_success')->default(0)->comment('是否成功');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->dropIfExists('device_cache_commands');
    }
}
