@props(['variant' => 'full'])

@php
    use App\Support\SiteSettings;

    $socialLinks = SiteSettings::socialLinks();
@endphp

<footer>
    <div class="footer-top">
        <div style="text-align:right;">
            <a href="{{ url('/') }}" class="footer-logo-link" aria-label="مجلة العرب — Al Arab Magazine">
                <img src="{{ asset('logo.png') }}" alt="مجلة العرب — Al Arab Magazine" class="footer-logo-img">
            </a>
            <p class="footer-about">
                @if($variant === 'full')
                    <strong>مجلة العرب</strong> (Al Arab Magazine) — المجلة العربية الأولى التي تحتفي بالإنسان العربي المتميّز في كل مكان. نحكي قصص المؤثرين والفنانين والأطباء ورواد الأعمال الذين يُلهمون الأمة. صادرة من دبي، للعالم العربي.
                @else
                    <strong>مجلة العرب</strong> — المجلة العربية الأولى التي تحتفي بالإنسان العربي المتميّز في كل مكان.
                @endif
            </p>
            @if ($socialLinks !== [])
                <div class="footer-socials">
                    @foreach ($socialLinks as $link)
                        <a href="{{ $link['url'] }}" class="fsoc" target="_blank" rel="noopener noreferrer" aria-label="{{ $link['platform'] }}">{{ $link['label'] }}</a>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="fcol">
            <div class="fcol-title">الأقسام</div>
            <ul>
                <li><a href="{{ url('/influencers') }}">المؤثرون العرب</a></li>
                <li><a href="{{ url('/artists') }}">الفنانون العرب</a></li>
                <li><a href="{{ route('business.index') }}">الأعمال العربية</a></li>
                <li><a href="{{ url('/doctors') }}">أطباء عرب</a></li>
                @if($variant === 'full')
                    <li><a href="{{ route('fashion.index') }}">الموضة العربية</a></li>
                @endif
                <li><a href="{{ url('/news') }}">الأخبار</a></li>
                @if(\App\Support\HomeSections::hasInterviews())
                    <li><a href="{{ route('interviews.index') }}">المقابلات</a></li>
                @endif
                <li><a href="{{ url('/blogs') }}">المدونات</a></li>
            </ul>
        </div>
        <div class="fcol">
            <div class="fcol-title">المجلة</div>
            <ul>
                <li><a href="{{ route('about') }}">عن العرب</a></li>
                <li><a href="{{ route('editorial') }}">هيئة التحرير</a></li>
                <li><a href="{{ route('advertise') }}">الإعلان معنا</a></li>
                <li><a href="{{ route('contact') }}">اتصل بنا</a></li>
            </ul>
        </div>
        @if($variant === 'full')
            <div class="fcol">
                <div class="fcol-title">الاشتراك</div>
                <ul>
                    <li><a href="{{ url('/#newsletter') }}">النشرة المجانية</a></li>
                </ul>
            </div>
        @else
            <div class="fcol">
                <div class="fcol-title">الاشتراك</div>
                <ul>
                    <li><a href="{{ url('/#newsletter') }}">النشرة المجانية</a></li>
                </ul>
            </div>
        @endif
    </div>
    <div class="footer-bottom">
        <div class="flegal">
            <a href="{{ route('privacy') }}">سياسة الخصوصية</a>
            <a href="{{ route('terms') }}">شروط الاستخدام</a>
            <a href="{{ route('privacy') }}#cookies">إعدادات الكوكيز</a>
        </div>
        <div class="fcopy">© 2026 مجلة العرب. جميع الحقوق محفوظة.@if($variant === 'full') مدينة دبي للإعلام، الإمارات العربية المتحدة.@endif</div>
    </div>
</footer>
