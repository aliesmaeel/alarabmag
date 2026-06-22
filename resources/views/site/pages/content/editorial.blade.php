@php
    use App\Support\EditorialPage;
@endphp

<h2>{{ EditorialPage::get('editorial_team_title') }}</h2>
<p>{{ EditorialPage::get('editorial_team_body') }}</p>

<h2>{{ EditorialPage::get('editorial_lead_editor_title') }}</h2>
<p><strong>{{ EditorialPage::get('editorial_lead_editor_name') }}</strong> — {{ EditorialPage::get('editorial_lead_editor_bio') }}</p>

<h2>{{ EditorialPage::get('editorial_news_title') }}</h2>
<p>
    @foreach (EditorialPage::newsTeam() as $member)
        <strong>{{ $member['name'] }}</strong> — {{ $member['role'] }}@if (! $loop->last)<br>@endif
    @endforeach
</p>

<h2>{{ EditorialPage::get('editorial_blogs_title') }}</h2>
<p>{{ EditorialPage::get('editorial_blogs_body') }}</p>

<h2>{{ EditorialPage::get('editorial_contact_title') }}</h2>
<p>{{ EditorialPage::get('editorial_contact_intro') }}<br>
<a href="mailto:{{ EditorialPage::get('editorial_contact_email') }}">{{ EditorialPage::get('editorial_contact_email') }}</a></p>

<p><a href="{{ route('about') }}">عن المجلة ←</a> · <a href="{{ route('contact') }}">اتصل بنا ←</a></p>
