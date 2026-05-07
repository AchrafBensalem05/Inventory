<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->string('location');
            $table->decimal('quantity', 10, 2);
            $table->string('unit');
            $table->decimal('min_threshold', 10, 2);
            $table->decimal('unit_cost', 12, 2);
            $table->date('last_restocked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
