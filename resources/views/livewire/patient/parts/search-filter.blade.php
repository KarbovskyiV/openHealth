<div x-show="showFilter">
    <div class="form-row-3">
        <div class="form-group group">
            <input wire:model="form.patientsFilter.firstName"
                   type="text"
                   name="filterFirstName"
                   id="filterFirstName"
                   class="input peer @error('form.patientsFilter.firstName') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />
            <label for="filterFirstName" class="label">
                {{ __('forms.first_name') }}
            </label>

            @error('form.patientsFilter.firstName')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="form.patientsFilter.lastName"
                   type="text"
                   name="filterLastName"
                   id="filterLastName"
                   class="input peer @error('form.patientsFilter.lastName') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />
            <label for="filterLastName" class="label">
                {{ __('forms.last_name') }}
            </label>

            @error('form.patientsFilter.lastName')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <div class="datepicker-wrapper">
                <input wire:model="form.patientsFilter.birthDate"
                       datepicker-max-date="{{ now()->format('Y-m-d') }}"
                       type="text"
                       name="filterBirthDate"
                       id="filterBirthDate"
                       class="datepicker-input with-leading-icon input peer @error('form.patientsFilter.birthDate') input-error @enderror"
                       placeholder=" "
                       required
                       autocomplete="off"
                />
                <label for="filterBirthDate" class="wrapped-label">
                    {{ __('forms.birth_date') }}
                </label>
            </div>

            @error('form.patientsFilter.birthDate')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>
    </div>

    <div x-data="{ showAdditionalParams: false }">
        <button class="flex items-center gap-2 button-minor mb-4"
                @click.prevent="showAdditionalParams = !showAdditionalParams"
        >
            @icon('adjustments', 'w-4 h-4')
            <span>{{ __('forms.additional_search_parameters') }}</span>
        </button>

        <div x-show="showAdditionalParams" x-transition x-cloak>
            <div class="form-row-3">
                <div class="form-group group">
                    <input wire:model="form.patientsFilter.secondName"
                           type="text"
                           name="filterSecondName"
                           id="filterSecondName"
                           class="input peer @error('form.patientsFilter.secondName') input-error @enderror"
                           placeholder=" "
                           autocomplete="off"
                    />
                    <label for="filterSecondName" class="label">
                        {{ __('forms.second_name') }}
                    </label>

                    @error('form.patientsFilter.secondName')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="form-group group">
                    <input wire:model="form.patientsFilter.taxId"
                           type="text"
                           name="filterTaxId"
                           id="filterTaxId"
                           class="input peer @error('form.patientsFilter.taxId') input-error @enderror"
                           placeholder=" "
                           maxlength="10"
                           autocomplete="off"
                    />
                    <label for="filterTaxId" class="label">
                        {{ __('forms.rnokpp') }} ({{ __('forms.ipn') }})
                    </label>

                    @error('form.patientsFilter.taxId')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group group">
                    <input wire:model="form.patientsFilter.phoneNumber"
                           name="filterPhoneNumber"
                           id="filterPhoneNumber"
                           type="text"
                           class="input peer @error('form.patientsFilter.phoneNumber') input-error @enderror"
                           placeholder=" "
                           autocomplete="off"
                    />
                    <label for="filterPhoneNumber" class="label">
                        {{ __('forms.phone_number') }}
                    </label>

                    @error('form.patientsFilter.phoneNumber')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="form-group group">
                    <input wire:model="form.patientsFilter.birthCertificate"
                           type="text"
                           name="filterBirthCertificate"
                           id="filterBirthCertificate"
                           class="input peer @error('form.patientsFilter.birthCertificate') input-error @enderror"
                           placeholder=" "
                           autocomplete="off"
                    />
                    <label for="filterBirthCertificate" class="label">
                        {{ __('forms.birth_certificate') }}
                    </label>

                    @error('form.patientsFilter.birthCertificate')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            @if($context === 'index')
                <div class="form-row-3">
                    <div class="form-group group" x-data="{ open: false }">
                        <label for="filterDropdown" class="label"></label>
                        <div class="relative">
                            <input type="text"
                                   id="filterDropdown"
                                   class="input peer w-full cursor-pointer text-gray-500 dark:text-gray-400"
                                   placeholder="{{ __('forms.select_filter') }}"
                                   @click="open = !open"
                                   :value="
                                       $wire.activeFilter === 'all' ? '{{ __('patients.all') }}' :
                                       $wire.activeFilter === 'eHEALTH' ? '{{ __('patients.patients') }}' :
                                       $wire.activeFilter === 'APPLICATION' ? '{{ __('patients.applications') }}' :
                                       ''
                                   "
                                   readonly
                            />
                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute z-10 mt-2 w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg"
                            >
                                <ul class="py-2 px-3 space-y-2 text-sm text-gray-700 dark:text-gray-200">
                                    <li>
                                        <label class="flex items-center space-x-2 cursor-pointer" @click="open = false">
                                            <input type="radio"
                                                   value="all"
                                                   wire:model.live="activeFilter"
                                                   class="sr-only"
                                            />
                                            <span class="{{ $activeFilter === 'all' ? 'text-blue-600' : '' }}">
                                                {{ __('patients.all') }}
                                            </span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="flex items-center space-x-2 cursor-pointer" @click="open = false">
                                            <input type="radio"
                                                   value="eHEALTH"
                                                   wire:model.live="activeFilter"
                                                   class="sr-only"
                                            />
                                            <span class="{{ $activeFilter === 'eHEALTH' ? 'text-blue-600' : '' }}">
                                                {{ __('patients.patients') }}
                                            </span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="flex items-center space-x-2 cursor-pointer" @click=" open = false">
                                            <input type="radio"
                                                   value="APPLICATION"
                                                   wire:model.live="activeFilter"
                                                   class="sr-only"
                                            />
                                            <span class="{{ $activeFilter === 'APPLICATION' ? 'text-blue-600' : '' }}">
                                                {{ __('patients.applications') }}
                                            </span>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
