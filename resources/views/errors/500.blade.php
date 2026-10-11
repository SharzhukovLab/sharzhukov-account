<x-layouts.public :title="'500 — ' . config('app.name')">
    <x-lab::error-page
        :code="500"
        title="Something broke"
        message="Our server tripped over its own feet. We've been notified — try again in a moment."
        :actions="[
            ['label' => '← Back to home', 'url' => '/', 'variant' => 'primary'],
            ['label' => 'Main site ↗', 'url' => 'https://sharzhukov.com', 'variant' => 'secondary', 'external' => true],
        ]"
    />
</x-layouts.public>