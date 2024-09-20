<div class="w-1/2 m-auto">
    <div class="card bg-base-100 w-96 shadow-xl dark:bg-secondary-800">
        <div class="card-body">
            <h2 class="card-title dark:text-white">Scan Box Barcode</h2>
            <form wire:submit="check" id="boxCheck">
                <x-input icon="qr-code" label="Box Code" placeholder="Scan the barcode" wire:model.live="form.box_code"
                    autofocus autocomplete="off" index="0" class="p-4" />
                @if ($showExtraForm === true)
                    <x-card>

                        <x-select label="PO" :async-data="route('filament.beverly.api.carton-po', ['box_code' => $form->box_code])" option-value="po" option-label="po"
                            wire:model.live="form.po" index="1" class="p-4" />
                        @if ($form->po !== null)
                            <x-select label="Carton Number" :async-data="route('filament.beverly.api.carton-number', [
                                'box_code' => $form->box_code,
                                'po' => $form->po,
                            ])" option-value="carton_number"
                                option-label="carton_number" wire:model="form.carton_number" index="2"
                                class="p-4" />
                        @endif
                    </x-card>

                @endif

                {{-- <x-slot name="footer">
            <x-button label="Reset" negative href='/beverly/carton/check' />
            <x-button label="Check" primary type="submit" spinner="check" wire:click="check"/>
        </x-slot:actions> --}}
            </form>
            <div class="card-actions justify-end">
                <x-button label="Reset" negative href='/beverly/carton/check' />
                <x-button label="Check" primary type="submit" spinner="check" wire:click="check" />
            </div>
        </div>
    </div>
    {{-- <x-card title="Search Carton Box" subtitle="Scan the carton box code here."> --}}

    {{-- </x-card> --}}

</div>


<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('swal', (event) => {
            const data = event
            swal.fire({
                toast: data[0]['toast'],
                position: data[0]['position'] ?? "middle-center",
                icon: data[0]['icon'],
                title: data[0]['title'],
                text: data[0]['text'],
                showConfirmButton: data[0]['showConfirmButton'] ?? true,
                timer: data[0]['timer'] ?? 3000,
                timerProgressBar: data[0]['timerProgressBar'] ?? false,
            })
        })
    })
</script>
