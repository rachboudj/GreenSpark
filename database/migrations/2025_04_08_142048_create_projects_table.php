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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('project_categories')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('short_description');
            $table->text('description');
            $table->decimal('funding_goal', 10, 2);
            $table->decimal('current_amount', 10, 2)->default(0);
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->enum('status', ['draft', 'pending', 'active', 'funded', 'closed']);
            $table->string('region');
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
