<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            // إضافة الحقول الجديدة
            $table->json('options')->nullable()->after('question');
            $table->string('correct_option')->nullable()->after('options');
            $table->dropColumn('correct_answer'); // إزالة الحقل القديم
        });
    }

    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['options', 'correct_option']);
            $table->boolean('correct_answer')->default(false);
        });
    }
};