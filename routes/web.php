<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});
Auth::routes(['verify' => true]);
// Route::get('login', [CustomAuthController::class, 'index'])->name('login');

// Auth::routes();

/**
 * Client
 */
Route::resource('/client', App\Http\Controllers\ClientController::class);

/**
 * dashboard
 */
Route::get('/dashboard/{id}', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard',[App\Http\Controllers\DashboardController::class,'getList']);
/**
     * change passord
     */
    Route::get('change-password',[App\Http\Controllers\DashboardController::class,'changePassword'])->name('change_password');
    Route::post('update-password',[App\Http\Controllers\DashboardController::class,'updatePassword'])->name('change-password.update');
    Route::middleware(['sessioncheck'])->group(function () {

        /**
         * Consumption Profile
         */
    Route::get('get_discoms/{stateId}', [App\Http\Controllers\ConsumptionProfileController::class, 'getDiscom'])->name('getDiscom');
    Route::get('get_wheeling_charge/{discomId}', [App\Http\Controllers\ConsumptionProfileController::class, 'getWheelingCharge'])->name('getWheelingCharge');
    Route::post('/tod_value/{id}', [App\Http\Controllers\ConsumptionProfileController::class, 'SaveTodTime'])->name('consumption_profile.tod');
    Route::post('/tod_delete/{id}',[App\Http\Controllers\ConsumptionProfileController::class,'DeleteTodTime'])->name('tod-delete');
    Route::resource('/consumption_profile', App\Http\Controllers\ConsumptionProfileController::class);
    Route::post('/consumption_profile_tod', [App\Http\Controllers\ConsumptionProfileController::class, 'getTodData'])->name('getTodData');
    Route::post('/update/granularity/{id}', [App\Http\Controllers\ConsumptionProfileController::class, 'updateGranularity'])->name('granularity.update');
    Route::post('/source_update/granularity/{id}', [App\Http\Controllers\SourceProfileController::class, 'updateSourceGranularity'])->name('source_update');
    Route::post('calculating_data', [App\Http\Controllers\ConsumptionProfileController::class, 'ConvertConsumotion'])->name('convert_consumption');
    Route::post('export_conversion', [App\Http\Controllers\ConsumptionProfileController::class, 'ExportConvertConsumotion'])->name('export_convert_consumption');
    Route::get('get_slots/{stateId}', [App\Http\Controllers\ConsumptionProfileController::class, 'getStateSlot'])->name('getStateSlot');
    // Route::get('/projects/{id}', [App\Http\Controllers\ProjectController::class, 'index'])->name('project.index');
    // Route::resource('/project', App\Http\Controllers\ProjectController::class);

    /**
     * source profile
     */
    Route::resource('/source_profile', App\Http\Controllers\SourceProfileController::class);
    Route::post('source_calculating_data', [App\Http\Controllers\SourceProfileController::class, 'ConvertSource'])->name('source_convert_consumption');
    Route::post('source_profile/export_conversion', [App\Http\Controllers\SourceProfileController::class, 'ExportConvertSource'])->name('source_export_convert_consumption');


    /**
     * Mapping
     */
    Route::resource('/mapping',App\Http\Controllers\MappingController::class);
    Route::post('convert_mapping_table', [App\Http\Controllers\MappingController::class, 'GenerateMappingTable'])->name('mapping_table');
    Route::post('mapping/export_conversion', [App\Http\Controllers\MappingController::class, 'ExportConvertMapping'])->name('mapping_export');
    Route::post('mapping/many-to-many-mapping', [App\Http\Controllers\MappingController::class, 'ManyToManyMapping'])->name('many-to-many-mapping');
    Route::get('mapping/get_source_profile/{id}', [App\Http\Controllers\MappingController::class, 'GetSourceProfile'])->name('get_source_profile');

    /**
     * Project
     */
    Route::resource('/project',App\Http\Controllers\ProjectController::class);
    Route::post('/project-detail-component', [App\Http\Controllers\ProjectController::class, 'ProjectDetailComponent'])->name('project-detail-component.store');
    Route::post('loan-component', [App\Http\Controllers\ProjectController::class, 'LoanComponent'])->name('loan-component.store');
    Route::post('annual-maintenance-component', [App\Http\Controllers\ProjectController::class, 'AnnualMaintenanceComponent'])->name('annual-maintenance-component');
    Route::post('insurance-component', [App\Http\Controllers\ProjectController::class, 'InsuranceComponent'])->name('insurance-component');
    Route::post('recovery-component', [App\Http\Controllers\ProjectController::class, 'RecoveryComponent'])->name('recovery-component');
    Route::post('transmission-charges-component', [App\Http\Controllers\ProjectController::class, 'TransmissionChargesComponent'])->name('transmission-charges-component');
    Route::post('wheeling-charge-component', [App\Http\Controllers\ProjectController::class, 'WheelingChargesComponent'])->name('wheeling-charge-component');
    Route::post('banking-component', [App\Http\Controllers\ProjectController::class, 'BankingComponent'])->name('banking-component');
    Route::post('peak-banking-component', [App\Http\Controllers\ProjectController::class, 'PeakBankingComponent'])->name('peak-banking-component');
    Route::post('electricity-component', [App\Http\Controllers\ProjectController::class, 'ElectricityComponent'])->name('electricity-component');

});
