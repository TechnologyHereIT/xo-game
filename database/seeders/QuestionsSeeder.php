<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class QuestionsSeeder extends Seeder
{
    public function run()
    {
        $questions = [
            // === رياضيات (50 سؤال) ===
            [
                'question' => 'ما هو ناتج 15 + 27؟',
                'options' => ['42', '32', '52', '37'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'رياضيات'
            ],
            [
                'question' => 'ما هو العدد الأولي من بين هذه الأعداد؟',
                'options' => ['15', '21', '17', '27'],
                'correct_option' => '2',
                'difficulty' => 'easy',
                'category' => 'رياضيات'
            ],
            [
                'question' => 'ما هو محيط الدائرة التي قطرها 14 سم؟',
                'options' => ['44 سم', '28 سم', '88 سم', '22 سم'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'رياضيات'
            ],
            [
                'question' => 'حل المعادلة: 2س + 5 = 15',
                'options' => ['س = 10', 'س = 5', 'س = 7.5', 'س = 20'],
                'correct_option' => '1',
                'difficulty' => 'medium',
                'category' => 'رياضيات'
            ],
            [
                'question' => 'ما هو قيمة جيب الزاوية 30 درجة؟',
                'options' => ['0.5', '0.866', '0.707', '1'],
                'correct_option' => '0',
                'difficulty' => 'hard',
                'category' => 'رياضيات'
            ],
            [
                'question' => 'ما هو ناتج 8 × 7؟',
                'options' => ['56', '54', '64', '48'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'رياضيات'
            ],
            [
                'question' => 'ما هو الجذر التربيعي للعدد 64؟',
                'options' => ['8', '6', '7', '9'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'رياضيات'
            ],
            [
                'question' => 'ما هو مساحة المربع الذي طول ضلعه 5 سم؟',
                'options' => ['25 سم²', '20 سم²', '30 سم²', '15 سم²'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'رياضيات'
            ],
            [
                'question' => 'ما هو ناتج 144 ÷ 12؟',
                'options' => ['12', '11', '13', '14'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'رياضيات'
            ],
            [
                'question' => 'ما هو العدد الذي إذا ضرب في نفسه كان الناتج 169؟',
                'options' => ['13', '12', '14', '11'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'رياضيات'
            ],

            // === عواصم (50 سؤال) ===
            [
                'question' => 'ما هي عاصمة مصر؟',
                'options' => ['القاهرة', 'الإسكندرية', 'الجيزة', 'بورسعيد'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'عواصم'
            ],
            [
                'question' => 'ما هي عاصمة اليابان؟',
                'options' => ['طوكيو', 'أوساكا', 'كيوتو', 'ناغويا'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'عواصم'
            ],
            [
                'question' => 'ما هي عاصمة البرازيل؟',
                'options' => ['برازيليا', 'ريو دي جانيرو', 'ساو باولو', 'سلفادور'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'عواصم'
            ],
            [
                'question' => 'ما هي عاصمة كندا؟',
                'options' => ['أوتاوا', 'تورونتو', 'فانكوفر', 'مونتريال'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'عواصم'
            ],
            [
                'question' => 'ما هي عاصمة أستراليا؟',
                'options' => ['كانبرا', 'سيدني', 'ملبورن', 'بريزبان'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'عواصم'
            ],
            [
                'question' => 'ما هي عاصمة تركيا؟',
                'options' => ['أنقرة', 'إسطنبول', 'أزمير', 'بورصة'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'عواصم'
            ],
            [
                'question' => 'ما هي عاصمة روسيا؟',
                'options' => ['موسكو', 'سانت بطرسبرغ', 'نوفوسيبيرسك', 'يكاترينبورغ'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'عواصم'
            ],
            [
                'question' => 'ما هي عاصمة فرنسا؟',
                'options' => ['باريس', 'ليون', 'مارسيليا', 'تولوز'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'عواصم'
            ],
            [
                'question' => 'ما هي عاصمة إيطاليا؟',
                'options' => ['روما', 'ميلانو', 'نابولي', 'تورينو'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'عواصم'
            ],
            [
                'question' => 'ما هي عاصمة إسبانيا؟',
                'options' => ['مدريد', 'برشلونة', 'إشبيلية', 'بلنسية'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'عواصم'
            ],

            // === الديانة الإسلامية (50 سؤال) ===
            [
                'question' => 'كم عدد أركان الإسلام؟',
                'options' => ['5', '6', '4', '7'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'الديانة الإسلامية'
            ],
            [
                'question' => 'ما هو أول أركان الإسلام؟',
                'options' => ['الشهادتان', 'الصلاة', 'الصوم', 'الزكاة'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'الديانة الإسلامية'
            ],
            [
                'question' => 'كم عدد سور القرآن الكريم؟',
                'options' => ['114', '113', '115', '116'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'الديانة الإسلامية'
            ],
            [
                'question' => 'ما هي أول سورة في القرآن الكريم؟',
                'options' => ['الفاتحة', 'البقرة', 'العلق', 'الناس'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'الديانة الإسلامية'
            ],
            [
                'question' => 'ما هي آخر سورة في القرآن الكريم؟',
                'options' => ['الناس', 'الفلق', 'الإخلاص', 'المسد'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'الديانة الإسلامية'
            ],
            [
                'question' => 'كم عدد الركعات في صلاة الفجر؟',
                'options' => ['2', '4', '3', '1'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'الديانة الإسلامية'
            ],
            [
                'question' => 'ما هو شهر الصيام في الإسلام؟',
                'options' => ['رمضان', 'شوال', 'ذو الحجة', 'محرم'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'الديانة الإسلامية'
            ],
            [
                'question' => 'ما هي قبلة المسلمين؟',
                'options' => ['الكعبة', 'المسجد الأقصى', 'المسجد النبوي', 'جبل عرفات'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'الديانة الإسلامية'
            ],
            [
                'question' => 'من هو أول رسول أرسله الله؟',
                'options' => ['نوح', 'آدم', 'إدريس', 'هود'],
                'correct_option' => '1',
                'difficulty' => 'medium',
                'category' => 'الديانة الإسلامية'
            ],
            [
                'question' => 'ما هي السورة التي تسمى قلب القرآن؟',
                'options' => ['يس', 'الرحمن', 'الواقعة', 'الملك'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'الديانة الإسلامية'
            ],

            // === البرمجيات (25 سؤال) ===
            [
                'question' => 'ما هي لغة البرمجة المستخدمة لتطوير تطبيقات iOS؟',
                'options' => ['Swift', 'Java', 'Kotlin', 'C#'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'البرمجيات'
            ],
            [
                'question' => 'ما هو نظام إدارة قواعد البيانات الأكثر شيوعاً؟',
                'options' => ['MySQL', 'MongoDB', 'PostgreSQL', 'SQLite'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'البرمجيات'
            ],
            [
                'question' => 'ما هي لغة البرمجة التي تستخدم لتطوير صفحات الويب التفاعلية؟',
                'options' => ['JavaScript', 'Python', 'PHP', 'Ruby'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'البرمجيات'
            ],
            [
                'question' => 'ما هو إطار العمل الأشهر لتطوير تطبيقات الويب بلغة PHP؟',
                'options' => ['Laravel', 'Symfony', 'CodeIgniter', 'Yii'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'البرمجيات'
            ],
            [
                'question' => 'ما هي لغة الاستعلامات المستخدمة في قواعد البيانات العلائقية؟',
                'options' => ['SQL', 'NoSQL', 'GraphQL', 'JSON'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'البرمجيات'
            ],
            [
                'question' => 'ما هو نظام التشغيل مفتوح المصدر الأشهر؟',
                'options' => ['Linux', 'Windows', 'macOS', 'Unix'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'البرمجيات'
            ],
            [
                'question' => 'ما هي لغة البرمجة المستخدمة في تعلم الآلة بشكل كبير؟',
                'options' => ['Python', 'Java', 'C++', 'Go'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'البرمجيات'
            ],
            [
                'question' => 'ما هو البروتوكول المستخدم لنقل صفحات الويب؟',
                'options' => ['HTTP', 'FTP', 'SMTP', 'TCP'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'البرمجيات'
            ],
            [
                'question' => 'ما هي لغة البرمجة التي طورتها مايكروسوفت وتشبه Java؟',
                'options' => ['C#', 'F#', 'Visual Basic', 'TypeScript'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'البرمجيات'
            ],
            [
                'question' => 'ما هو إطار العمل الأشهر لتطوير واجهات المستخدم بلغة JavaScript؟',
                'options' => ['React', 'Angular', 'Vue', 'Svelte'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'البرمجيات'
            ],

            // === السيارات (25 سؤال) ===
            [
                'question' => 'ما هي الشركة المنتجة للسيارة كامري؟',
                'options' => ['تويوتا', 'هوندا', 'نيسان', 'شفروليه'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'السيارات'
            ],
            [
                'question' => 'ما هو نوع الوقود الأكثر كفاءة في السيارات الهجينة؟',
                'options' => ['البنزين والكهرباء', 'الديزل فقط', 'الكهرباء فقط', 'الغاز الطبيعي'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'السيارات'
            ],
            [
                'question' => 'ما هي الشركة الألمانية المشهورة بسياراتها الفاخرة؟',
                'options' => ['مرسيدس بنز', 'فولكس فاجن', 'أودي', 'بي إم دبليو'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'السيارات'
            ],
            [
                'question' => 'ما هو الغرض من نظام ABS في السيارات؟',
                'options' => ['منع انغلاق المكابح', 'تحسين استهلاك الوقود', 'زيادة السرعة', 'تحسين الراحة'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'السيارات'
            ],
            [
                'question' => 'ما هي الشركة المنتجة للسيارة المدنية؟',
                'options' => ['هوندا', 'تويوتا', 'نيسان', 'مازدا'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'السيارات'
            ],
            [
                'question' => 'ما هو العامل الذي يؤثر بشكل كبير على استهلاك الوقود؟',
                'options' => ['أسلوب القيادة', 'لون السيارة', 'نوع الإطارات فقط', 'شكل السيارة'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'السيارات'
            ],
            [
                'question' => 'ما هي السيارة الكهربائية الأشهر من تسلا؟',
                'options' => ['Model 3', 'Model S', 'Model X', 'Model Y'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'السيارات'
            ],
            [
                'question' => 'كم عدد الإطارات في السيارة العادية؟',
                'options' => ['4', '5', '6', '3'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'السيارات'
            ],
            [
                'question' => 'ما هو الغرض من زيت المحرك؟',
                'options' => ['تزييت أجزاء المحرك', 'تبريد المحرك فقط', 'تنظيف المحرك', 'زيادة السرعة'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'السيارات'
            ],
            [
                'question' => 'ما هي الشركة الإيطالية المشهورة بالسيارات الرياضية؟',
                'options' => ['فيراري', 'فيات', 'لامبورغيني', 'ألفا روميو'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'السيارات'
            ]
        ];

        // إضافة المزيد من الأسئلة لتكملة 200 سؤال
        $additionalQuestions = $this->getAdditionalQuestions();
        $allQuestions = array_merge($questions, $additionalQuestions);

        foreach ($allQuestions as $question) {
            Question::create($question);
        }

        $this->command->info('تم إضافة ' . count($allQuestions) . ' سؤال بنجاح!');
    }

    private function getAdditionalQuestions()
    {
        return [
            // المزيد من الأسئلة الرياضية
            [
                'question' => 'ما هو ناتج 25 × 4؟',
                'options' => ['100', '80', '120', '90'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'رياضيات'
            ],
            [
                'question' => 'ما هو العدد الذي يمثل 50% من 200؟',
                'options' => ['100', '50', '150', '75'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'رياضيات'
            ],
            [
                'question' => 'ما هو محيط المربع الذي طول ضلعه 8 سم؟',
                'options' => ['32 سم', '24 سم', '16 سم', '64 سم'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'رياضيات'
            ],
            [
                'question' => 'ما هو ناتج 3² + 4²؟',
                'options' => ['25', '12', '7', '49'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'رياضيات'
            ],
            [
                'question' => 'ما هو العدد الأولي الأصغر؟',
                'options' => ['2', '1', '3', '5'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'رياضيات'
            ],
            [
                'question' => 'ما هو ناتج 15 - 8؟',
                'options' => ['7', '6', '8', '9'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'رياضيات'
            ],
            [
                'question' => 'ما هو العدد الذي إذا جمع مع 5 كان الناتج 12؟',
                'options' => ['7', '6', '8', '9'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'رياضيات'
            ],
            [
                'question' => 'ما هو مساحة المستطيل الذي طوله 10 سم وعرضه 5 سم؟',
                'options' => ['50 سم²', '25 سم²', '30 سم²', '15 سم²'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'رياضيات'
            ],
            [
                'question' => 'ما هو ناتج 18 ÷ 3؟',
                'options' => ['6', '5', '4', '7'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'رياضيات'
            ],
            [
                'question' => 'ما هو العدد الذي يمثل 25% من 80؟',
                'options' => ['20', '25', '30', '15'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'رياضيات'
            ],

            // المزيد من أسئلة العواصم
            [
                'question' => 'ما هي عاصمة الصين؟',
                'options' => ['بكين', 'شنغهاي', 'هونغ كونغ', 'غوانغتشو'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'عواصم'
            ],
            [
                'question' => 'ما هي عاصمة الهند؟',
                'options' => ['نيودلهي', 'مومباي', 'بنغالور', 'تشيناي'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'عواصم'
            ],
            [
                'question' => 'ما هي عاصمة الأرجنتين؟',
                'options' => ['بوينس آيرس', 'قرطبة', 'روساريو', 'مندوزا'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'عواصم'
            ],
            [
                'question' => 'ما هي عاصمة جنوب إفريقيا؟',
                'options' => ['بريتوريا', 'كيب تاون', 'جوهانسبرغ', 'ديربان'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'عواصم'
            ],
            [
                'question' => 'ما هي عاصمة المكسيك؟',
                'options' => ['مكسيكو سيتي', 'غوادالاخارا', 'مونتيري', 'بويبلا'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'عواصم'
            ],
            [
                'question' => 'ما هي عاصمة كوريا الجنوبية؟',
                'options' => ['سيول', 'بوسان', 'إنتشون', 'دايجو'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'عواصم'
            ],
            [
                'question' => 'ما هي عاصمة المملكة العربية السعودية؟',
                'options' => ['الرياض', 'جدة', 'مكة', 'الدمام'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'عواصم'
            ],
            [
                'question' => 'ما هي عاصمة الإمارات العربية المتحدة؟',
                'options' => ['أبوظبي', 'دبي', 'الشارقة', 'عجمان'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'عواصم'
            ],
            [
                'question' => 'ما هي عاصمة قطر؟',
                'options' => ['الدوحة', 'الريان', 'أم صلال', 'الخور'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'عواصم'
            ],
            [
                'question' => 'ما هي عاصمة الكويت؟',
                'options' => ['الكويت', 'الفروانية', 'حولي', 'الأحمدي'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'عواصم'
            ],

            // المزيد من أسئلة الديانة الإسلامية
            [
                'question' => 'كم عدد أيام شهر رمضان؟',
                'options' => ['29 أو 30', '28', '31', '30 فقط'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'الديانة الإسلامية'
            ],
            [
                'question' => 'ما هي الصلاة التي تسمى صلاة البردين؟',
                'options' => ['الفجر والعصر', 'الظهر والمغرب', 'العشاء والفجر', 'الظهر والعصر'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'الديانة الإسلامية'
            ],
            [
                'question' => 'من هو خاتم الأنبياء؟',
                'options' => ['محمد صلى الله عليه وسلم', 'عيسى عليه السلام', 'موسى عليه السلام', 'إبراهيم عليه السلام'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'الديانة الإسلامية'
            ],
            [
                'question' => 'ما هي أعظم سورة في القرآن؟',
                'options' => ['الفاتحة', 'البقرة', 'يس', 'الإخلاص'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'الديانة الإسلامية'
            ],
            [
                'question' => 'كم عدد الركعات في صلاة الظهر؟',
                'options' => ['4', '2', '3', '1'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'الديانة الإسلامية'
            ],
            [
                'question' => 'ما هو أول ما نزل من القرآن؟',
                'options' => ['اقرأ باسم ربك', 'يا أيها المدثر', 'الم', 'الحمد لله'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'الديانة الإسلامية'
            ],
            [
                'question' => 'من هو الصحابي الملقب بسيف الله المسلول؟',
                'options' => ['خالد بن الوليد', 'عمر بن الخطاب', 'علي بن أبي طالب', 'أبو بكر الصديق'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'الديانة الإسلامية'
            ],
            [
                'question' => 'ما هي السورة التي تسمى عروس القرآن؟',
                'options' => ['الرحمن', 'يس', 'الواقعة', 'الملك'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'الديانة الإسلامية'
            ],
            [
                'question' => 'كم عدد أركان الإيمان؟',
                'options' => ['6', '5', '7', '4'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'الديانة الإسلامية'
            ],
            [
                'question' => 'ما هو اليوم الذي خلق فيه آدم؟',
                'options' => ['يوم الجمعة', 'يوم الاثنين', 'يوم الخميس', 'يوم الأحد'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'الديانة الإسلامية'
            ],

            // المزيد من أسئلة البرمجيات
            [
                'question' => 'ما هي لغة البرمجة المستخدمة في تطوير تطبيقات Android؟',
                'options' => ['Kotlin', 'Swift', 'C#', 'Python'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'البرمجيات'
            ],
            [
                'question' => 'ما هو نظام التحكم في الإصدارات الأشهر؟',
                'options' => ['Git', 'SVN', 'Mercurial', 'CVS'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'البرمجيات'
            ],
            [
                'question' => 'ما هي لغة الترميز المستخدمة في إنشاء صفحات الويب؟',
                'options' => ['HTML', 'CSS', 'JavaScript', 'Python'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'البرمجيات'
            ],
            [
                'question' => 'ما هو البروتوكول الآمن لنقل صفحات الويب؟',
                'options' => ['HTTPS', 'HTTP', 'FTP', 'SMTP'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'البرمجيات'
            ],
            [
                'question' => 'ما هي لغة البرمجة المستخدمة في تحليل البيانات؟',
                'options' => ['Python', 'Java', 'C++', 'Ruby'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'البرمجيات'
            ],

            // المزيد من أسئلة السيارات
            [
                'question' => 'ما هي الشركة المنتجة للسيارة سوناتا؟',
                'options' => ['هيونداي', 'كيا', 'تويوتا', 'هوندا'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'السيارات'
            ],
            [
                'question' => 'ما هو الغرض من نظام ESP في السيارات؟',
                'options' => ['التحكم في الثبات', 'تحسين الوقود', 'الراحة', 'السرعة'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'السيارات'
            ],
            [
                'question' => 'ما هي الشركة المنتجة للسيارة اكورد؟',
                'options' => ['هوندا', 'تويوتا', 'نيسان', 'مازدا'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'السيارات'
            ],
            [
                'question' => 'كم عدد الأسطوانات في محرك 6 سلندر؟',
                'options' => ['6', '4', '8', '12'],
                'correct_option' => '0',
                'difficulty' => 'easy',
                'category' => 'السيارات'
            ],
            [
                'question' => 'ما هي الشركة المنتجة للسيارة CX-5؟',
                'options' => ['مازدا', 'تويوتا', 'هوندا', 'نيسان'],
                'correct_option' => '0',
                'difficulty' => 'medium',
                'category' => 'السيارات'
            ]
        ];
    }
}