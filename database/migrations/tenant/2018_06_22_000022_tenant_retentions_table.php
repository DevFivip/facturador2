<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantRetentionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retentions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('establishment_id');
            $table->uuid('external_id');
            $table->char('soap_type_id', 2);
            $table->char('state_type_id', 2);
            $table->string('ubl_version');
            $table->char('document_type_id', 2);
            $table->unsignedInteger('series_id');
            $table->integer('number');
            $table->date('date_of_issue');
            $table->unsignedInteger('customer_id');
            $table->char('currency_type_id', 3);
            $table->text('observation');
            $table->char('system_code_retention_id', 8);
            $table->decimal('percent', 10, 2);
            $table->decimal('total_retention', 10, 2);
            $table->decimal('total', 10, 2);

            $table->string('filename')->nullable();
            $table->string('hash')->nullable();
            $table->boolean('has_xml')->default(false);
            $table->boolean('has_pdf')->default(false);
            $table->boolean('has_cdr')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('establishment_id')->references('id')->on('establishments');
            $table->foreign('soap_type_id')->references('id')->on('soap_types');
            $table->foreign('state_type_id')->references('id')->on('state_types');
            $table->foreign('document_type_id')->references('id')->on('document_types');
            $table->foreign('series_id')->references('id')->on('series');
            $table->foreign('currency_type_id')->references('id')->on('currency_types');
            $table->foreign('system_code_retention_id')->references('id')->on('codes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('retentions');
    }
}
