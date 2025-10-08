<div>
    <x-header-navigation class="items-start" x-data="{ showFilter: false }">

        <x-slot name="title">
            {{ __('forms.employees') }}
        </x-slot>

        @can('create', \App\Models\Employee\EmployeeRequest::class)
            <div class="mt-3 ml-0 flex flex-col sm:flex-row sm:flex-wrap gap-2 self-start">
                <a href="{{ route('employee-request.create', ['legalEntity' => legalEntity()->id]) }}"
                   class="button-primary">{{ __('forms.new_employee') }}</a>
                <button wire:click="sync" type="button" class="button-sync flex items-center gap-2 whitespace-nowrap">
                    @icon('refresh', 'w-4 h-4')
                    {{ __('forms.synchronise_with_eHealth') }}
                </button>
            </div>
        @endcan


        <x-slot name="navigation">
            <div class="flex flex-col -my-4">
                <form wire:submit.prevent="applyFilters">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div class="flex flex-col lg:flex-row items-stretch lg:items-end gap-2 lg:gap-4 w-full">
                        <div class="w-full lg:w-96">
                            <x-forms.form-group>
                                <x-slot name="label">
                                    <label for="employee_search"
                                           class="text-sm font-medium text-gray-900 dark:text-white block mb-2 flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                        </svg>
                                        <span>{{ __('forms.employee_search') }}</span>
                                    </label>
                                </x-slot>
                                <x-slot name="input">
                                    <div class="form-group group w-full">
                                        <input type="text"
                                               id="employee_search"
                                               placeholder=" "
                                               class="input peer"
                                               wire:model.defer="search"
                                               autocomplete="off" />
                                        <label for="employee_search" class="label">ПІБ</label>
                                    </div>
                                </x-slot>
                            </x-forms.form-group>
                        </div>
                        <button class="button-minor flex items-center justify-center gap-2 w-full lg:w-auto self-stretch lg:self-auto lg:-translate-y-[9px]"
                                @click="showFilter = !showFilter">
                            <svg width="16" height="16" id="svg-adjustments" viewBox="0 0 16 16" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M4.00003 3.1999C4.00003 2.98773 3.91574 2.78425 3.76571 2.63422C3.61568 2.48419 3.41220 2.39990 3.20003 2.39990C2.98785 2.39990 2.78437 2.48419 2.63434 2.63422C2.48431 2.78425 2.40003 2.98773 2.40003 3.1999V9.0143C2.15682 9.15474 1.95485 9.35672 1.81444 9.59994C1.67402 9.84316 1.60010 10.11910 1.60010 10.39990C1.60010 10.68070 1.67402 10.95660 1.81444 11.1999C1.95485 11.44310 2.15682 11.64510 2.40003 11.7855V12.7999C2.40003 13.01210 2.48431 13.21560 2.63434 13.3656C2.78437 13.51560 2.98785 13.59990 3.20003 13.5999C3.41220 13.59990 3.61568 13.51560 3.76571 13.3656C3.91574 13.21560 4.00003 13.01210 4.00003 12.7999V11.7855C4.24324 11.64510 4.44520 11.44310 4.58562 11.1999C4.72603 10.95660 4.79996 10.68070 4.79996 10.39990C4.79996 10.11910 4.72603 9.84316 4.58562 9.59994C4.44520 9.35672 4.24324 9.15474 4.00003 9.0143V3.1999ZM8.80003 3.1999C8.80003 2.98773 8.71574 2.78425 8.56571 2.63422C8.41568 2.48419 8.21220 2.39990 8.00003 2.39990C7.78785 2.39990 7.58437 2.48419 7.43434 2.63422C7.28431 2.78425 7.20003 2.98773 7.20003 3.1999V4.2143C6.95682 4.35474 6.75485 4.55672 6.61444 4.79994C6.47402 5.04316 6.40010 5.31906 6.40010 5.59990C6.40010 5.88075 6.47402 6.15665 6.61444 6.39987C6.75485 6.64309 6.95682 6.84507 7.20003 6.9855V12.7999C7.20003 13.01210 7.28431 13.21560 7.43434 13.3656C7.58437 13.51560 7.78785 13.59990 8.00003 13.5999C8.21220 13.59990 8.41568 13.51560 8.56571 13.3656C8.71574 13.21560 8.80003 13.01210 8.80003 12.7999V6.9855C9.04324 6.84507 9.24520 6.64309 9.38562 6.39987C9.52603 6.15665 9.59996 5.88075 9.59996 5.59990C9.59996 5.31906 9.52603 5.04316 9.38562 4.79994C9.24520 4.55672 9.04324 4.35474 8.80003 4.2143V3.1999ZM12.8 2.3999C13.0122 2.3999 13.2157 2.48419 13.3657 2.63422C13.5157 2.78425 13.6 2.98773 13.6 3.1999V9.0143C13.8432 9.15474 14.0452 9.35672 14.1856 9.59994C14.3260 9.84316 14.4 10.11910 14.4 10.39990C14.4 10.68070 14.3260 10.95660 14.1856 11.1999C14.0452 11.44310 13.8432 11.64510 13.6 11.7855V12.7999C13.6 13.01210 13.5157 13.21560 13.3657 13.3656C13.2157 13.51560 13.0122 13.59990 12.8 13.5999C12.5879 13.59990 12.3844 13.51560 12.2343 13.3656C12.0843 13.21560 12 13.01210 12 12.7999V11.7855C11.7568 11.64510 11.5549 11.44310 11.4144 11.1999C11.2740 10.95660 11.2001 10.68070 11.2001 10.39990C11.2001 10.11910 11.2740 9.84316 11.4144 9.59994C11.5549 9.35672 11.7568 9.15474 12 9.0143V3.1999C12 2.98773 12.0843 2.78425 12.2343 2.63422C12.3844 2.48419 12.5879 2.39990 12.8 2.3999V2.3999Z"
                                    fill="currentColor"/>
                            </svg>
                            <span>{{ __('forms.additional_search_parameters') }}</span>
                        </button>
                    </div>


                    <div x-cloak x-show="showFilter" x-transition class="pt-4 mt-4">
                        <div class="form-row-4">
                            <div class="form-group phone-wrapper">
                                <input wire:model.defer="filter.phone"
                                       wire:keydown.enter="applyFilters"
                                       type="tel" placeholder=" "
                                       class="peer input pl-10 with-leading-icon text-gray-500"
                                       x-mask="+380999999999" id="filter_phone" />
                                <label for="filter_phone" class="label pl-10">{{ __('forms.phone') }}</label>
                            </div>
                            <div class="form-group group">
                                <input wire:model.defer="filter.email" wire:keydown.enter="applyFilters" name="filter_email" id="filter_email" class="input peer" placeholder=" " autocomplete="off" />
                                <label for="filter_email" class="label">Email</label>
                            </div>
                        </div>
                        <div class="form-row-4">
                            <div class="form-group group">
                                <select wire:model.defer="filter.role" wire:keydown.enter="applyFilters"
                                        id="filter_role"
                                        class="input peer text-gray-500 dark:bg-gray-800 dark:text-gray-400"
                                >
                                    <option value="">Всі ролі</option>
                                    @foreach($dictionaries['EMPLOYEE_TYPE'] ?? [] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <label for="filter_role" class="label">Роль працівника</label>
                            </div>
                            <div class="form-group group">
                                <select wire:model.defer="filter.position" wire:keydown.enter="applyFilters"
                                        id="filter_position"
                                        class="input peer text-gray-500 dark:bg-gray-800 dark:text-gray-400"
                                >
                                    <option value="">Всі посади</option>
                                    @foreach($dictionaries['POSITION'] ?? [] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <label for="filter_position" class="label">{{ __('forms.position') }}</label>
                            </div>
                        </div>
                        <div class="form-row-4">
                            <div class="form-group group">
                                <select wire:model.defer="filter.division_id" wire:keydown.enter="applyFilters"
                                        name="filter_division"
                                        id="filter_division"
                                        class="input peer text-gray-500 dark:bg-gray-800 dark:text-gray-400"
                                >
                                    <option value="">Всі підрозділи</option>
                                    @foreach($divisions ?? [] as $division)
                                        <option value="{{ $division['id'] }}">{{ $division['name'] }}</option>
                                    @endforeach
                                </select>
                                <label for="filter_division" class="label">Медичний заклад</label>
                            </div>
                            <div class="form-group group" x-data="{ open: false, selectedStatuses: @entangle('status') }">
                                <label for="statusFilter" class="label">Статус</label>
                                <div class="relative">

                                    <input type="text"
                                           id="statusFilter"
                                           class="input peer w-full cursor-pointer text-gray-500 dark:text-gray-400"
                                           placeholder="Оберіть статуси"
                                           x-on:click="open = !open"
                                           :value="selectedStatuses.length ? selectedStatuses.map(s => {
                                                if (s === 'APPROVED') return '{{ __('forms.active') }}';
                                                if (s === 'NEW') return '{{ __('forms.draft') }}';
                                                if (s === 'DISMISSED') return '{{ __('forms.dismissed') }}';
                                                if (s === 'VERIFIED') return '{{ __('forms.verified') }}';
                                                if (s === 'NOT_VERIFIED') return '{{ __('forms.not_verified') }}';
                                                return s;
                                            }).join(', ') : ''"
                                           readonly
                                    />
                                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                    <div x-show="open"
                                         x-on:click.away="open = false"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute z-10 mt-2 w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg">
                                        <ul class="py-2 px-3 space-y-2 text-sm text-gray-700 dark:text-gray-200">
                                            <li>
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="checkbox" value="APPROVED" wire:model.defer="status"
                                                           class="rounded-sm text-blue-600 focus:ring-blue-500 border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-600 dark:checked:border-transparent" />
                                                    <span>{{ __('forms.active') }}</span>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="checkbox" value="NEW" wire:model.defer="status"
                                                           class="rounded-sm text-blue-600 focus:ring-blue-500 border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-600 dark:checked:border-transparent" />
                                                    <span>{{ __('forms.draft') }}</span>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="checkbox" value="DISMISSED" wire:model.defer="status"
                                                           class="rounded-sm text-blue-600 focus:ring-blue-500 border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-600 dark:checked:border-transparent" />
                                                    <span>{{ __('forms.dismissed') }}</span>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="checkbox" value="VERIFIED" wire:model.defer="status"
                                                           class="rounded-sm text-blue-600 focus:ring-blue-500 border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-600 dark:checked:border-transparent">
                                                    <span>{{ __('forms.verified') }}</span>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="checkbox" value="NOT_VERIFIED" wire:model.defer="status"
                                                           class="rounded-sm text-blue-600 focus:ring-blue-500 border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-600 dark:checked:border-transparent">
                                                    <span>{{ __('forms.not_verified') }}</span>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-9 mt-6 flex flex-col sm:flex-row gap-2 w-full">
                        <button type="submit" class="flex items-center gap-2 button-primary">
                            @icon('search', 'w-4 h-4')
                            <span>{{ __('forms.search') }}</span>
                        </button>

                        <button type="button" wire:click="resetFilters" class="button-primary-outline">
                            {{ __('forms.reset_all_filters') }}
                        </button>
                    </div>
                </div>
                </form>
            </div>
        </x-slot>
    </x-header-navigation>

    <x-section class="shift-content pl-3.5">
        <div class="space-y-6 employee-section-no-left-padding mt-6">
            <div class="table-container-responsive overflow-x-auto" style="max-width:100%;">
                @forelse($parties as $party)
                    <fieldset class="p-4 sm:p-8 sm:pb-10 mb-16 mt-6 border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 max-w-[1280px]" wire:key="party-{{ $party->id }}">
                        <legend class="legend">{{ $party->fullName }}</legend>
                        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-200 dark:border-gray-700 pb-4">
                            <div>
                                <div class="flex items-center flex-wrap gap-x-4 gap-y-1 text-sm text-gray-500 mt-2">
                                    @if ($mobilePhone = $party->phones->firstWhere('type', 'MOBILE'))
                                        <span class="flex items-center gap-1.5">
                                       <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M18.427 14.768 17.2 13.542a1.733 1.733 0 0 0-2.45 0l-.613.613a1.732 1.732 0 0 1-2.45 0l-1.838-1.84a1.735 1.735 0 0 1 0-2.452l.612-.613a1.735 1.735 0 0 0 0-2.452L9.237 5.572a1.6 1.6 0 0 0-2.45 0c-3.223 3.2-1.702 6.896 1.519 10.117 3.22 3.221 6.914 4.745 10.12 1.535a1.601 1.601 0 0 0 0-2.456Z"/>
                                         </svg>
                                        <a href="tel:{{ $mobilePhone->number }}"
                                           class="hover:underline">{{ $mobilePhone->number }}</a>
                                    </span>
                                    @endif
                                    @if($party->email)
                                        <span class="flex items-center gap-1.5">
                                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                           <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m3.5 5.5 7.893 6.036a1 1 0 0 0 1.214 0L20.5 5.5M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"/>
                                        </svg>
                                        <a href="mailto:{{$party->email}}" class="hover:underline">{{ $party->email }}</a>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @can('create', \App\Models\Employee\EmployeeRequest::class)
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('employee-request.position-add', ['legalEntity' => legalEntity()->id, 'party' => $party->id]) }}"
                                       class="item-add text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                        <span
                                            class="text-xl leading-none">+</span><span>{{ __('forms.add_position') }}</span>
                                    </a>
                                </div>
                            @endcan
                        </div>
                        <div class="flow-root mt-4">
                            <div class="max-w-screen-xl">
                                <table class="table-input w-full table-fixed min-w-[600px] text-sm">
                                    <thead class="thead-input">
                                    <tr>
                                        <th scope="col" class="th-input w-[28%]">Посада</th>
                                        <th scope="col" class="th-input w-[22%]">Роль</th>
                                        <th scope="col" class="th-input w-[20%]">Підрозділ</th>
                                        <th scope="col" class="th-input w-[15%]">Статус</th>
                                        <th scope="col" class="th-input w-[15%] text-center"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $positions = $party->employees->merge($party->employeeRequests);
                                        $groupedPositions = $positions->groupBy('position');
                                    @endphp
                                    @foreach($groupedPositions as $positionCode => $items)
                                        @php
                                            $positionToShow = $items->firstWhere(fn($item) => $item instanceof \App\Models\Employee\Employee) ?? $items->first();
                                        @endphp
                                        <tr>
                                            <td class="td-input break-words whitespace-normal align-top">{{ $dictionaries['POSITION'][$positionToShow->position] ?? $positionToShow->position }}</td>
                                            <td class="td-input break-words whitespace-normal align-top">{{ $dictionaries['EMPLOYEE_TYPE'][$positionToShow->employee_type] ?? $positionToShow->employee_type }}</td>
                                            <td class="td-input break-words whitespace-normal align-top">{{ $positionToShow->division->name ?? 'N/A' }}</td>

                                            <td class="td-input break-words whitespace-nowrap align-top">
                                                @php
                                                    // First, check if the record is an Employee model. This is the highest priority.
                                                    $isEmployee = $positionToShow instanceof \App\Models\Employee\Employee;
                                                @endphp

                                                @if($isEmployee)
                                                    {{-- For a standard Employee record, show its actual status --}}
                                                    @if($positionToShow->status?->value === 'APPROVED')
                                                        <span class="badge-green">{{__('forms.status.active')}}</span>
                                                    @else
                                                        <span class="badge-red">{{__('forms.status.dismissed')}}</span>
                                                    @endif
                                                @else
                                                    {{-- If it's not an Employee, it must be an EmployeeRequest. Now check if it's a draft. --}}
                                                    @if(is_null($positionToShow->applied_at))
                                                        {{-- applied_at is null, so it's a draft/new request. --}}
                                                        <span class="badge-red">{{__('forms.status.draft')}}</span>
                                                    @else
                                                        {{-- applied_at has a value, meaning the request has been submitted and is active/processed. --}}
                                                        <span class="badge-green">{{__('forms.status.active')}}</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="td-input text-center">
                                                @include('livewire.employee.parts.actions-dropdown', [
                                                    'position' => $positionToShow
                                                ])
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </fieldset>
                @empty
                    <div class="text-center py-16">
                        <p class="text-gray-500 dark:text-gray-400 text-lg">{{__('forms.nothing_found')}}</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="mt-8 pl-3.5 pb-8 lg:pl-8 2xl:pl-5">
            {{ $parties->links() }}
        </div>
    </x-section>

    @include('livewire.employee.parts.modals.deactivate-modal')
    @include('livewire.employee.parts.modals.delete-draft-modal')
</div>
