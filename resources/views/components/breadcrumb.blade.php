@props([
    'style' => 'default',
])
<div class="breadcrumbs text-sm text-white">
    <ul>
      <li>
        <a>
            {{-- <img src="{{ asset('vendor/blade-tabler-icons/'.$iconParent.'.svg') }}" class="w-10 h-10 p-2 "/> --}}

         {{$parentLevel}}
        </a>
      </li>
      @if ($secondLevel)
      <li @class([
        'text-white' => $style === 'default',
        'text-primary-500' => $style === 'active',
      ])>
        <a>
            {{-- <img src="{{ asset('vendor/blade-tabler-icons/'.$iconSecond.'.svg') }}" class="w-10 h-10 p-2 stroke-white"/> --}}
          {{$secondLevel}}
        </a>
      </li>
      @endif

    </ul>
  </div>
