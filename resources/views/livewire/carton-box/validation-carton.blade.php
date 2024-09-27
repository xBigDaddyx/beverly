       <div class="grid grid-cols-6 gap-20">
           <div class="col-span-2">
               <div class="grid grid-cols-2 gap-4">
                   <div
                       class="card bg-base-100 shadow-xl m-auto w-full dark:bg-secondary-800 dark:text-white col-span-2">
                       <div class="card-body">
                           <form wire:submit="validation" x-data x-init="$refs.code.focus()">
                               @if (session()->get('carton.type') === 'SOLID')
                                   @if ($completed)
                                       <x-input x-ref="code" icon="qr-code" autofocus label="Polybag barcode"
                                           wire:model="form.polybag_barcode" hint="Please scan polybag barcode now."
                                           disabled placeholder="scan barcode here" />
                                   @else
                                       <x-input x-ref="code" icon="qr-code" autofocus label="Polybag barcode"
                                           wire:model="form.polybag_barcode" hint="Please scan polybag barcode now."
                                           placeholder="scan barcode here" />
                                   @endif
                               @elseif (session()->get('carton.type') === 'RATIO' || session()->get('carton.type') === 'MIX')
                                   @if ($completed)
                                       <x-input x-ref="code" icon="qr-code" autofocus label="Garment tag barcode"
                                           wire:model="form.tag_barcode" hint="Please scan garment tag barcode now."
                                           placeholder="scan barcode here" disable />
                                   @elseif (session()->get('polybag.status') === 1)
                                       <x-input x-ref="code" icon="qr-code" autofocus label="Polybag/Carton barcode"
                                           wire:model="form.polybag_barcode"
                                           hint="Please scan polybag/carton barcode now."
                                           placeholder="scan barcode here" />
                                   @else
                                       <x-input x-ref="code" icon="qr-code" autofocus label="Garment tag barcode"
                                           wire:model="form.tag_barcode" hint="Please scan garment tag barcode now."
                                           placeholder="scan barcode here" />
                                   @endif
                               @endif
                           </form>
                           <div class="card-actions justify-end">
                               @if ($showTable)
                                   <x-button label="Hide Table" icon="window" secondary spinner="save"
                                       wire:click="toggleShowTable" />
                               @else
                                   <x-button label="Show Table" icon="window" info spinner="save"
                                       wire:click="toggleShowTable" />
                               @endif
                               <x-button label="Reset" icon="arrow-path" negative spinner="save" :link="route('beverly.check.carton.release')" />
                           </div>
                       </div>
                   </div>

               </div>

           </div>
           <div class="col-span-4 gap-4">
               <div class="grid grid-cols-4 gap-4">

                   @if ($carton->type === 'RATIO' || $carton->type === 'MIX')
                       @livewire('validation-stat', ['carton' => $carton, 'type' => $type, 'polybags' => $polybags, 'tags' => $tags])
                   @else
                       @livewire('validation-stat', ['carton' => $carton, 'type' => $type, 'polybags' => $polybags])
                   @endif
                   @livewire('validation-attribute', ['carton' => $carton, 'type' => $type, 'polybags' => $polybags])
                   <div
                       class="card shadow-xl bg-warning-300 dark:bg-primary-600 dark:text-white col-span-2 animate-pulse animate-infinite">
                       <div class="card-body">
                           <h2 class="card-title">
                               <x-heroicon-o-exclamation-triangle
                                   class="stroke-current shrink-0 h-4 w-4" />{{ $carton->type }}
                               <div class="badge badge-secondary">Principle</div>
                           </h2>
                           @if ($carton->type === 'MIX' || $carton->type === 'RATIO')
                               <p>After finish validating garment tag, close by scanning polybag barcode or carton
                                   box barcode.</p>
                           @else
                               <p>Validating each polybags inside the carton box. Make sure each polybag have same
                                   attribute
                                   size/color.</p>
                           @endif

                       </div>
                   </div>
                   @if ($showTable)
                       <div class="col-span-4">
                           @livewire('list-solid-polybag', ['carton' => $carton])
                       </div>
                   @endif
               </div>

           </div>


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
