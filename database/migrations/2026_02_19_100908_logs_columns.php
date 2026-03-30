<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("logs_columns", function (Blueprint $table) {
            $table->unsignedBigInteger("logs_id");
            $table->string("table_name");
            $table->string("column_name");

            $table->primary(["logs_id", "table_name", "column_name"]);

            $table
                ->foreign("logs_id")
                ->references("id")
                ->on("logs")
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("logs_columns");
    }
};
