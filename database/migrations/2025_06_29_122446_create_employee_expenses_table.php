<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeExpensesTable extends Migration
{
    public function up(): void
    {
        Schema::create('employee_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('employee_id')->nullable(); // from employee_profiles
            $table->string('type')->comment('advance, purchase, item, etc.');
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->date('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employee_profiles')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_expenses');
    }
}
