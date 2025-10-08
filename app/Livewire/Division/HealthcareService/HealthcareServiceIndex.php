<?php

declare(strict_types=1);

namespace App\Livewire\Division\HealthcareService;

use App\Classes\eHealth\EHealth;
use App\Enums\Status;
use App\Exceptions\EHealth\EHealthResponseException;
use App\Exceptions\EHealth\EHealthValidationException;
use App\Livewire\Division\Forms\HealthcareServiceForm as HealthCareFormRequest;
use App\Models\Division;
use App\Models\HealthcareService;
use App\Models\LegalEntity;
use App\Repositories\Repository;
use App\Traits\FormTrait;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Log;
use Throwable;

class HealthcareServiceIndex extends Component
{
    use WithPagination;
    use FormTrait;

    public HealthCareFormRequest $formService;

    public Division $division;

    public string $mode = 'create';

    /**
     * Values of possible allowed categories
     *
     * @var array $healthcareCategoriesKeys
     */
    protected array $healthcareCategoriesKeys = ['MSP'];

    public ?array $speciality_type_msp_keys = [
        'PHARMACIST',
        '"PHARMACEUTICS_ORGANIZATION',
        'CLINICAL_PROVISOR',
        'ANALYTICAL_AND_CONTROL_PHARMACY',
        'PHARMACEUTICS_ORGANIZATION'
    ];

    public ?array $speciality_type;

    public bool $divisionStatus = false;

    public array $dictionaryNames = ['DIVISION_TYPE', 'SPECIALITY_TYPE', 'PROVIDING_CONDITION'];

    public function mount(LegalEntity $legalEntity, Division $division)
    {
        $this->division = $division;

        $this->divisionStatus = $this->division->status === Status::ACTIVE;

        $this->getDictionary();
    }

    public function store(): void
    {
        $this->resetErrorBag();

        $error = $this->formService->doValidation($this->mode);

        if ($error) {
            $this->dispatch('flashMessage', ['message' => $error, 'type' => 'error']);
        } else {
            if (!$this->updateOrCreate()) {
                return;
            }
        }

        $this->closeModal();
    }

    public function update(): void
    {
        $error = $this->formService->doValidation($this->mode);

        if ($error) {
            $this->dispatch('flashMessage', ['message' => $error, 'type' => 'error']);
        } else {
            if (!$this->updateOrCreate()) {
                return;
            }
        }

        $this->closeModal();
    }

    private function updateHealthcareService(): array|null
    {
        $uuid = $this->formService->getHealthcareServiceParam('uuid');

        $healthcareServiceRawData = $this->formService->getHealthcareService();

        $requestParams = Repository::healthcareService()->prepareRequestUpdateData($healthcareServiceRawData);

        try {
            return EHealth::healthcareService()->update(uuid: $uuid, data: $requestParams)->validate();
        } catch (Exception $err) {
            Log::error(self::class . ':updateHealthcareService', ['error' => $err->getMessage()]);
        }

        return null;
    }

    public function activate(HealthcareService $healthcareService): void
    {
        try {
            $response = EHealth::healthcareService()->activate($healthcareService->uuid);

            if (!$response->successful()) {
                throw new Exception('response_error ' . $response->body());
            }

            $responseData = $response->getData();

            Repository::healthcareService()->setAction($healthcareService, $responseData['status']);
        } catch (Exception $err) {
            Log::error(self::class . ':activate:', ['message' => $err->getMessage()]);

            session()->flash('error', __('Цю послугу не вдалось активувати'));
        }
    }

    public function deactivate(HealthcareServiceModel $healthcareService): void
    {
        try {
            $response = EHealth::healthcareService()->deactivate($healthcareService->uuid);

            if (!$response->successful()) {
                throw new Exception('response_error ' . $response->body());
            }

            $responseData = $response->getData();

            Repository::healthcareService()->setAction($healthcareService, $responseData['status']);
        } catch (Exception $err) {
            Log::error(self::class . ':deactivate:', ['message' => $err->getMessage()]);

            session()->flash('error', __('Цю послугу не вдалось деактивувати'));
        }
    }

    public function sync(): void
    {
        try {
            // TODO: кнопка sync має бути тільки в division а не в спільному списку, бо не можна зробити сінкс по легал ентіті
            $response = EHealth::healthcareService()->getMany();
        } catch (ConnectionException $exception) {
            $this->logConnectionError($exception, 'Error connecting when getting a healthcare service list');
            Session::flash('error', "Виникла помилка. Відсутній зв'язок із ЕСОЗ");

            return;
        } catch (EHealthValidationException|EHealthResponseException $exception) {
            $this->logEHealthException($exception, 'Error connecting when getting a healthcare service list');
            Session::flash('error', 'Виникла помилка. Зверніться до адміністратора.');

            return;
        }

        $healthcareServices = $response->validate();

        try {
            Repository::healthcareService()->sync($healthcareServices);
        } catch (Throwable $exception) {
            Session::flash('error', 'Виникла помилка. Зверніться до адміністратора.');
            $this->logDatabaseErrors($exception, 'Error while synchronizing healthcare services with eHealth: ');

            return;
        }

        if ($response->isNotLast()) {
            // TODO run
        }

        Session::flash('success', __('Інформацію успішно оновлено'));
    }

    public function specialityType(): void
    {
        $this->dictionaries['modal']['SPECIALITY_TYPE'] = array_intersect_key(
            $this->speciality_type,
            array_flip($this->speciality_type_msp_keys)
        );
    }

    #[Computed]
    public function healthcareServices(): LengthAwarePaginator
    {
        $allItems = HealthcareService::with(['division'])->get();

        // Pagination
        $perPage = config('pagination.per_page');
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $allItems->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $currentItems,
            $allItems->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url()]
        );
    }

    public function render(): View
    {
        return view('livewire.division.healthcare-service.healthcare-service-index', [
            'healthcareServices' => $this->healthcareServices
        ]);
    }
}
