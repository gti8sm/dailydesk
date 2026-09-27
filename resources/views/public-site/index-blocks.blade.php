@extends('layouts.public-site')

@section('title', $tenant->name . ' — Accueil')
@section('meta_description', 'Site officiel de ' . $tenant->name)

@section('content')
<div class="py-8 px-4">
    <div class="max-w-7xl mx-auto">
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
