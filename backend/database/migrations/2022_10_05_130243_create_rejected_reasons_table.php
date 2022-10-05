<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRejectedReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rejected_reasons', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            
            //追記
            $table->string('reason', '100')->unique();//
            $table->string('reasonText', '100');//
            $table->string('genre', '100');//この選択肢使用するジャンル
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rejected_reasons');
    }
}
