<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargeEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charge_equipments', function (Blueprint $table) {
            $table->id();

            $table->string('equipment_id')->unique()->comment('设备编码');
            $table->string('station_id')->default(0)->comment('所属站点ID');
            $table->string('operator_id')->default(0)->comment('运营商ID');
            $table->string('equipment_name')->default('')->comment('设备名称');
            $table->string('manufacturer_brand')->default('')->comment('设备品牌');
            $table->string('equipment_model')->default('')->comment('设备型号');
            $table->integer('manufacturer_id')->default(0)->comment('生产商ID');
            $table->date('production_date')->comment('设备生产日期');
            $table->tinyInteger('equipment_type')->default(0)->comment('设备类别');
            $table->tinyInteger('equipment_category')->default(0)->comment('设备类型');
            $table->smallInteger('validate_connector_count')->default(0)->comment('有效的充电接口数量');
            $table->decimal('rated_voltage', 3, 1)->comment('整机额定电压');
            $table->decimal('rated_current', 3, 1)->comment('整机额定电流');
            $table->decimal('rated_power', 3, 1)->comment('整机额定功率');
            $table->decimal('equipment_lng', 10, 6)->comment('充电设备经度');
            $table->decimal('equipment_lat', 10, 6)->comment('充电设备纬度');
            $table->date('operation_date')->nullable()->comment('投运日期');
            $table->tinyInteger('camera')->default(0)->comment('是否有摄像头');
            $table->softDeletes();
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
        Schema::dropIfExists('charge_equipments');
    }
}
