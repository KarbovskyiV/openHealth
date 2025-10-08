@use('App\Enums\Declaration\Status')
@use('Carbon\CarbonImmutable')

<div>
    <x-header-navigation x-data="{ showFilter: false }">
        <x-slot name="title">{{ __('forms.declarations') }}</x-slot>

        <div class="ml-auto flex items-center gap-2 mt-2 lg:mt-0">
            <button class="button-sync flex items-center gap-2 whitespace-nowrap">
                @icon('refresh', 'w-4 h-4')
                {{ __('forms.synchronise_with_eHealth') }}
            </button>
        </div>

        <x-slot name="navigation">
            <div class="flex">
                <div class="w-full">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1">
                                @icon('search-outline', 'w-4 h-4.5 text-gray-800 dark:text-white')
                                <p class="default-p">{{ __('declarations.search') }}</p>
                            </div>

                            @isset($countActive)
                                <div class="flex items-center gap-4 pl-30">
                                    <p class="default-p">{{ __('declarations.count_active') }}:</p>
                                    <span class="badge-green">{{ $countActive }}</span>
                                </div>
                            @endisset
                        </div>

                        {{-- Search by name --}}
                        <div class="form-row-3 form-group group top-3 mt-2">
                            <input type="text"
                                   id="searchByName"
                                   placeholder=" "
                                   class="input peer"
                                   wire:model.live.debounce.300ms="searchByName"
                                   autocomplete="off"
                            />
                            <label for="searchByName" class="label">
                                {{ __('patients.patient_full_name') }}
                            </label>

                            <button class="flex items-center gap-2 button-minor" @click="showFilter = !showFilter">
                                @icon('adjustments', 'w-4 h-4')
                                <span>{{ __('forms.additional_search_parameters') }}</span>
                            </button>
                        </div>

                        {{-- Show additional filters --}}
                        <div x-show="showFilter" x-cloak x-transition class="mt-12" x-data="{ openType: false }">
                            {{-- Show different filters for owner --}}
                            @if(Auth::user()->hasRole('OWNER'))
                                @include('livewire.declaration.parts.owner-filters')
                            @else
                                @include('livewire.declaration.parts.basic-filters')
                            @endif
                        </div>
                    </div>
                </div>
        </x-slot>
    </x-header-navigation>

    <div wire:key="declarations-table-{{ $declarations->total() }}-{{ $declarations->currentPage() }}">
        <div class="max-w-7xl mx-auto">
            @if($declarations->isNotEmpty())
                <table class="table-input w-full">
                    <thead class="thead-input">
                    <tr>
                        <th scope="col" class="th-input">{{__('forms.full_name')}}</th>
                        <th scope="col" class="th-input">{{__('forms.number')}}</th>
                        <th scope="col" class="th-input">{{__('forms.birth_date_abbreviated')}}</th>
                        <th scope="col" class="th-input">{{__('employees.doctor')}}</th>
                        <th scope="col" class="th-input">{{__('forms.status.label')}}</th>
                        <th scope="col" class="th-input">{{__('forms.action')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($declarations as $declaration)
                        <tr wire:key="{{ $declaration->declarationNumber }}">
                            <td class="td-input">{{ $declaration->person->fullName }}</td>
                            <td class="td-input">{{ $declaration->declarationNumber }}</td>
                            <td class="td-input">{{ CarbonImmutable::parse($declaration->person->birth_date)->format('d.m.Y') }}</td>
                            <td class="td-input">{{ $declaration->employee->fullName }}</td>
                            <td class="td-input">
                        <span class="{{
                            match($declaration->status) {
                                Status::DRAFT => 'badge-dark',
                                Status::NEW, Status::APPROVED => 'badge-yellow',
                                Status::ACTIVE => 'badge-green',
                                Status::REJECTED, Status::CANCELLED, Status::TERMINATED => 'badge-red',
                                default => ''
                            }
                        }}">
                            {{ $declaration->status->label() }}
                        </span>
                            </td>
                            <td x-data="{ openDropdown: false }" class="relative td-input text-center overflow-visible">
                                <button @click.stop="openDropdown = !openDropdown" type="button" class="cursor-pointer">
                                    @if($declaration->type === 'declaration' || $declaration->status === Status::REJECTED)
                                        @can('view', $declaration)
                                            <a href="{{ route('declaration.view', [legalEntity(), $declaration->id]) }}">
                                                @icon('eye', 'w-6 h-6 text-gray-800 dark:text-white')
                                            </a>
                                        @endcan
                                    @else
                                        @icon('edit-user-outline', 'w-6 h-6 text-gray-800 dark:text-white')
                                    @endif
                                </button>

                                <div x-show="openDropdown"
                                     @click.outside="openDropdown = false"
                                     x-transition
                                     class="absolute right-0 mt-2 z-10 w-fit bg-white rounded divide-y divide-gray-100 shadow"
                                     style="display: none"
                                >
                                    @if($declaration->type === 'request' && $declaration->status === Status::DRAFT)
                                        <a href="{{ route('declaration.edit', [legalEntity(), $declaration->person->id, $declaration->id]) }}"
                                           @click="openDropdown = false"
                                           class="cursor-pointer text-[#222222] text-nowrap flex gap-3 items-center py-2 pl-4 pr-10"
                                        >
                                            @icon('check-circle', 'w-5 h-5 text-red-500')
                                            {{ __('Продовжити створення декларації') }}
                                        </a>

                                        <button wire:click="destroy('{{ $declaration['id'] }}')"
                                                @click="openDropdown = false"
                                                class="cursor-pointer text-nowrap text-red-500 flex gap-3 items-center py-2 pl-4 pr-5"
                                        >
                                            @icon('delete', 'w-5 h-5')
                                            {{ __('Видалити заявку на декларацію') }}
                                        </button>
                                    @endif

                                    @if($declaration->type === 'request' && $declaration->status === Status::NEW)
                                        @can('approve', $declaration)
                                            <button @click="openDropdown = false"
                                                    wire:click="approve({{ $declaration->person->id }}, {{ $declaration->id }})"
                                                    class="cursor-pointer text-[#222222] text-nowrap flex gap-3 items-center py-2 pl-4 pr-19"
                                            >
                                                @icon('check-circle', 'w-5 h-5 text-red-500')
                                                {{ __('declarations.approve') }}
                                            </button>
                                        @endcan

                                        @can('reject', $declaration)
                                            <button wire:click="reject('{{ $declaration['uuid'] }}')"
                                                    @click="openDropdown = false"
                                                    class="cursor-pointer text-nowrap text-red-500 flex gap-3 items-center py-2 pl-4 pr-5"
                                            >
                                                @icon('delete', 'w-5 h-5')
                                                {{ __('declarations.reject_declaration_request') }}
                                            </button>
                                        @endcan
                                    @endif

                                    @if($declaration->type === 'request' && $declaration->status === Status::APPROVED)
                                        @can('sign', $declaration)
                                            <button @click="openDropdown = false"
                                                    wire:click="sign({{ $declaration->person->id }}, {{ $declaration->id }})"
                                                    class="cursor-pointer text-[#222222] text-nowrap flex gap-3 items-center py-2 pl-4 pr-19"
                                            >
                                                @icon('check-circle', 'w-5 h-5 text-red-500')
                                                {{ __('declarations.sign') }}
                                            </button>
                                        @endcan

                                        @can('reject', $declaration)
                                            <button wire:click="reject('{{ $declaration['uuid'] }}')"
                                                    @click="openDropdown = false"
                                                    class="cursor-pointer text-nowrap text-red-500 flex gap-3 items-center py-2 pl-4 pr-5"
                                            >
                                                @icon('delete', 'w-5 h-5')
                                                {{ __('declarations.reject_declaration_request') }}
                                            </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            @else
                <div class="text-center py-16">
                    <p class="text-gray-500 dark:text-gray-400 text-lg">{{ __('forms.nothing_found') }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-8 pl-3.5 pb-8 lg:pl-8 2xl:pl-5">
        {{ $declarations->links() }}
    </div>

    <x-forms.loading/>
</div>
