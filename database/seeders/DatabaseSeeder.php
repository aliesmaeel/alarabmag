<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Article;
use App\Models\Blog;
use App\Models\Person;
use App\Models\Setting;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdminUser();
        $this->seedArticles();
        $this->seedBlogs();
        $this->seedPeople();
        $this->seedSettings();

        $this->command->info('✅ مجلة العرب — Database seeded successfully!');
    }

    private function seedAdminUser(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@alarab.com'],
            [
                'name'     => 'مدير الموقع',
                'password' => Hash::make('password'),
            ],
        );

        $this->command->line('  → Admin user: admin@alarab.com / password');
    }

    private function seedArticles(): void
    {
        $articles = [
            [
                'title'     => 'أحمد الراشدي: الرجل الذي يُعيد كتابة قواعد الاقتصاد العربي',
                'subtitle'  => 'رجل الأعمال العربي لعام 2026',
                'excerpt'   => 'ثلاث شركات يونيكورن قبل سن الأربعين. جولة استثمارية بـ400 مليون دولار. ورؤية تجعل الوطن العربي المركز المالي التالي للعالم.',
                'body'      => '<p>في مكتب زجاجي يُطل على مركز دبي المالي الدولي، يجلس أحمد الراشدي بثقة الرجل الذي بنى ثلاث شركات يونيكورن قبل أن يبلغ الأربعين.</p><p>مشروعه الأحدث — منصة تقنية مالية تُعيد تشكيل المدفوعات عبر الحدود في منطقة الشرق الأوسط وشمال أفريقيا — أغلق جولة Series C بقيمة 400 مليون دولار في فبراير الماضي. ورفض عرض استحواذ بستة مليارات دولار بعدها بستة أشهر.</p>',
                'category'  => 'أعمال',
                'author'    => 'ليلى منصور',
                'image_url' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=1200&q=85',
                'read_time' => '12 دقيقة',
                'featured'  => true,
                'status'    => 'published',
                'region'    => 'الإمارات',
            ],
            [
                'title'     => 'خالد الزعبي يوقع عقداً تاريخياً مع الهلال بـ180 مليون درهم',
                'subtitle'  => 'أضخم صفقة في كرة القدم الخليجية',
                'excerpt'   => 'صفقة تُعيد رسم خريطة كرة القدم الخليجية وتضع اللاعب العربي في بؤرة الاهتمام العالمي.',
                'body'      => '<p>في خطوة تاريخية هزّت أروقة كرة القدم الخليجية، وقّع المهاجم الإماراتي خالد الزعبي عقداً مع نادي الهلال السعودي بقيمة 180 مليون درهم.</p>',
                'category'  => 'رياضة',
                'author'    => 'عمر الفيصل',
                'image_url' => 'https://images.unsplash.com/photo-1546519638-68e109498ffc?auto=format&fit=crop&w=1200&q=85',
                'read_time' => '6 دقائق',
                'featured'  => true,
                'status'    => 'published',
                'region'    => 'الإمارات',
            ],
            [
                'title'     => 'ريما الحسن تصنع التاريخ في مهرجان كان 2026',
                'subtitle'  => 'جائزة أفضل مخرجة',
                'excerpt'   => 'المخرجة الأردنية تترك جمهور كان في صمت دقيقة كاملة قبل تصفيق استمر تسع دقائق.',
                'body'      => '<p>على خشبة مسرح كان، وقفت ريما الحسن تحمل جائزة أفضل مخرجة وعيناها تلمعان بالفخر والدموع معاً.</p>',
                'category'  => 'فن',
                'author'    => 'زينة الخوري',
                'image_url' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?auto=format&fit=crop&w=1200&q=85',
                'read_time' => '8 دقائق',
                'featured'  => true,
                'status'    => 'published',
                'region'    => 'الأردن',
            ],
            [
                'title'     => 'أول دار أزياء سعودية في أسبوع باريس للأزياء',
                'subtitle'  => 'لحظة تاريخية للموضة العربية',
                'excerpt'   => 'المصمم السعودي يُثبت أن الهوية العربية قادرة على قيادة المشهد العالمي.',
                'body'      => '<p>في قلب العاصمة الفرنسية، وسط صالة تضم صفوة عالم الموضة، خطت عارضات الأزياء بأثواب تمزج الجماليات العربية الأصيلة بالأناقة العالمية.</p>',
                'category'  => 'موضة',
                'author'    => 'أميرة سعيد',
                'image_url' => 'https://images.unsplash.com/photo-1559181567-c3190ca9be46?auto=format&fit=crop&w=1200&q=85',
                'read_time' => '6 دقائق',
                'featured'  => false,
                'status'    => 'published',
                'region'    => 'السعودية',
            ],
            [
                'title'     => 'صفقات العقارات الفاخرة في دبي تتجاوز 68 مليار درهم',
                'subtitle'  => 'تقرير الربع الأول 2026',
                'excerpt'   => 'نمو 34% على أساس سنوي مع تزايد إقبال المستثمرين الدوليين.',
                'body'      => '<p>كشف أحدث تقرير عقاري أن إجمالي الصفقات في قطاع العقارات الفاخرة بدبي تجاوز 68 مليار درهم خلال الربع الأول من 2026.</p>',
                'category'  => 'أعمال',
                'author'    => 'فريق التحرير',
                'image_url' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=1200&q=85',
                'read_time' => '5 دقائق',
                'featured'  => false,
                'status'    => 'published',
                'region'    => 'الإمارات',
            ],
            [
                'title'     => 'لينا الرفاعي: بنت شركة بمليار دولار من عمّان',
                'subtitle'  => 'مقابلة حصرية',
                'excerpt'   => 'رفضت عروض وادي السيليكون وبنت فريقها كاملاً من المواهب العربية.',
                'body'      => '<p>في مكتبها الواسع في عمّان، تستقبلنا لينا الرفاعي بابتسامة هادئة لا تخفي وراءها طموحاً لا حدود له.</p>',
                'category'  => 'أعمال',
                'author'    => 'سارة خليل',
                'image_url' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=1200&q=85',
                'read_time' => '10 دقائق',
                'featured'  => false,
                'status'    => 'published',
                'region'    => 'الأردن',
            ],
        ];

        foreach ($articles as $data) {
            Article::create($data);
        }

        $this->command->line('  → Articles seeded (' . count($articles) . ' records)');
    }

    private function seedBlogs(): void
    {
        $blogs = [
            [
                'title'      => 'لماذا الموسيقى العربية تُسيطر على قوائم سبوتيفاي العالمية الآن؟',
                'excerpt'    => 'الموجة العربية الجديدة ليست صدفة، إنها نتيجة سنوات من البناء الصامت.',
                'body'       => '<p>منذ بداية عام 2025، بدأ المستمعون حول العالم يلاحظون ظاهرة جديدة: أغانٍ عربية تتسلل إلى قوائم التشغيل العالمية.</p>',
                'author'     => 'أميرة سعيد',
                'author_bio' => 'كاتبة ومحللة ثقافية متخصصة في الموسيقى العربية',
                'image_url'  => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=1200&q=85',
                'tags'       => 'موسيقى,ثقافة,سبوتيفاي,فن عربي',
                'featured'   => true,
                'status'     => 'published',
            ],
            [
                'title'      => 'كيف تبني علامتك التجارية الشخصية في الفضاء العربي الرقمي',
                'excerpt'    => 'دليل عملي للمؤثرين الطموحين في العالم العربي.',
                'body'       => '<p>في عالم تتزاحم فيه الأصوات وتتشابك فيه الهويات، أصبح بناء علامة شخصية قوية ضرورة لا رفاهية.</p>',
                'author'     => 'خالد الشمراني',
                'author_bio' => 'خبير تسويق رقمي ومؤثر رقمي',
                'image_url'  => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=1200&q=85',
                'tags'       => 'مؤثرون,تسويق,رقمي,علامة شخصية',
                'featured'   => false,
                'status'     => 'published',
            ],
            [
                'title'      => 'الطبيب العربي في الخارج: بين الاغتراب والإنجاز',
                'excerpt'    => 'قصص أطباء عرب غيّروا مسار الطب الحديث من خارج الوطن.',
                'body'       => '<p>لم يكن د. خالد العمري يتوقع يوم تخرجه من كلية الطب في الرياض أن اسمه سيُذكر يوماً ما في أروقة مايو كلينيك.</p>',
                'author'     => 'د. رانيا مصطفى',
                'author_bio' => 'طبيبة أطفال وكاتبة، زميلة جامعة هارفارد',
                'image_url'  => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=1200&q=85',
                'tags'       => 'أطباء,اغتراب,طب,إنجاز عربي',
                'featured'   => false,
                'status'     => 'published',
            ],
        ];

        foreach ($blogs as $data) {
            Blog::create($data);
        }

        $this->command->line('  → Blogs seeded (' . count($blogs) . ' records)');
    }

    private function seedPeople(): void
    {
        $people = [
            // Influencers
            ['name' => 'نورا المنصوري',    'name_en' => 'Nora Mansouri',        'role' => 'مؤثرة في الموضة ونمط الحياة',      'category' => 'influencer', 'country' => 'الإمارات',  'flag' => '🇦🇪', 'image_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80', 'excerpt' => 'حوّلت شغفها بالموضة إلى إمبراطورية رقمية عابرة للحدود.', 'handle' => '@nora.mansouri',  'platform' => 'إنستغرام', 'followers' => '12.4M', 'stat' => '12.4M', 'stat_label' => 'متابع على إنستغرام', 'featured' => true],
            ['name' => 'خالد الشمراني',   'name_en' => 'Khalid Shamrani',      'role' => 'مؤثر في التقنية وريادة الأعمال',  'category' => 'influencer', 'country' => 'السعودية', 'flag' => '🇸🇦', 'image_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80', 'excerpt' => 'يُبسّط عالم التقنية لملايين المتابعين العرب.', 'handle' => '@k.shamrani',     'platform' => 'يوتيوب',   'followers' => '8.9M',  'stat' => '8.9M',  'stat_label' => 'متابع عبر المنصات',   'featured' => true],
            ['name' => 'سارة الخوري',     'name_en' => 'Sara Khoury',          'role' => 'مؤثرة في الطبخ والثقافة',         'category' => 'influencer', 'country' => 'لبنان',    'flag' => '🇱🇧', 'image_url' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=600&q=80', 'excerpt' => 'تُعيد اكتشاف المطبخ العربي وتقدّمه للعالم بعيون جديدة.', 'handle' => '@sara.khoury',    'platform' => 'يوتيوب',   'followers' => '6.2M',  'stat' => '6.2M',  'stat_label' => 'متابع على يوتيوب',    'featured' => true],
            ['name' => 'محمد حسين',        'name_en' => 'Mohamed Hussein',      'role' => 'مؤثر في الرياضة واللياقة',        'category' => 'influencer', 'country' => 'مصر',      'flag' => '🇪🇬', 'image_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=600&q=80', 'excerpt' => 'ألهم ملايين الشباب العربي للاهتمام بصحتهم ولياقتهم.', 'handle' => '@mo.hussein.fit', 'platform' => 'تيك توك',  'followers' => '5.7M',  'stat' => '5.7M',  'stat_label' => 'متابع على تيك توك',   'featured' => false],

            // Artists
            ['name' => 'نور خليل',        'name_en' => 'Nour Khalil',          'role' => 'راقصة ومصممة رقص معاصر',          'category' => 'artist', 'country' => 'لبنان',    'flag' => '🇱🇧', 'image_url' => 'https://images.unsplash.com/photo-1518834107812-67b0b7c58434?auto=format&fit=crop&w=600&q=80', 'excerpt' => 'قاعة كارنيجي وأوبرا باريس. تعيد الإبداع إلى مسارح بيروت.', 'featured' => true],
            ['name' => 'طارق نور',        'name_en' => 'Tarek Nour',           'role' => 'منتج موسيقي',                      'category' => 'artist', 'country' => 'مصر',      'flag' => '🇪🇬', 'image_url' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=600&q=80', 'excerpt' => 'يدمج المقامات العربية بالإلكترونيك. سبوتيفاي تسمّيه الأكثر استماعاً.', 'featured' => true],
            ['name' => 'ريما الحسن',      'name_en' => 'Rima Al-Hassan',       'role' => 'مخرجة سينمائية',                   'category' => 'artist', 'country' => 'الأردن',   'flag' => '🇯🇴', 'image_url' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?auto=format&fit=crop&w=600&q=80', 'excerpt' => 'جائزة أفضل مخرجة في كان 2026. فيلمها القادم الأكثر ترقباً.', 'featured' => true],
            ['name' => 'هدى الرشيد',      'name_en' => 'Huda Al-Rashid',       'role' => 'فنانة تشكيلية',                    'category' => 'artist', 'country' => 'السعودية', 'flag' => '🇸🇦', 'image_url' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=600&q=80', 'excerpt' => 'منحوتتها في آرت بازل بيعت في 90 دقيقة. كريستيز تنتظر.', 'featured' => false],

            // Doctors
            ['name' => 'د. خالد العمري',  'name_en' => 'Dr. Khalid Al-Omari',  'role' => 'جراح أعصاب',                       'category' => 'doctor', 'country' => 'السعودية', 'flag' => '🇸🇦', 'image_url' => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=600&q=80', 'excerpt' => 'الطبيب العربي الذي يُعالج المستحيل في مايو كلينيك.', 'hospital' => 'مايو كلينيك · أمريكا',           'specialty' => 'جراحة الأعصاب',        'badge' => 'أفضل طبيب في العالم 2025', 'featured' => true],
            ['name' => 'د. سمر النجار',   'name_en' => 'Dr. Samar Al-Najjar',  'role' => 'أخصائية أورام',                    'category' => 'doctor', 'country' => 'لبنان',    'flag' => '🇱🇧', 'image_url' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=600&q=80', 'excerpt' => 'حققت اكتشافاً علمياً يُغير قواعد علاج السرطان.', 'hospital' => 'رويال مارسدن · لندن',          'specialty' => 'أورام وعلاج السرطان', 'badge' => 'اكتشاف علمي 2026',         'featured' => true],
            ['name' => 'د. عمر بن راشد',  'name_en' => 'Dr. Omar Bin Rashed',  'role' => 'طبيب زراعة أعضاء',                 'category' => 'doctor', 'country' => 'الإمارات', 'flag' => '🇦🇪', 'image_url' => 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=600&q=80', 'excerpt' => '100 عملية زراعة أعضاء ناجحة في كليفلاند كلينيك.', 'hospital' => 'كليفلاند كلينيك · أبوظبي',       'specialty' => 'زراعة الأعضاء',       'badge' => '100 عملية ناجحة',          'featured' => false],
            ['name' => 'د. رانيا مصطفى', 'name_en' => 'Dr. Rania Mustafa',    'role' => 'طبيبة أطفال وباحثة',               'category' => 'doctor', 'country' => 'مصر',      'flag' => '🇪🇬', 'image_url' => 'https://images.unsplash.com/photo-1594824476967-48c8b964273f?auto=format&fit=crop&w=600&q=80', 'excerpt' => 'زميلة هارفارد وأصغر أستاذة عربية في الكلية الطبية.', 'hospital' => 'جامعة هارفارد الطبية',            'specialty' => 'طب الأطفال',          'badge' => 'زميلة هارفارد',            'featured' => false],

            // Business
            ['name' => 'أحمد الراشدي',         'name_en' => 'Ahmad Al-Rashidi',       'role' => 'مؤسس ورئيس تنفيذي', 'category' => 'business', 'country' => 'السعودية', 'flag' => '🇸🇦', 'image_url' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=600&q=80', 'excerpt' => 'ثلاث شركات يونيكورن قبل الأربعين. يعيد تشكيل مشهد المال.', 'company' => 'PayMENA',       'net_worth' => '$4.8B', 'stat' => '$4.8B', 'stat_label' => 'صافي الثروة', 'featured' => true],
            ['name' => 'الشيخة نورة القاسمي', 'name_en' => 'Sheikha Nora Al-Qasimi', 'role' => 'مديرة عامة',         'category' => 'business', 'country' => 'الإمارات',  'flag' => '🇦🇪', 'image_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80', 'excerpt' => 'أقوى امرأة في الأسهم الخاصة الخليجية.', 'company' => 'خليج كابيتال', 'net_worth' => '$3.2B', 'stat' => '$3.2B', 'stat_label' => 'صافي الثروة', 'featured' => true],
            ['name' => 'لينا الرفاعي',         'name_en' => 'Lina Al-Rifai',          'role' => 'مؤسسة مشاركة',       'category' => 'business', 'country' => 'الأردن',    'flag' => '🇯🇴', 'image_url' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=600&q=80', 'excerpt' => 'بنت شركة بمليار دولار من عمّان دون أن تغادر الوطن.', 'company' => 'Souq.ai',      'net_worth' => '$1.4B', 'stat' => '$1.4B', 'stat_label' => 'تقييم الشركة', 'featured' => false],
        ];

        foreach ($people as $data) {
            Person::create($data);
        }

        $this->command->line('  → People seeded (' . count($people) . ' records)');
    }

    private function seedSettings(): void
    {
        $settings = [
            'site_name'        => 'العرب',
            'site_name_en'     => 'AL ARAB',
            'site_tagline'     => 'قوة. تميّز. الإنسان العربي.',
            'site_description' => 'المجلة العربية الأولى للإنسان العربي المتميّز',
            'current_issue'    => 'العدد الثاني عشر · ربيع 2026',
            'editor_email'     => 'editor@alarab.com',
            'instagram'        => '@alarab_magazine',
            'twitter'          => '@alarabmag',
            'youtube'          => '@alarabmagazine',
            'tiktok'           => '@alarab',
            'whatsapp'         => '+971500000000',
            'facebook'         => 'alarabmagazine',
        ];

        Setting::setMany($settings);

        $this->command->line('  → Settings seeded (' . count($settings) . ' keys)');
    }
}
