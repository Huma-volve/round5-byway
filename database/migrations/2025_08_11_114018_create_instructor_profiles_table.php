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
        Schema::create('instructor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // $table->text(column: 'bio');
            // $table->text('bio')->default('');

$table->text('bio')->nullable();
            $table->decimal('total_earnings', 10, 2)->default(0);
 // New columns
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('headline');
        $table->text('about');
        $table->json('skills')->nullable();
    $table->json('work_experiences')->nullable();


            // Social Media Links
            $table->string('twitter_link')->nullable();
            $table->string('linkdin_link')->nullable();
            $table->string('youtube_link')->nullable();
            $table->string('facebook_link')->nullable();
            $table->timestamps();
        });
    }

    protected $casts = [
        'skills' => 'array',
        'work_experience' => 'array',
    ];
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_profiles');
    }
};
