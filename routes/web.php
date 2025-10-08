<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\EHealthLoginController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\HomeController;
use App\Livewire\Actions\Logout;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\SelectLegalEntity;
use App\Livewire\Auth\VerifyEmail;
use App\Livewire\Auth\VerifyPersonality;
use App\Livewire\Contract\ContractForm;
use App\Livewire\Contract\ContractIndex;
use App\Livewire\Dashboard;
use App\Livewire\Declaration\DeclarationCreate;
use App\Livewire\Declaration\DeclarationEdit;
use App\Livewire\Declaration\DeclarationIndex;
use App\Livewire\Declaration\DeclarationView;
use App\Livewire\DiagnosticReport\DiagnosticReportCreate;
use App\Livewire\Division\DivisionCreate;
use App\Livewire\Division\DivisionEdit;
use App\Livewire\Division\DivisionIndex;
use App\Livewire\Division\DivisionView;
use App\Livewire\Division\HealthcareService\HealthcareServiceCreate;
use App\Livewire\Division\HealthcareService\HealthcareServiceIndex;
use App\Livewire\Employee\EmployeeCreate;
use App\Livewire\Employee\EmployeeEdit;
use App\Livewire\Employee\EmployeeIndex;
use App\Livewire\Employee\EmployeePositionAdd;
use App\Livewire\Employee\EmployeeRequestEdit;
use App\Livewire\Employee\EmployeeRequestShow;
use App\Livewire\Employee\EmployeeShow;
use App\Livewire\Encounter\EncounterCreate;
use App\Livewire\Encounter\EncounterEdit;
use App\Livewire\LegalEntity\CreateLegalEntity;
use App\Livewire\LegalEntity\EditLegalEntity;
use App\Livewire\License\LicenseCreate;
use App\Livewire\License\LicenseEdit;
use App\Livewire\License\LicenseIndex;
use App\Livewire\License\LicenseView;
use App\Livewire\Patient\PatientCreate;
use App\Livewire\Patient\PatientEdit;
use App\Livewire\Patient\PatientIndex;
use App\Livewire\Patient\Records\PatientData;
use App\Livewire\Patient\Records\PatientEpisodes;
use App\Livewire\Patient\Records\PatientSummary;
use App\Livewire\Procedure\ProcedureCreate;
use App\Models\Declaration;
use App\Models\DeclarationRequest;
use App\Models\Division;
use App\Models\HealthcareService;
use App\Models\LegalEntity;
use App\Models\License;
use App\Models\MedicalEvents\Sql\DiagnosticReport;
use App\Models\MedicalEvents\Sql\Encounter;
use App\Models\MedicalEvents\Sql\Procedure;
use App\Models\Person\Person;
use App\Models\Person\PersonRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::post('/send-email', [EmailController::class, 'sendEmail'])->name('send.email');

/* Auth */

Route::get('/ehealth/oauth/', EHealthLoginController::class)->name('ehealth.oauth.callback');

