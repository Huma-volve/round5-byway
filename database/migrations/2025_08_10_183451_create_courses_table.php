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
        Schema::disableForeignKeyConstraints();

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete("cascade");
            $table->string('title');
            $table->string('image')->nullable(); // صورة الكورس (optional)
            $table->decimal('rating', 3, 2)->default(0); // تقييم الكورس (من 0.00 لحد 5.00)
$table->integer('duration')->default(0);



            $table->text('description');

            $table->string('image_url')->nullable(); // صورة الكورس
            $table->string('video_url')->nullable(); // فيديو الكورس
            $table->enum('status', ["published","pending", "draft"])->default('draft');
            $table->decimal('price', 10, 2);

            $table->string('video_url')->nullable();
            $table->enum('status', ["published","pending"]);
    $table->decimal('price', 8, 2); // السعر (xx.xx)

            $table->foreignId('category_id')->references('id')->on('categories')->nullable()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
