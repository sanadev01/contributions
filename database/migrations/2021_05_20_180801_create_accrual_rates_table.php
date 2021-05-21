<?php

use App\Services\Correios\Models\Package;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccrualRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accrual_rates', function (Blueprint $table) {
            $table->id();

            $table->double('weight')->default(0);
            $table->bigInteger('service')->default(Package::SERVICE_CLASS_STANDARD);
            $table->double('cwb')->default(0);
            $table->double('gru')->default(0);
            
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
        Schema::dropIfExists('accrual_rates');
    }
}
