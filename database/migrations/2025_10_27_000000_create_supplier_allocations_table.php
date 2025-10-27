<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_allocations', function (Blueprint $table) {
            $table->id();

            // FK ke master tables (hapus relasi kalau parent dihapus)
            $table->foreignId('menu_group_id')->constrained('menu_groups')->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained('ingredients')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();

            // Nilai
            // Pakai 3 desimal untuk quantity supaya fleksibel (ubah ke 2 jika mau bulat cent)
            $table->decimal('quantity', 12, 3);
            $table->decimal('price', 12, 2)->default(0); // sesuaikan jika mau nullable()
            $table->text('notes')->nullable();

            // Status alur
            $table->enum('status', ['pending', 'delivered'])->default('pending');

            $table->timestamps();

            // Unik per kombinasi MG + Ingredient + Supplier (ini inti perbaikannya)
            $table->unique(
                ['menu_group_id', 'ingredient_id', 'supplier_id'],
                'supplier_allocations_mg_ing_sup_unique'
            );

            // Index bantu untuk query sum/filter (opsional tapi berguna)
            $table->index(['menu_group_id', 'ingredient_id'], 'supplier_allocations_mg_ing_idx');
            $table->index('supplier_id', 'supplier_allocations_supplier_id_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_allocations');
    }
};
