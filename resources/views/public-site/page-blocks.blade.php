@extends('layouts.public-site')

@section('title', $page->title . ' — ' . $tenant->name)
@section('meta_description', $page->meta_description ?: $page->title)

@section('content')
<div class="py-8 px-4">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $page->title }}</h1>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            @foreach($renderedBlocks as $block)
            <div class="{{ $block['width_class'] }} col-span-12">
                {!! $block['html'] !!}
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
