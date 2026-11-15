@props(['action' => '', 'class' => ''])

<form method="GET" action="{{ $action }}" {{ $attributes->merge(['class' => $class]) }}>
    {{ $slot }}
</form>
