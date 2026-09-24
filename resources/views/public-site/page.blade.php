@extends('layouts.public-site')

@section('title', $page->title . ' — ' . $tenant->name)
@section('meta_description', $page->meta_description ?: $page->title)

@section('content')
<section class="py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg p-8 md:p-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $page->title }}</h1>
            <div class="prose-public">
                {!! $page->content !!}
            </div>
        </div>
    </div>
</section>
@endsection
