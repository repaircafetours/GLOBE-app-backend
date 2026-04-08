<?php

use App\Models\Role;
use App\Models\Speciality;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("volunteers", function (Blueprint $table) {
            $table->id();
            $table->integer("idHumHub");
            $table->string("password")->nullable();
            $table->string("login")->nullable()->unique();
            $table->schemalessAttributes("extra_attributes");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("volunteers");
    }
};
