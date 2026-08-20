<?php

namespace Database\Seeders;

use App\Models\SongPoll;
use App\Models\SongPollEntry;
use Illuminate\Database\Seeder;

class SongPollSeeder extends Seeder
{
    public function run(): void
    {
        $poll = SongPoll::updateOrCreate(
            ['slug' => 'top-arabic-songs-2025'],
            [
                'title' => 'أفضل 10 أغاني عربية في العام الماضي',
                'title_en' => 'Top 10 Arabic Songs of Last Year',
                'eyebrow' => 'Vote · أغنية العام',
                'subtitle' => 'صوّت لأغنية واحدة — اختيار مجلة العرب لأبرز الإصدارات العربية في 2025. صوت واحد لكل قارئ.',
                'year' => 2025,
                'status' => 'published',
                'starts_at' => now()->subDay(),
                'ends_at' => now()->endOfYear(),
            ],
        );

        $songs = [
            [
                'title' => 'خطفوني',
                'artist' => 'عمرو دياب',
                'country' => 'مصر',
                'flag' => '🇪🇬',
                'excerpt' => 'أغنية العام على أنغامي — دويتو مع جنى دياب تصدر مصر والإمارات.',
                'image_url' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=900&q=80',
                'listen_url' => 'https://www.youtube.com/results?search_query=%D8%AE%D8%B7%D9%81%D9%88%D9%86%D9%8A+%D8%B9%D9%85%D8%B1%D9%88+%D8%AF%D9%8A%D8%A7%D8%A8',
                'votes_count' => 2486,
                'sort_order' => 1,
            ],
            [
                'title' => 'بابا',
                'artist' => 'عمرو دياب',
                'country' => 'مصر',
                'flag' => '🇪🇬',
                'excerpt' => 'أول أغنية صيف 2025 تتخطى 200 مليون مشاهدة على يوتيوب.',
                'image_url' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=900&q=80',
                'listen_url' => 'https://www.youtube.com/results?search_query=%D8%A8%D8%A7%D8%A8%D8%A7+%D8%B9%D9%85%D8%B1%D9%88+%D8%AF%D9%8A%D8%A7%D8%A8',
                'votes_count' => 2310,
                'sort_order' => 2,
            ],
            [
                'title' => 'سيدي يا سيدي',
                'artist' => 'نانسي عجرم',
                'country' => 'لبنان',
                'flag' => '🇱🇧',
                'excerpt' => 'من ألبوم نانسي 11 — ترند عالمي على تيك توك.',
                'image_url' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=900&q=80',
                'listen_url' => 'https://www.youtube.com/results?search_query=%D8%B3%D9%8A%D8%AF%D9%8A+%D9%8A%D8%A7+%D8%B3%D9%8A%D8%AF%D9%8A+%D9%86%D8%A7%D9%86%D8%B3%D9%8A+%D8%B9%D8%AC%D8%B1%D9%85',
                'votes_count' => 1984,
                'sort_order' => 3,
            ],
            [
                'title' => 'يا قلبه',
                'artist' => 'نانسي عجرم',
                'country' => 'لبنان',
                'flag' => '🇱🇧',
                'excerpt' => 'بالاد عاطفي من أبرز إصدارات البوب اللبناني في 2025.',
                'image_url' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&w=900&q=80',
                'listen_url' => 'https://www.youtube.com/results?search_query=%D9%8A%D8%A7+%D9%82%D9%84%D8%A8%D9%87+%D9%86%D8%A7%D9%86%D8%B3%D9%8A+%D8%B9%D8%AC%D8%B1%D9%85',
                'votes_count' => 1742,
                'sort_order' => 4,
            ],
            [
                'title' => 'ما تخزلنيش',
                'artist' => 'إليسا',
                'country' => 'لبنان',
                'flag' => '🇱🇧',
                'excerpt' => 'إليسا الأكثر استماعاً في لبنان — أغنية عن الوفاء بصوتها الأيقوني.',
                'image_url' => 'https://images.unsplash.com/photo-1459749411177-04aa873387ee?auto=format&fit=crop&w=900&q=80',
                'listen_url' => 'https://www.youtube.com/results?search_query=%D9%85%D8%A7+%D8%AA%D8%AE%D8%B2%D9%84%D9%86%D9%8A%D8%B4+%D8%A5%D9%84%D9%8A%D8%B3%D8%A7',
                'votes_count' => 1608,
                'sort_order' => 5,
            ],
            [
                'title' => 'الوعد',
                'artist' => 'ويجز',
                'country' => 'مصر',
                'flag' => '🇪🇬',
                'excerpt' => 'راب مصري في ذروته — من أبرز أغاني العام في المشهد البديل.',
                'image_url' => 'https://images.unsplash.com/photo-1571330735066-03aaa9429d89?auto=format&fit=crop&w=900&q=80',
                'listen_url' => 'https://www.youtube.com/results?search_query=%D8%A7%D9%84%D9%88%D8%B9%D8%AF+%D9%88%D9%8A%D8%AC%D8%B2',
                'votes_count' => 1420,
                'sort_order' => 6,
            ],
            [
                'title' => 'الحب جاني',
                'artist' => 'TUL8TE',
                'country' => 'مصر',
                'flag' => '🇪🇬',
                'excerpt' => 'صوت الجيل الجديد — مزيج إندie وR&B عربي اكتسح قوائم التشغيل.',
                'image_url' => 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=900&q=80',
                'listen_url' => 'https://www.youtube.com/results?search_query=TUL8TE+%D8%A7%D9%84%D8%AD%D8%A8+%D8%AC%D8%A7%D9%86%D9%8A',
                'votes_count' => 1295,
                'sort_order' => 7,
            ],
            [
                'title' => 'كله لنا',
                'artist' => 'زين',
                'country' => 'الأردن',
                'flag' => '🇯🇴',
                'excerpt' => 'من قائمة فوغ العربية لأفضل أغاني 2025 — صوت أردني عابر للحدود.',
                'image_url' => 'https://images.unsplash.com/photo-1487180144351-b8472da7d491?auto=format&fit=crop&w=900&q=80',
                'listen_url' => 'https://www.youtube.com/results?search_query=%D9%83%D9%84%D9%87+%D9%84%D9%86%D8%A7+%D8%B2%D9%8A%D9%86+Zeyne',
                'votes_count' => 1118,
                'sort_order' => 8,
            ],
            [
                'title' => 'كيفك ع فراقي',
                'artist' => 'فضل شاكر ومحمد شاكر',
                'country' => 'لبنان',
                'flag' => '🇱🇧',
                'excerpt' => 'دويتو عائلي أعاد الطرب اللبناني إلى صدارة القوائم.',
                'image_url' => 'https://images.unsplash.com/photo-1429962714451-bb934ecdc4ec?auto=format&fit=crop&w=900&q=80',
                'listen_url' => 'https://www.youtube.com/results?search_query=%D9%83%D9%8A%D9%81%D9%83+%D8%B9+%D9%81%D8%B1%D8%A7%D9%82%D9%8A+%D9%81%D8%B6%D9%84+%D8%B4%D8%A7%D9%83%D8%B1',
                'votes_count' => 974,
                'sort_order' => 9,
            ],
            [
                'title' => 'مكسرات',
                'artist' => 'أحمد سعد',
                'country' => 'مصر',
                'flag' => '🇪🇬',
                'excerpt' => 'أغنية شعبية راقصة من أكثر الإصدارات تداولاً في 2025.',
                'image_url' => 'https://images.unsplash.com/photo-1506157786151-b8491531f063?auto=format&fit=crop&w=900&q=80',
                'listen_url' => 'https://www.youtube.com/results?search_query=%D9%85%D9%83%D8%B3%D8%B1%D8%A7%D8%AA+%D8%A3%D8%AD%D9%85%D8%AF+%D8%B3%D8%B9%D8%AF',
                'votes_count' => 812,
                'sort_order' => 10,
            ],
        ];

        foreach ($songs as $song) {
            $entry = SongPollEntry::query()->firstOrNew([
                'song_poll_id' => $poll->id,
                'title' => $song['title'],
                'artist' => $song['artist'],
            ]);

            $votes = $song['votes_count'];
            unset($song['votes_count']);

            $entry->fill($song + ['song_poll_id' => $poll->id]);

            if (! $entry->exists) {
                $entry->votes_count = $votes;
            }

            $entry->save();
        }

        $this->command?->info('  → Song poll seeded: '.$poll->title);
    }
}
