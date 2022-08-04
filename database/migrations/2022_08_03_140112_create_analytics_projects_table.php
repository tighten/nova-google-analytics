<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('analytics_projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_id');
            $table->string('project_name');
            $table->string('credentials');
            $table->string('gate')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('analytics_projects');
    }
};
