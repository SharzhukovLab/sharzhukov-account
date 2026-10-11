<x-layouts.public :title="'403 — ' . config('app.name')">
    <x-lab::error-page
        :code="403"
        title="Access denied"
        message="You don't have permission to be here. If that seems wrong, sign in or contact us."
        :actions="[
            ['label' => '← Back to home', 'url' => '/', 'variant' => 'primary'],
            ['label' => 'Main site ↗', 'url' => 'https://sharzhukov.com', 'variant' => 'secondary', 'external' => true],
        ]"
    />
</x-layouts.public>