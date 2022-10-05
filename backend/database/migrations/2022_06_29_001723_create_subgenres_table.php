<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubgenresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subgenres', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('genre', '100');//ジャンル
            $table->string('subgenre', '100')->nullable();//サブジャンル
            $table->string('subgenreText', '100')->nullable();//サブジャンル読み方
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subgenres');
    }
}
