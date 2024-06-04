<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChargingRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charging_record', function (Blueprint $table) {
            $table->id();
            $table->string('start_charge_seq',50)->comment('充电订单号');
            $table->string('cell_id',50)->comment('充电口id');
            $table->string('operator_id',50)->comment('运营商ID');

            $table->dateTime('start_datetime')->nullable()->comment('开始充电时间');
            $table->dateTime('end_datetime')->nullable()->comment('结束充电时间');

            $table->decimal('total_power')->comment('累计充电量');
            $table->decimal('total_elec_money')->comment('总电费');
            $table->decimal('total_service_money')->comment('总服务费');

            $table->tinyInteger('start_type')->comment('启动方式');
            $table->tinyInteger('stop_reason')->comment('结束原因');

            $table->decimal('average_power')->comment('平均功率');

            $table->timestamp('created_at')->useCurrent()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate()->useCurrent()->comment('更新时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('charging_record');
    }
}
