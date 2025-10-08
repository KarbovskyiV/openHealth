<?php

declare(strict_types=1);

namespace App\Livewire\Division\HealthcareService;

use App\Classes\eHealth\EHealth;
use App\Core\Arr;
use App\Exceptions\EHealth\EHealthResponseException;
use App\Exceptions\EHealth\EHealthValidationException;
use App\Models\HealthcareService;
use App\Repositories\Repository;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class HealthcareServiceCreate extends HealthcareServiceComponent
{
    public function create(): void
    {
        if (Auth::user()?->cannot('create', HealthcareService::class)) {
            Session::flash('error', 'У вас немає дозволу на створення послуги');

            return;
        }

        try {
            $validated = $this->form->doValidation();
        } catch (ValidationException $exception) {
            Session::flash('error', $exception->validator->errors()->first());
            $this->setErrorBag($exception->validator->getMessageBag());

            return;
        }

        // Create in eHealth
        try {
            $response = EHealth::healthcareService()->create(data: removeEmptyKeys(Arr::toSnakeCase($validated)));
        } catch (ConnectionException $exception) {
            $this->logConnectionError($exception, 'Error connecting when creating a healthcare service');
            Session::flash('error', "Виникла помилка. Відсутній зв'язок із ЕСОЗ");

            return;
        } catch (EHealthValidationException|EHealthResponseException $exception) {
            $this->logEHealthException($exception, 'Error when creating a healthcare service');

            if ($exception instanceof EHealthValidationException) {
                Session::flash('error', $exception->getFormattedMessage());
            } else {
                Session::flash('error', 'Помилка від ЕСОЗ: ' . $exception->getMessage());
            }

            return;
        }

        // Store in local database
        try {
            Repository::healthcareService()->store($response->validate());

            $this->redirectRoute('healthcare-service.index', [legalEntity(), $this->divisionId], navigate: true);
        } catch (Throwable $exception) {
            $this->logDatabaseErrors($exception, 'Failed to store healthcare service');
            Session::flash('error', 'Виникла помилка. Зверніться до адміністратора.');

            return;
        }
    }

    public function validatedResponse(): array
    {
        return [
            "available_time" => [],
            "category" => [
                "text" => null,
                "coding" => [
                    0 => [
                        "code" => "MSP",
                        "system" => "HEALTHCARE_SERVICE_CATEGORIES"
                    ]
                ],
            ],
            "comment" => null,
            "coverage_area" => null,
            "division_id" => "032516fc-fb09-40b6-842c-afa644c7c70f",
            "uuid" => "14f9a875-a7ed-4407-9b0a-8db0029e2bec",
            "ehealth_inserted_at" => "2025-10-08T09:23:54.739638Z",
            "ehealth_inserted_by" => "82d1f518-23c9-4c6c-868b-6f7ab26c6da8",
            "is_active" => true,
            "legal_entity_uuid" => "f13ab4b7-1167-4215-9fb3-2116b775ddb1",
            "license_id" => null,
            "licensed_healthcare_service" => null,
            "not_available" => [],
            "providing_condition" => "OUTPATIENT",
            "speciality_type" => "LABORATORY_RESEARCH_OF_ENVIRONMENT_CHEMICAL_FACTORS",
            "status" => "ACTIVE",
            "type" => null,
            "ehealth_updated_at" => "2025-10-08T09:23:54.739638Z",
            "ehealth_updated_by" => "82d1f518-23c9-4c6c-868b-6f7ab26c6da8"
        ];
    }

    public function render(): View
    {
        return view('livewire.division.healthcare-service.healthcare-service-create');
    }
}
