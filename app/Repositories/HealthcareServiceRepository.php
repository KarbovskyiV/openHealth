<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Arr;
use App\Models\LegalEntity;
use App\Repositories\MedicalEvents\Repository;
use Exception;
use App\Models\Division;
use App\Models\HealthcareService;
use Illuminate\Support\Facades\DB;
use App\Classes\eHealth\Api\HealthcareService as HealthcareServiceApi;
use Throwable;

class HealthcareServiceRepository
{
    protected ?Division $division = null;

    /**
     * Sets the division for this healthcare service
     *
     * @param  Division  $division  The division to set
     *
     * @return static Returns this repository instance for method chaining
     */
    public function setDivision(Division $division): static
    {
        $this->division = $division;

        return $this;
    }

    public function getDivision(): Division
    {
        return $this->division;
    }

    /**
     * Store data after successful creating in EHealth.
     *
     * @param  array  $data
     * @return HealthcareService
     * @throws Throwable
     */
    public function store(array $data): HealthcareService
    {
        return DB::transaction(function () use ($data) {
            $data = $this->storeCategoryAndType($data);

            return HealthcareService::create($data);
        });
    }

    /**
     * Sync data.
     *
     * @param  array  $items
     * @return void
     * @throws Throwable
     */
    public function sync(array $items): void
    {
        DB::transaction(function () use ($items) {
            $divisionUuids = collect($items)->pluck('division_id')->unique()->filter();
            $legalEntityUuids = collect($items)->pluck('legal_entity_uuid')->unique()->filter();

            $divisions = Division::whereIn('uuid', $divisionUuids)->pluck('id', 'uuid');
            $legalEntities = LegalEntity::whereIn('uuid', $legalEntityUuids)->pluck('id', 'uuid');

            $dataToInsert = [];

            foreach ($items as $item) {
                $item['division_id'] = $divisions[$item['division_id']];
                $item['legal_entity_id'] = $legalEntities[$item['legal_entity_uuid']];
                unset($item['legal_entity_uuid']);

                // Конвертуємо всі масиви в JSON
                $item['available_time'] = isset($item['available_time'])
                    ? json_encode($item['available_time'], JSON_THROW_ON_ERROR)
                    : null;

                $item['not_available'] = isset($item['not_available'])
                    ? json_encode($item['not_available'], JSON_THROW_ON_ERROR)
                    : null;

                $item = $this->storeCategoryAndType($item);

                $dataToInsert[] = $item;
            }

            HealthcareService::upsert(
                $dataToInsert,
                ['uuid'],
                new HealthcareService()->getFillable()
            );
        });
    }

    public function getAssociatedDivisions(array $healthcareServicesList): array
    {
        // Get all unique division UUIDs for batch lookup
        $divisionUuids = array_unique(array_column($healthcareServicesList, 'division_id'));

        // Batch lookup: get division IDs mapped by their UUIDs to avoid redundant queries
        return Division::whereIn('uuid', $divisionUuids)->pluck('id', 'uuid')->toArray();
    }

    /**
     * Saves all healthcare services from API response using batch upsert operation.
     *
     * @param  array  $healthcareServicesList  Raw healthcare services data from eHealth API
     * @param  array  $divisions
     * @return void
     * @throws Exception|Throwable If database transaction fails
     */
    public function saveHealthcareServiceAll(array $healthcareServicesList, array $divisions): void
    {
        DB::transaction(static function () use ($healthcareServicesList, $divisions) {
            $upsertData = collect(
                app(HealthcareServiceApi::class)->normalizeResponseDataForUpsert($healthcareServicesList, $divisions)
            )->map(function (array $item) {
                // Save category and type to separate table
                return $this->storeCategoryAndType($item);
            })->all();

            // At first save all the Divisions to the DB
            HealthcareService::upsert($upsertData, uniqueBy: ['uuid'], update: new HealthcareService()->getFillable());
        });
    }

    /**
     * TODO: maybe need to put it into validation (need testing)
     * Prepare Request Data
     *
     * @param  mixed  $rawData
     * @return array
     */
    public function prepareRequestCreateData(array $rawData): array
    {
        $params = [
            'division_id' => $this->getDivision()->uuid,
            'category' => [
                'coding' => [
                    [
                        'system' => 'HEALTHCARE_SERVICE_CATEGORIES',
                        'code' => $rawData['category']
                    ]
                ]
            ],
            'providing_condition' => $rawData['providing_condition'],
            'speciality_type' => $rawData['speciality_type'],
        ];

        if (isset($rawData['comment']) && !empty($rawData['comment'])) {
            $params['comment'] = $rawData['comment'];
        }

        if (!empty($rawData['available_time'])) {
            foreach ($rawData['available_time'] as $index => $dayTime) {
                if (!empty($dayTime['all_day'])) {
                    $rawData['available_time'][$index]['available_start_time'] = '';
                    $rawData['available_time'][$index]['available_end_time'] = '';
                }
            }

            $params['available_time'] = available_time($rawData['available_time']);
        }

        if (!empty($rawData['not_available'])) {
            $params['not_available'] = not_available($rawData['not_available']);
        }

        return $params;
    }

    /**
     * Prepares the raw data for a healthcare service update request.
     * For update, to modify only allowed 'comment', 'available_time' and 'not_available' fields.
     *
     * @param  array  $rawData  The raw data to be processed for the update request
     * @return array The processed data ready for updating a healthcare service
     */
    public function prepareRequestUpdateData(array $rawData): array
    {
        $params = [];

        if (!empty($rawData['comment'])) {
            $params['comment'] = $rawData['comment'];
        }

        if (!empty($rawData['available_time'])) {
            foreach ($rawData['available_time'] as $index => $dayTime) {
                if (!empty($dayTime['all_day'])) {
                    $rawData['available_time'][$index]['available_start_time'] = '';
                    $rawData['available_time'][$index]['available_end_time'] = '';
                }
            }

            $params['available_time'] = available_time($rawData['available_time']);
        }

        if (!empty($rawData['not_available'])) {
            $params['not_available'] = not_available($rawData['not_available']);
        }

        return $params;
    }

    /**
     * Set status for specific action (for activate or deactivate)
     *
     * @param  HealthcareService  $healthcareService
     * @param  string  $status
     * @return void
     * @throws Exception
     */
    public function setAction(HealthcareService $healthcareService, string $status): void
    {
        try {
            $healthcareService->setAttribute('status', $status)->save();

            $healthcareService->refresh();
        } catch (Exception $err) {
            throw new Exception($err->getMessage());
        }
    }

    /**
     * Create instance of Healthcare Service class
     *
     * @param  array  $responseData  // The data array suitable to do fill on HealthcareService Model
     * @return HealthcareService|null
     */
    public function createOrUpdate(array $responseData): HealthcareService|null
    {
        $healthcareService = HealthcareService::firstOrNew(['uuid' => $responseData['uuid']]);

        $healthcareService->fill($responseData);

        return $healthcareService;
    }

    protected function storeCategoryAndType(array $data): array
    {
        // Save category
        $category = Repository::codeableConcept()->store($data['category']);
        $data['category_id'] = $category->id;

        // Save if type is present
        if (!empty($data['type'])) {
            $type = Repository::codeableConcept()->store($data['type']);
            $data['type_id'] = $type->id;
        }

        // Remove nested data to avoid mass assignment issues
        unset($data['category'], $data['type']);

        return $data;
    }
}
