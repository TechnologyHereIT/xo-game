<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function random()
    {
        $question = Question::inRandomOrder()->first();
        
        if (!$question) {
            return response()->json([
                'error' => 'لا توجد أسئلة متاحة'
            ], 404);
        }

        return response()->json([
            'id' => $question->id,
            'question' => $question->question,
            'options' => $question->options,
            'correct_option' => $question->correct_option,
            'difficulty' => $question->difficulty,
            'category' => $question->category
        ]);
    }

    public function createSampleQuestions()
    {
        // حذف الأسئلة القديمة إذا وجدت
        Question::truncate();

        // تشغيل الـ seeder
        \Artisan::call('db:seed', ['--class' => 'QuestionsSeeder']);

        return response()->json([
            'message' => 'تم إنشاء 200 سؤال بنجاح في مجالات مختلفة'
        ]);
    }
}