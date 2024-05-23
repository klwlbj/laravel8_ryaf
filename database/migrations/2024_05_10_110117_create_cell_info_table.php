<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCellInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cell_info', function (Blueprint $table) {
            $table->id();
            $table->string('cell_id',50)->unique()->comment('充电口id');
            $table->string('equipment_id',50)->comment('充电设备ID');
            $table->string('operator_id',50)->comment('运营商ID');
            $table->string('charging_meta_info_id',50)->nullable()->comment('平台生成的id');

            $table->tinyInteger('cell_status')->comment('充电设备接口状态');
            $table->tinyInteger('door_status')->comment('柜门状态');
            $table->integer('error_code')->comment('故障代码');

            $table->decimal('current')->comment('电流');
            $table->decimal('voltage')->comment('电压');
            $table->decimal('power')->comment('功率');
            $table->decimal('quantity')->comment('单次累积电量');
            $table->decimal('env_temperature')->comment('实时环境温度');
            $table->decimal('cir_temperature')->comment('实时线路温度');

            $table->decimal('residual_current')->comment('剩余电流');
            $table->decimal('start_charge_seq')->nullable()->comment('充电订单号');

            $table->timestamp('update_datetime')->comment('充电桩硬件上生成上报的时间');
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
        Schema::dropIfExists('cell_info');
    }
}
