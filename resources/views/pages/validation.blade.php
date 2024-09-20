<x-beverly::app-layout>
    <x-beverly::header title="Validating Carton Box" parentLevel="Carton Box" secondLevel="Validation" style="active" />
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 w-full max-w-2xl">
            @livewire('validation-carton', ['carton' => $carton])
        </div>
    </div>






</x-beverly::app-layout>
