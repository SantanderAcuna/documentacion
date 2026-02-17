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
        Schema::create('funcionarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('full_name');
            $table->string('identification_number');
            $table->foreignId('cargo_id')->nullable()->constrained('cargos')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->date('hire_date');
            $table->enum('contract_type', ['planta', 'contrato', 'provisional']);
            $table->enum('status', ['active', 'inactive', 'vacation', 'license'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('user_id');
            $table->index('identification_number');
            $table->index('cargo_id');
            $table->index('department_id');
            $table->index('contract_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funcionarios');
    }
};
