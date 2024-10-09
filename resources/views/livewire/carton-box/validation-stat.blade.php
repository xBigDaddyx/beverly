<div class="hidden md:block col-span-4" wire.poll.keep-alive>
    @if ($carton->type === 'RATIO' || $carton->type === 'MIX')
        <div class="hero col-span-4 ">
            <div class="hero-overlay bg-opacity-40 rounded-xl shadow-md"></div>
            <div class="hero-content text-neutral-content text-center">
                <div class="mx-auto">
                    <div class="stats bg-transparent text-white">
                        <div class="stat">
                            <div class="stat-figure text-error">
                                <x-heroicon-o-clipboard-document-list class="inline-block w-8 h-8 stroke-current" />

                            </div>
                            <div class="stat-title text-white dark:text-white">Carton Number</div>
                            <div class="stat-value text-md">{{ $carton->carton_number }}</div>
                            <div class="stat-desc text-error">Box Code : {{ $carton->box_code }}</div>
                        </div>
                        <div class="stat">
                            <div class="stat-figure text-secondary">
                                <x-heroicon-o-document-duplicate class="inline-block w-8 h-8 stroke-current" />
                            </div>
                            <div class="stat-title text-white dark:text-white font-bold">Polybags Tags
                            </div>
                            <div class="stat-value text-secondary animate-bounce animate-infinite">{{ $count }}
                            </div>
                            <div class="stat-desc text-white">Validated</div>
                        </div>
                        <div class="stat">
                            <div class="stat-figure text-secondary">
                                <x-heroicon-o-tag class="inline-block w-8 h-8 stroke-current" />
                            </div>
                            <div class="stat-title text-white dark:text-white font-bold">Garment Tags</div>
                            <div class="stat-value text-secondary animate-bounce animate-infinite">{{ $tags_count }}
                            </div>
                            <div class="stat-desc text-white">Validated</div>
                        </div>
                        <div class="stat">
                            <div class="stat-figure text-success">
                                <x-heroicon-o-swatch class="inline-block w-8 h-8 stroke-current" />
                            </div>
                            <div class="stat-title text-white dark:text-white text-xl font-bold">Purchase
                                Order</div>
                            <div class="stat-value text-success text-sm">{{ $carton->packingList->po }}</div>
                            <div class="stat-desc text-white">{{ $carton->packingList->buyer->name }}</div>
                        </div>
                        <div class="stat">
                            <div class="stat-figure text-info">
                                <x-heroicon-o-inbox-stack class="inline-block w-8 h-8 stroke-current" />
                            </div>
                            <div class="stat-title text-white dark:text-white text-xl font-bold">Master
                                Order</div>
                            <div class="stat-value text-info text-sm">{{ $carton->packingList->contract_no }}
                            </div>

                        </div>
                    </div>



                </div>
            </div>
        </div>
    @else
        <div class="hero col-span-4 ">
            <div class="hero-overlay bg-opacity-40 rounded-xl shadow-md"></div>
            <div class="hero-content text-neutral-content text-center">
                <div class="mx-auto">
                    <div class="stats bg-transparent text-white">
                        <div class="stat">
                            <div class="stat-figure text-error">
                                <x-heroicon-o-clipboard-document-list class="inline-block w-8 h-8 stroke-current" />

                            </div>
                            <div class="stat-title text-white dark:text-white">Carton Number</div>
                            <div class="stat-value text-md">{{ $carton->carton_number }}</div>
                            <div class="stat-desc text-error">Box Code : {{ $carton->box_code }}</div>
                        </div>
                        <div class="stat">
                            <div class="stat-figure text-secondary">
                                <x-heroicon-o-document-duplicate class="inline-block w-8 h-8 stroke-current" />
                            </div>
                            <div class="stat-title text-white dark:text-white font-bold">Polybags Tags
                            </div>
                            <div class="stat-value text-secondary animate-bounce animate-infinite">{{ $count }}
                            </div>
                            <div class="stat-desc text-white">Validated</div>
                        </div>
                        <div class="stat">
                            <div class="stat-figure text-success">
                                <x-heroicon-o-swatch class="inline-block w-8 h-8 stroke-current" />
                            </div>
                            <div class="stat-title text-white dark:text-white text-xl font-bold">Purchase
                                Order</div>
                            <div class="stat-value text-success text-sm">{{ $carton->packingList->po }}</div>
                            <div class="stat-desc text-white">{{ $carton->packingList->buyer->name }}</div>
                        </div>
                        <div class="stat">
                            <div class="stat-figure text-info">
                                <x-heroicon-o-inbox-stack class="inline-block w-8 h-8 stroke-current" />
                            </div>
                            <div class="stat-title text-white dark:text-white text-xl font-bold">Master
                                Order</div>
                            <div class="stat-value text-info text-sm">{{ $carton->packingList->contract_no }}
                            </div>

                        </div>
                    </div>



                </div>
            </div>
        </div>
    @endif


</div>
