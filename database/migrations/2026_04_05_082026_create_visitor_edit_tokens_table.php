<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("visitor_edit_tokens", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("visitor_id")
                ->constrained("visitors")
                ->cascadeOnDelete();
            $table->string("token", 64)->unique();
            $table->dateTime("expires_at");
            $table->dateTime("created_at");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("visitor_edit_tokens");
    }
};
