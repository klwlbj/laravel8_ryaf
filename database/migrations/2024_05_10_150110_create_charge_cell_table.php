<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChargeCellTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charge_cells', function (Blueprint $table) {
            $table->id();

            $table->string('cell_id')->unique()->comment('充电设备接口ID');
            $table->string('equipment_id')->default(0)->comment('所属充电设施ID');
            $table->string('operator_id')->default(0)->comment('所属运营商ID');
            $table->tinyInteger('cell_type')->default(0)->comment('充电设备接口类型');
            $table->string('cell_standard')->default('')->comment('接口标准');
            $table->decimal('rated_voltage_upper_limit', 4, 1)->default(0)->comment('额定电压上限');
            $table->decimal('rated_voltage_lower_limit', 4, 1)->default(0)->comment('额定电压下限');
            $table->decimal('rated_current', 4, 1)->default(0)->comment('额定电流');
            $table->decimal('rated_power', 4, 1)->default(0)->comment('额定功率');
            $table->string('electricity_fee')->default('')->comment('充电电费率描述');
            $table->string('service_fee')->default('')->comment('服务费率描述');
            $table->tinyInteger('fire_control')->default(0)->comment('是否有灭火装置');
            $table->tinyInteger('smoke_sensation')->default(0)->comment('是否有烟感');

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
        Schema::dropIfExists('charge_cells');
    }
}
