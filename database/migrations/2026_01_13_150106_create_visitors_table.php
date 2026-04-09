<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("visitors", function (Blueprint $table) {
            $table->id();
            $table->string("email")->unique();
            $table->string("title");
            $table->string("name");
            $table->string("surname");
            $table->string("zip_code");
            $table->string("city");
            $table->string("phone_number")->nullable();
            $table->string("source")->nullable();
            $table->boolean("notification");
            $table->schemalessAttributes("extra_attributes");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("visitors");
    }
};
