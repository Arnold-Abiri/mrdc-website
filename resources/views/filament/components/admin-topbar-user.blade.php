@if ($user = filament()->auth()->user())
    <button
        class="mrdc-topbar-user-meta"
        type="button"
        aria-label="Open account menu for {{ $user->name }}"
        aria-haspopup="menu"
        x-on:click="$el.previousElementSibling?.querySelector('.fi-dropdown-trigger')?.dispatchEvent(new MouseEvent('mousedown', { bubbles: true, button: 0 }))"
    >
        <strong>{{ $user->name }}</strong>
        <span>{{ $user->hasRole('System Administrator') ? 'System Administrator' : ($user->getRoleNames()->first() ?: 'Council staff') }}</span>
    </button>
@endif
