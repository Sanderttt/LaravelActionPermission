<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateControllerActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('controller_actions')) {
            Schema::create('controller_actions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();
                $table->string('controller');
                $table->string('function');
                $table->string('method');
                $table->string('name');
                $table->string('path');
                $table->boolean('is_ignored')->default(false);
                $table->boolean('in_nav')->default(false);
                $table->unique(['controller', 'function', 'method']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('controller_actions');
    }
}
