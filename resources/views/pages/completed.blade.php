<x-beverly::app-layout>

    <div class="hero min-h-screen">
        <div class="hero-overlay bg-opacity-60">
        </div>
        <div class="hero-content text-neutral-content text-center">
            <div class="max-w-screen">
                <h1 class="mb-5 text-5xl font-bold">📦 This carton box is completed!</h1>
                <h1 class="mb-5 text-5xl font-bold text-primary-500">{{ $carton->box_code }}</h1>
                <p class="mb-5">
                    <p class="mt-3 text-lg">Completed by <span class="font-bold text-error">{{ $user->name }}</span> at <span class="font-bold text-error">{{ \Carbon\Carbon::parse($carton->completed_at)->format('d M Y H:i:s') }}</span>
                    </p>
                </p>
                <x-button icon="arrow-left" primary label="Back" href="/beverly/carton/check" class="mt-6"/>
            </div>
        </div>
    </div>
</x-beverly::app-layout>
