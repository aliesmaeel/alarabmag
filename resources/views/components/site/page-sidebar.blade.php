@props(['activeNav' => null])

@php
    use App\Support\HomeSections;

    $onHome = request()->routeIs('home');
    $items = HomeSections::forSidebar($onHome);
@endphp

<aside class="page-sidebar" id="pageSidebar" aria-label="تنقل الأقسام">
    <div class="page-sidebar-head">
        <div class="page-sidebar-label">الأقسام</div>
        <button
            type="button"
            class="page-sidebar-collapse"
            id="sidebarCollapse"
            aria-label="طي القائمة"
            aria-expanded="true"
            title="طي القائمة"
        >
            <span aria-hidden="true">‹</span>
        </button>
    </div>
    <nav class="page-sidebar-nav">
        @foreach($items as $item)
            <a
                href="{{ $item['href'] }}"
                class="page-sidebar-link @if($activeNav === $item['key']) active @endif"
                @if($item['is_anchor']) data-section="{{ $item['id'] }}" @endif
                title="{{ $item['label'] }}"
            >
                <span class="page-sidebar-dot"></span>
                <span class="page-sidebar-text">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>

<button
    type="button"
    class="page-sidebar-expand"
    id="sidebarExpand"
    aria-label="إظهار القائمة"
    title="إظهار القائمة"
    hidden
>
    <span aria-hidden="true">›</span>
</button>

<button type="button" class="page-sidebar-toggle" id="sidebarToggle" aria-label="فتح قائمة الأقسام">
    <span></span><span></span><span></span>
</button>
<div class="page-sidebar-backdrop" id="sidebarBackdrop"></div>
