@props(['date', 'format' => 'j F Y', 'prefix' => ''])
@php($dt = $date instanceof \DateTimeInterface ? \Illuminate\Support\Carbon::instance($date) : ($date ? \Illuminate\Support\Carbon::parse($date) : null))
@if ($dt)<time {{ $attributes->merge(['datetime' => $dt->toIso8601String()]) }}>{{ $prefix }}{{ $dt->locale('ar')->translatedFormat($format) }}</time>@endif
