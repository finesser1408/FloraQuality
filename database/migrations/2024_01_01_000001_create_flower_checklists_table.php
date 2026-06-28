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
        Schema::create('flower_checklists', function (Blueprint $table) {
            $table->id();
            $table->date('check_date');
            $table->time('check_time');
            $table->enum('condition', ['good', 'average', 'bad']);
            $table->text('remarks')->nullable();
            $table->string('staff_signature')->nullable();     // path to PNG
            $table->string('supplier_signature')->nullable();  // path to PNG
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['check_date', 'condition']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flower_checklists');
    }
};
