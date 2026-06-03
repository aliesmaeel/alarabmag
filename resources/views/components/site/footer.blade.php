@props(['variant' => 'full'])

<footer>
    <div class="footer-top">
        <div style="text-align:right;">
            <a href="{{ url('/') }}" class="footer-logo-link" aria-label="مجلة العرب — Al Arab Magazine">
                <img src="{{ asset('logo.png') }}" alt="مجلة العرب — Al Arab Magazine" class="footer-logo-img">
            </a>
            <p class="footer-about">
                @if($variant === 'full')
                    المجلة العربية الأولى التي تحتفي بالإنسان العربي المتميّز في كل مكان. نحكي قصص المؤثرين والفنانين والأطباء ورواد الأعمال الذين يُلهمون الأمة. صادرة من دبي، للعالم العربي.
                @else
                    المجلة العربية الأولى التي تحتفي بالإنسان العربي المتميّز في كل مكان.
                @endif
            </p>
            <div class="footer-socials">
                <a href="#" class="fsoc">𝕏</a>
                <a href="#" class="fsoc">in</a>
                <a href="#" class="fsoc">📷</a>
                <a href="#" class="fsoc">▶</a>
                <a href="#" class="fsoc">📘</a>
            </div>
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
                <li><a href="{{ url('/blogs') }}">المدونات</a></li>
            </ul>
        </div>
        @if($variant === 'full')
            <div class="fcol">
                <div class="fcol-title">المناطق</div>
                <ul>
                    <li><a href="#">الإمارات</a></li>
                    <li><a href="#">السعودية</a></li>
                    <li><a href="#">قطر</a></li>
                    <li><a href="#">الكويت</a></li>
                    <li><a href="#">لبنان</a></li>
                    <li><a href="#">مصر</a></li>
                    <li><a href="#">الأردن</a></li>
                    <li><a href="#">العراق</a></li>
                </ul>
            </div>
            <div class="fcol">
                <div class="fcol-title">المجلة</div>
                <ul>
                    <li><a href="#">عن العرب</a></li>
                    <li><a href="#">هيئة التحرير</a></li>
                    <li><a href="#">الإعلان معنا</a></li>
                    <li><a href="#">الترشيح للمجلة</a></li>
                    <li><a href="#">الفعاليات</a></li>
                    <li><a href="#">وظائف</a></li>
                </ul>
            </div>
            <div class="fcol">
                <div class="fcol-title">الاشتراك</div>
                <ul>
                    <li><a href="#">النشرة المجانية</a></li>
                    <li><a href="#">الاشتراك الرقمي</a></li>
                    <li><a href="#">الطبعة الورقية</a></li>
                    <li><a href="#">اشتراك المؤسسات</a></li>
                    <li><a href="#">هدية الاشتراك</a></li>
                    <li><a href="#">الأعداد السابقة</a></li>
                </ul>
            </div>
        @else
            <div class="fcol">
                <div class="fcol-title">المجلة</div>
                <ul>
                    <li><a href="#">عن العرب</a></li>
                    <li><a href="#">هيئة التحرير</a></li>
                    <li><a href="#">الإعلان معنا</a></li>
                </ul>
            </div>
            <div class="fcol">
                <div class="fcol-title">الاشتراك</div>
                <ul>
                    <li><a href="#">النشرة المجانية</a></li>
                    <li><a href="#">الطبعة الورقية</a></li>
                </ul>
            </div>
        @endif
    </div>
    <div class="footer-bottom">
        <div class="flegal">
            <a href="#">سياسة الخصوصية</a>
            <a href="#">شروط الاستخدام</a>
            @if($variant === 'full')
                <a href="#">إعدادات الكوكيز</a>
            @endif
        </div>
        <div class="fcopy">© 2026 مجلة العرب. جميع الحقوق محفوظة.@if($variant === 'full') مدينة دبي للإعلام، الإمارات العربية المتحدة.@endif</div>
    </div>
</footer>
