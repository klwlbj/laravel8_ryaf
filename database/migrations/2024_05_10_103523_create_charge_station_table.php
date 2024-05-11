<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargeStationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charge_stations', function (Blueprint $table) {
            $table->id();

            $table->string('station_id')->unique()->comment('充电站ID');
            $table->string('station_name')->default(0)->comment('名称');
            $table->string('operator_id')->default(0)->comment('运营商id');
            $table->string('equipment_owner_id')->default(0)->comment('设备所属方ID');
            $table->string('area_code')->default('')->comment('充电站省市辖区编码');
            $table->string('district')->default('')->comment('所属区县');
            $table->string('street')->default('')->comment('所属街道');
            $table->string('community')->default('')->comment('所属社区');
            $table->string('village')->default('')->comment('所属小区');
            $table->string('address')->default('')->comment('详细地址');
            $table->string('service_tel')->default('')->comment('服务电话');
            $table->tinyInteger('station_type')->default(0)->comment('服务电话类型');
            $table->tinyInteger('station_status')->default(0)->comment('站点状态');

            $table->smallInteger('charging_nums')->default(0)->comment('充电口数量');
            $table->decimal('station_lng', 10, 6)->default(0)->comment('经度');
            $table->decimal('station_lat', 10, 6)->default(0)->comment('纬度');
            $table->tinyInteger('canopy')->default(0)->comment('是否带雨棚');
            $table->tinyInteger('camera')->default(0)->comment('是否有摄像头');
            $table->tinyInteger('smoke_sensation')->default(0)->comment('是否有烟感');
            $table->tinyInteger('fire_control')->default(0)->comment('是否有消防器材');
            $table->tinyInteger('fee_type')->default(0)->comment('收费方式');

            $table->string('electricity_fee')->default('')->comment('充电电费率');
            $table->string('service_fee')->default('')->comment('服务费率');
            $table->date('create_date')->nullable()->comment('建成日期');
            $table->date('operation_date')->nullable()->comment('投运日期');
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
        Schema::dropIfExists('charge_stations');
    }
}
