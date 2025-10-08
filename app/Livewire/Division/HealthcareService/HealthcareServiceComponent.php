<?php

declare(strict_types=1);

namespace App\Livewire\Division\HealthcareService;

use App\Livewire\Division\Forms\HealthcareServiceForm as Form;
use App\Models\Division;
use App\Models\LegalEntity;
use App\Traits\FormTrait;
use App\Traits\WorkTimeUtilities;
use Livewire\Component;

class HealthcareServiceComponent extends Component
{
    use FormTrait;
    use WorkTimeUtilities;

    public Form $form;

    public string $divisionName;

    public int $divisionId;

    public array $licenses;

    protected array $dictionaryNames = [
        'HEALTHCARE_SERVICE_CATEGORIES',
        'SPECIALITY_TYPE',
        'PROVIDING_CONDITION',
        'HEALTHCARE_SERVICE_PHARMACY_DRUGS_TYPES'
    ];

    public function mount(LegalEntity $legalEntity, Division $division): void
    {
        $this->getDictionary();

        $this->dictionaries['HEALTHCARE_SERVICE_CATEGORIES'] = $this->getDictionariesFields(
            config('ehealth.healthcare_service_' . strtolower(legalEntity()->type) . '_categories', []),
            'HEALTHCARE_SERVICE_CATEGORIES'
        );
        $this->dictionaries['PROVIDING_CONDITION'] = $this->getDictionariesFields(
            config('ehealth.legal_entity_' . strtolower(legalEntity()->type) . '_providing_conditions', []),
            'PROVIDING_CONDITION'
        );

        $this->divisionName = $division->name;
        $this->form->divisionId = $division->uuid;
        $this->divisionId = $division->id;

        $this->licenses = $legalEntity->licenses()->get(['id', 'uuid', 'type'])->toArray();
    }
}
