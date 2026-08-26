<form wire:submit="submit" class="mt-10 grid gap-6">
    <div class="hidden" aria-hidden="true">
        <input type="text" wire:model="website" tabindex="-1" autocomplete="off">
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <label class="block">
            <span class="font-mono text-[0.65rem] uppercase tracking-[0.18em] text-fg/50">Name</span>
            <input wire:model="name" type="text" required autocomplete="name"
                class="mt-2 block min-h-12 w-full border border-fg/20 bg-transparent px-4 py-3 text-base text-fg placeholder:text-fg/30 focus:border-fg focus:outline-none">
            @error('name') <span class="mt-2 block text-sm text-fg/60">{{ $message }}</span> @enderror
        </label>
        <label class="block">
            <span class="font-mono text-[0.65rem] uppercase tracking-[0.18em] text-fg/50">Email</span>
            <input wire:model="email" type="email" required autocomplete="email"
                class="mt-2 block min-h-12 w-full border border-fg/20 bg-transparent px-4 py-3 text-base text-fg placeholder:text-fg/30 focus:border-fg focus:outline-none">
            @error('email') <span class="mt-2 block text-sm text-fg/60">{{ $message }}</span> @enderror
        </label>
    </div>

    <label class="block">
        <span class="font-mono text-[0.65rem] uppercase tracking-[0.18em] text-fg/50">The project</span>
        <textarea wire:model="message" required rows="5"
            class="mt-2 block w-full border border-fg/20 bg-transparent px-4 py-3 text-base text-fg placeholder:text-fg/30 focus:border-fg focus:outline-none"></textarea>
        @error('message') <span class="mt-2 block text-sm text-fg/60">{{ $message }}</span> @enderror
    </label>

    <div class="flex flex-wrap items-center gap-6">
        <button type="submit" class="inline-flex min-h-12 items-center bg-fg px-6 font-medium text-bg transition-opacity hover:opacity-80">
            Send
        </button>
        @if (session('status'))
            <p class="font-mono text-xs uppercase tracking-[0.16em] text-fg/70">{{ session('status') }}</p>
        @endif
    </div>
</form>
