<x-beverly::app-layout>
    <x-beverly::header title="Validating Carton Box" parentLevel="Carton Box" secondLevel="Validation" style="active" />
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8 w-full">
            @livewire('validation-carton', ['carton' => $carton])
        </div>
    </div>






</x-beverly::app-layout>
