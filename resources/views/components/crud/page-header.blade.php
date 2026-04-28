@props([
    'title',
    'subtitle' => null,
])

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h1 class="h3 mb-1 section-title">{{ $title }}</h1>
        @if ($subtitle)
            <p class="text-muted mb-0">{{ $subtitle }}</p>
        @endif
    </div>

    @if (trim($actions ?? '') !== '')
        <div class="d-flex gap-2">
            {{ $actions }}
        </div>
    @endif
</div>