Route::middleware('guest')->group(function () {
    Route::get('login', Login::class)->name('login');
    Route::get('register', Register::class)->name('register');
    Route::get('forgot-password', ForgotPassword::class)->name('forgot.password');
    Route::get('reset-password/{token}', ResetPassword::class)->name('password.reset');

    Route::get('email/verify', VerifyEmail::class)->name('verification.notice');

    Route::get('email/verify/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
});

Route::post('logout', Logout::class)->name('logout');

/* Dashboard */
Route::middleware(['auth:web,ehealth', 'verified'])->group(function () {
    Route::get('/verify-personality', VerifyPersonality::class)->name('party.verify');

    Route::get('/select-legal-entity', SelectLegalEntity::class)->name('legalEntity.select');

    Route::prefix('/dashboard')->group(function () {
        Route::get('/', Dashboard::class)->name('dashboard.index');

        Route::get('/legal-entities/create', CreateLegalEntity::class)
            ->can('create', LegalEntity::class)
            ->name('legal-entity.new.create');
    });

    Route::middleware(['can:access,legalEntity'])->prefix('/dashboard/{legalEntity}')
        ->whereNumber('legalEntity')
        ->group(function () {
            Route::get('/', Dashboard::class)->name('dashboard');

            Route::get('/edit', EditLegalEntity::class)
                ->can('edit', LegalEntity::class)
                ->name('legal-entity.edit');

            Route::get('/create', CreateLegalEntity::class)
                ->can('create', LegalEntity::class)
                ->name('legal-entity.create');

            Route::get('/healthcare-service', HealthcareServiceIndex::class)
                ->name('healthcare-service.index')
                ->can('viewAny', HealthcareService::class);

            Route::prefix('division')->middleware(['permission:division:read|division:details'])->group(function () {
                Route::get('/', DivisionIndex::class)->name('division.index')->can('viewAny', Division::class);

                Route::get('/create', DivisionCreate::class)->name('division.create')->can('create', Division::class);
                Route::get('/{division}', DivisionView::class)->name('division.view')->can('viewAny', Division::class);
                Route::get('/{division}/edit', DivisionEdit::class)->name('division.edit')->can('update', 'division');
                Route::get('/{division}/healthcare-service/create', HealthcareServiceCreate::class)
                    ->name('healthcare-service.create')
                    ->can('create', HealthcareService::class);
            });

            Route::prefix('employee')->name('employee.')->middleware('auth')->group(function () {
                Route::get('/', EmployeeIndex::class)->name('index');

                Route::get('/{employee}', EmployeeShow::class)
                    ->name('show')->middleware('can:view,employee');

                Route::get('/{employee}/edit', EmployeeEdit::class)
                    ->name('edit')->middleware('can:update,employee');
            });

            // --- Group for Employee Requests ---
            Route::prefix('employee-request')->name('employee-request.')->middleware('auth')->group(function () {
                Route::get('/create', EmployeeCreate::class)->name('create');
                Route::get('/party/{party}/position-add', EmployeePositionAdd::class)->name('position-add');

                Route::get('/{employee_request}', EmployeeRequestShow::class)
                    ->name('show')->middleware('can:view,employee_request');

                Route::get('/{employee_request}/edit', EmployeeRequestEdit::class)
                    ->name('edit')->middleware('can:update,employee_request');
            });

            Route::prefix('contract')->group(function () {
                Route::get('/', ContractIndex::class)->name('contract.index');
                Route::get('/form/{id?}', ContractForm::class)->name('contract.form');
            });

            // Routes related to legal entity licenses; primary license can't be edited
            Route::prefix('license')->middleware(['permission:license:read|license:write'])
                ->name('license.')
                ->group(function () {
                    Route::get('/', LicenseIndex::class)->name('index')->can('viewAny', License::class);
                    Route::get('/create', LicenseCreate::class)->name('create')->can('create', License::class);

                    Route::middleware(['can:view,license'])->prefix('{license}')->whereNumber('license')->group(
                        function () {
                            Route::get('/', static function (LegalEntity $legalEntity, License $license) {
                                if (Gate::allows('update', [$license, $legalEntity]) &&
                                    !$license->isPrimary && $legalEntity->type === LegalEntity::TYPE_PHARMACY) {
                                    return App::call(LicenseEdit::class, [$legalEntity, $license]);
                                } elseif (Gate::allows('view', [$license, $legalEntity])) {
                                    return App::call(LicenseView::class, [$legalEntity, $license]);
                                }

                                // If both check is false
                                abort(404);
                            })->name('view');
                        }
                    );
                });

            Route::get('/declaration', DeclarationIndex::class)
                ->name('declaration.index')
                ->can('viewAny', Declaration::class);

            Route::prefix('patient')->group(static function () {
                Route::name('patient.')->group(static function () {
                    Route::get('/', PatientIndex::class)->can('viewAny', Person::class)->name('index');
                    Route::get('/create', PatientCreate::class)->can('create', PersonRequest::class)->name('create');
                    Route::get('/edit/{id}', PatientEdit::class)->can('create', PersonRequest::class)->name('edit');

                    Route::middleware('can:view,' . Person::class)->group(function () {
                        Route::get('/{patientId}/patient-data', PatientData::class)->name('patient-data');
                        Route::get('/{patientId}/summary', PatientSummary::class)->name('summary');
                        Route::get('/{patientId}/episodes', PatientEpisodes::class)->name('episodes');
                    });
                });

                Route::name('declaration.')->group(static function () {
                    Route::get('/declaration/{declaration}', DeclarationView::class)
                        ->can('view', 'declaration')
                        ->name('view')
                        ->whereNumber('declaration');
                    Route::get('/{patientId}/declaration/create', DeclarationCreate::class)
                        ->name('create')
                        ->can('create', DeclarationRequest::class)
                        ->whereNumber('patientId');
                    Route::get('/{patientId}/declaration/{declarationRequest}', DeclarationEdit::class)
                        ->name('edit')
                        ->can('update', 'declarationRequest')
                        ->whereNumber(['patientId', 'declarationRequest']);
                });

                Route::middleware('can:create,' . Encounter::class)->name('encounter.')->group(function () {
                    Route::get('/{patientId}/encounter/create', EncounterCreate::class)->name('create');
                    Route::get('/{patientId}/encounter/{encounterId}', EncounterEdit::class)->name('edit');
                });

                Route::whereNumber('patientId')->group(static function () {
                    Route::get('{patientId}/diagnostic-report/create', DiagnosticReportCreate::class)
                        ->can('create', DiagnosticReport::class)
                        ->name('diagnostic-report.create');

                    Route::get('{patientId}/procedure/create', ProcedureCreate::class)
                        ->can('create', Procedure::class)
                        ->name('procedure.create');
                });
            });
        });
});

Route::get('/page-not-found', fn () => view('errors.404'))->name('url.page-not-found');

/*
 * GLOBAL FALLBACK ROUTE (MUST BE LAST IN web.php)
 * This Route::fallback() will trigger for ANY request that has not been matched by any route above.
 * This is final 404 handler for both authenticated and unauthenticated users,
 * or for routes that simply do not fit into any structured groups.
 */
Route::fallback(fn () => redirect()->route('url.page-not-found'));
