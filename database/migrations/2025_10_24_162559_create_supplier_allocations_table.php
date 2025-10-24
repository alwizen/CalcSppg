<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('supplier_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->decimal('allocated_amount', 14, 3);   // misal 50.000 kg
            $table->string('unit', 32);                   // redundan agar konsisten tampilan
            $table->decimal('price_per_unit', 14, 2)->nullable(); // diisi supplier
            $table->decimal('total_price', 14, 2)->nullable();    // auto dihitung
            $table->enum('status', ['draft', 'submitted', 'confirmed'])
                ->default('draft'); // lifecycle ringan
            $table->timestamps();

            $table->index(['menu_group_id', 'ingredient_id']);
            $table->unique(
                ['menu_group_id', 'ingredient_id', 'supplier_id'],
                'allocations_unique_triplet' // nama index pendek manual
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_allocations');
    }
};
