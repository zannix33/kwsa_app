<?php

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


//use App\Http\Controllers\AreaController;
//use App\Http\Controllers\BranchController;
//use App\Http\Controllers\PayrollPeriodController;//

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::group(['middleware' => ['auth']], function () {

    Route::get('/', 'DashboardController@index')->name('home');


    //Route::get('/', function () {
    //    return view('dashboard');
    //});

// Route::get('/','DashboardController@index');
    Route::prefix('hr')->name('hr.')->group(function(){
        Route::resource('employee', EmployeeController::class);
    });

    Route::prefix('clients')->name('clients.')->group(function(){
        Route::resource('companies', CompanyController::class);
    });

    Route::post(
        '/areas/assign-guard',
        [\App\Http\Controllers\AreaController::class, 'assignGuard']
    )->name('areas.assign.guard');

    Route::get('/areas/{area}/branches', [\App\Http\Controllers\AreaController::class, 'branches']);

    Route::get('/areas/{area}/guards', [\App\Http\Controllers\AreaController::class, 'guards']);

    Route::resource('areas', AreaController::class);


    Route::resource('branches', BranchController::class);
    Route::get(
        '/branches/{branch}/guards',
        [\App\Http\Controllers\BranchController::class, 'guards']
    );

    Route::post(
        '/branches/assign-guard',
        [\App\Http\Controllers\BranchController::class, 'assignGuard']
    );

    //DTR

    Route::prefix('dtr')->group(function () {

        Route::get(
            '/bulk/create',
            [\App\Http\Controllers\DailyTimeRecordController::class,'createBulk']
        )->name('dtr.bulk.create');

        Route::post(
            '/bulk/generate',
            [\App\Http\Controllers\DailyTimeRecordController::class,'generateBulk']
        )->name('dtr.bulk.generate');

        Route::post(
            '/bulk/store',
            [\App\Http\Controllers\DailyTimeRecordController::class,'bulkStore']
        )->name('dtr.bulk.store');

    });

    //Route::resource('dtr', DailyTimeRecordController::class);

    // Settings

    Route::resource('sss-contributions', SssContributionController::class);

    Route::resource('payroll-rates', PayrollRateController::class);

    // Payrolls
    Route::resource(
        'payroll-periods',
        PayrollPeriodController::class
    );

    Route::post(
        '/payroll-periods/{payrollPeriod}/generate',
        [\App\Http\Controllers\PayrollPeriodController::class, 'generate']
    )->name('payrolls.generate');

    Route::post(
        '/payroll-generation/{payrollPeriod}',
        [\App\Http\Controllers\PayrollGenerationController::class, 'generate']
    )->name('payroll-generation.generate');

    Route::resource('payrolls', PayrollController::class)
        ->only([
            'index',
            'show',
            'destroy'
        ]);

    Route::post(
        'payrolls/{payroll}/mark-paid',
        [PayrollController::class, 'markPaid']
    )->name('payrolls.mark-paid');

    // Government Deduction
    Route::resource(
        'sss',
        SssContributionController::class
    );

    // Report PDFs
    Route::get(
        '/payrolls/{payroll}/pdf',
        [\App\Http\Controllers\PayrollController::class, 'pdf']
    )->name('payrolls.pdf');

    // Thirtheenth Month Pay
    Route::get('/reports/thirteenth-month', [\App\Http\Controllers\ThirteenthMonthController::class, 'index'])
        ->name('reports.13th.index');

    Route::post('/reports/thirteenth-month', [\App\Http\Controllers\ThirteenthMonthController::class, 'generate'])
        ->name('reports.13th.generate');

    // Incident Reports
    Route::get(
        '/incidents/employee/{user}',
        [\App\Http\Controllers\IncidentController::class, 'employeeInformation']
    )->name('incidents.employee');

    Route::get(
        '/incidents/{incident}/print',
        [\App\Http\Controllers\IncidentController::class, 'print']
    )->name('incidents.print');

    Route::delete(
        '/incident-attachments/{attachment}',
        [\App\Http\Controllers\IncidentController::class,'deleteAttachment']
    )->name('incident.attachments.destroy');

    Route::get('/incident-dashboard', [\App\Http\Controllers\IncidentController::class, 'dashboard'])
        ->name('incidents.dashboard');

    Route::get('/incidents/datatables',
        [\App\Http\Controllers\IncidentController::class,'datatable'])
        ->name('incidents.datatable');

    Route::get('/incidents/export/excel',
        [\App\Http\Controllers\IncidentController::class,'excel'])
        ->name('incidents.excel');

    Route::get('/incidents/export/pdf',
        [\App\Http\Controllers\IncidentController::class,'pdf'])
        ->name('incidents.pdf');

    Route::resource('incidents', IncidentController::class);

    // Arms

    Route::prefix('arms')
        ->name('arms.')
        ->group(function () {

            Route::get('/', [\App\Http\Controllers\Arms\ArmController::class,'index'])
                ->name('index');

            Route::get('/datatable', [\App\Http\Controllers\Arms\ArmController::class,'datatable'])
                ->name('datatable');

            Route::get('/create', [\App\Http\Controllers\Arms\ArmController::class,'create'])
                ->name('create');

            Route::post('/', [\App\Http\Controllers\Arms\ArmController::class,'store'])
                ->name('store');

            Route::get('/{arm}', [\App\Http\Controllers\Arms\ArmController::class,'show'])
                ->name('show');

            Route::get('/{arm}/edit', [\App\Http\Controllers\Arms\ArmController::class,'edit'])
                ->name('edit');

            Route::put('/{arm}', [\App\Http\Controllers\Arms\ArmController::class,'update'])
                ->name('update');

            Route::delete('/{arm}', [\App\Http\Controllers\Arms\ArmController::class,'destroy'])
                ->name('destroy');

            Route::post('/{arm}/retire', [\App\Http\Controllers\Arms\ArmController::class,'retire'])
                ->name('retire');

            Route::post('/{arm}/lost', [\App\Http\Controllers\Arms\ArmController::class,'lost'])
                ->name('lost');

            Route::post('/{arm}/available', [\App\Http\Controllers\Arms\ArmController::class,'available'])
                ->name('available');

        });

    // Arms Assignment

    Route::prefix('arms/assignments')
        ->name('arms.assignments.')
        ->group(function () {

            Route::get('/dashboard', [ArmDashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('/dashboard/charts', [ArmDashboardController::class, 'charts'])
                ->name('dashboard.charts');

            Route::get('/dashboard/summary', [ArmDashboardController::class, 'summary'])
                ->name('dashboard.summary');


            Route::get('/', [ArmAssignmentController::class, 'index'])->name('index');

            Route::get('/datatable', [ArmAssignmentController::class, 'datatable'])->name('datatable');

            Route::get('/create', [ArmAssignmentController::class, 'create'])->name('create');

            Route::post('/', [ArmAssignmentController::class, 'store'])->name('store');

            Route::get('/current', [ArmAssignmentController::class, 'current'])->name('current');

            Route::get('/overdue', [ArmAssignmentController::class, 'overdue'])->name('overdue');

            Route::get('/employee/{user}', [ArmAssignmentController::class, 'employee'])->name('employee');

            Route::get('/firearm/{arm}', [ArmAssignmentController::class, 'firearm'])->name('firearm');

            Route::get('/{assignment}', [ArmAssignmentController::class, 'show'])->name('show');

            Route::get('/{assignment}/return', [ArmAssignmentController::class, 'edit'])->name('edit');

            Route::put('/{assignment}', [ArmAssignmentController::class, 'update'])->name('update');

            Route::delete('/{assignment}', [ArmAssignmentController::class, 'destroy'])->name('destroy');

        });

    // Arms Maintenance

    Route::prefix('arms/maintenances')
        ->name('arms.maintenances.')
        ->group(function () {

            Route::get('/', [ArmMaintenanceController::class, 'index'])->name('index');

            Route::get('/datatable', [ArmMaintenanceController::class, 'datatable'])->name('datatable');

            Route::get('/create', [ArmMaintenanceController::class, 'create'])->name('create');

            Route::post('/', [ArmMaintenanceController::class, 'store'])->name('store');

            Route::get('/due', [ArmMaintenanceController::class, 'due'])->name('due');

            Route::get('/firearm/{arm}', [ArmMaintenanceController::class, 'firearm'])->name('firearm');

            Route::get('/{maintenance}', [ArmMaintenanceController::class, 'show'])->name('show');

            Route::get('/{maintenance}/edit', [ArmMaintenanceController::class, 'edit'])->name('edit');

            Route::put('/{maintenance}', [ArmMaintenanceController::class, 'update'])->name('update');

            Route::delete('/{maintenance}', [ArmMaintenanceController::class, 'destroy'])->name('destroy');

            Route::post('/{maintenance}/complete', [ArmMaintenanceController::class, 'complete'])->name('complete');

        });

    //Arm Inspection

    Route::prefix('arms/inspections')
        ->name('arms.inspections.')
        ->group(function () {

            Route::get('/', [ArmInspectionController::class, 'index'])->name('index');

            Route::get('/datatable', [ArmInspectionController::class, 'datatable'])->name('datatable');

            Route::get('/create', [ArmInspectionController::class, 'create'])->name('create');

            Route::post('/', [ArmInspectionController::class, 'store'])->name('store');

            Route::get('/due', [ArmInspectionController::class, 'due'])->name('due');

            Route::get('/failed', [ArmInspectionController::class, 'failed'])->name('failed');

            Route::get('/passed', [ArmInspectionController::class, 'passed'])->name('passed');

            Route::get('/firearm/{arm}', [ArmInspectionController::class, 'firearm'])->name('firearm');

            Route::get('/{inspection}', [ArmInspectionController::class, 'show'])->name('show');

            Route::get('/{inspection}/edit', [ArmInspectionController::class, 'edit'])->name('edit');

            Route::put('/{inspection}', [ArmInspectionController::class, 'update'])->name('update');

            Route::delete('/{inspection}', [ArmInspectionController::class, 'destroy'])->name('destroy');

        });

    // Arm License
    Route::prefix('arms/licenses')
        ->name('arms.licenses.')
        ->group(function () {

            Route::get('/', [ArmLicenseController::class, 'index'])->name('index');

            Route::get('/datatable', [ArmLicenseController::class, 'datatable'])->name('datatable');

            Route::get('/create', [\App\Http\Controllers\Arms\ArmLicenseController::class, 'create'])->name('create');

            Route::post('/', [ArmLicenseController::class, 'store'])->name('store');

            Route::get('/expired', [ArmLicenseController::class, 'expired'])->name('expired');

            Route::get('/expiring', [ArmLicenseController::class, 'expiring'])->name('expiring');

            Route::get('/firearm/{arm}', [ArmLicenseController::class, 'firearm'])->name('firearm');

            Route::post('/{license}/renew', [ArmLicenseController::class, 'renew'])->name('renew');

            Route::get('/{license}', [ArmLicenseController::class, 'show'])->name('show');

            Route::get('/{license}/edit', [ArmLicenseController::class, 'edit'])->name('edit');

            Route::put('/{license}', [ArmLicenseController::class, 'update'])->name('update');

            Route::delete('/{license}', [ArmLicenseController::class, 'destroy'])->name('destroy');

        });

    // Ammunition Routes

    Route::prefix('arms/ammunition')
        ->name('arms.ammunition.')
        ->group(function () {

            Route::get('/', [AmmunitionController::class, 'index'])->name('index');

            Route::get('/datatable', [AmmunitionController::class, 'datatable'])->name('datatable');

            Route::get('/create', [AmmunitionController::class, 'create'])->name('create');

            Route::post('/', [AmmunitionController::class, 'store'])->name('store');

            Route::get('/low-stock', [AmmunitionController::class, 'lowStock'])->name('low-stock');

            Route::get('/expired', [AmmunitionController::class, 'expired'])->name('expired');

            Route::get('/valuation', [AmmunitionController::class, 'valuation'])->name('valuation');

            Route::get('/{ammunition}', [AmmunitionController::class, 'show'])->name('show');

            Route::get('/{ammunition}/edit', [AmmunitionController::class, 'edit'])->name('edit');

            Route::put('/{ammunition}', [AmmunitionController::class, 'update'])->name('update');

            Route::delete('/{ammunition}', [AmmunitionController::class, 'destroy'])->name('destroy');

            Route::post('/{ammunition}/receive', [AmmunitionController::class, 'receive'])->name('receive');

            Route::post('/{ammunition}/adjust', [AmmunitionController::class, 'adjust'])->name('adjust');

        });

    // Ammunition Releases

    Route::prefix('arms/ammunition-releases')
        ->name('arms.ammunition-releases.')
        ->group(function () {

            Route::get('/', [AmmunitionReleaseController::class,'index'])->name('index');

            Route::get('/datatable', [AmmunitionReleaseController::class,'datatable'])->name('datatable');

            Route::get('/create', [AmmunitionReleaseController::class,'create'])->name('create');

            Route::post('/', [AmmunitionReleaseController::class,'store'])->name('store');

            Route::get('/outstanding', [AmmunitionReleaseController::class,'outstanding'])->name('outstanding');

            Route::get('/employee/{user}', [AmmunitionReleaseController::class,'employee'])->name('employee');

            Route::get('/ammunition/{ammunition}', [AmmunitionReleaseController::class,'ammunition'])->name('ammunition');

            Route::get('/{ammunitionRelease}', [AmmunitionReleaseController::class,'show'])->name('show');

            Route::get('/{ammunitionRelease}/return', [AmmunitionReleaseController::class,'edit'])->name('edit');

            Route::put('/{ammunitionRelease}', [AmmunitionReleaseController::class,'update'])->name('update');

            Route::delete('/{ammunitionRelease}', [AmmunitionReleaseController::class,'destroy'])->name('destroy');

        });

    // Arms
    //Route::resource('arms', ArmController::class);
    //Route::get('arms/datatable',[ArmController::class,'datatable'])
    //    ->name('arms.datatable');


    Route::group(['prefix' => 'basic-ui'], function(){
        Route::get('accordions', function () { return view('pages.basic-ui.accordions'); });
        Route::get('buttons', function () { return view('pages.basic-ui.buttons'); });
        Route::get('badges', function () { return view('pages.basic-ui.badges'); });
        Route::get('breadcrumbs', function () { return view('pages.basic-ui.breadcrumbs'); });
        Route::get('dropdowns', function () { return view('pages.basic-ui.dropdowns'); });
        Route::get('modals', function () { return view('pages.basic-ui.modals'); });
        Route::get('progress-bar', function () { return view('pages.basic-ui.progress-bar'); });
        Route::get('pagination', function () { return view('pages.basic-ui.pagination'); });
        Route::get('tabs', function () { return view('pages.basic-ui.tabs'); });
        Route::get('typography', function () { return view('pages.basic-ui.typography'); });
        Route::get('tooltips', function () { return view('pages.basic-ui.tooltips'); });
    });

    Route::group(['prefix' => 'advanced-ui'], function(){
        Route::get('dragula', function () { return view('pages.advanced-ui.dragula'); });
        Route::get('clipboard', function () { return view('pages.advanced-ui.clipboard'); });
        Route::get('context-menu', function () { return view('pages.advanced-ui.context-menu'); });
        Route::get('popups', function () { return view('pages.advanced-ui.popups'); });
        Route::get('sliders', function () { return view('pages.advanced-ui.sliders'); });
        Route::get('carousel', function () { return view('pages.advanced-ui.carousel'); });
        Route::get('loaders', function () { return view('pages.advanced-ui.loaders'); });
        Route::get('tree-view', function () { return view('pages.advanced-ui.tree-view'); });
    });

    Route::group(['prefix' => 'forms'], function(){
        Route::get('basic-elements', function () { return view('pages.forms.basic-elements'); });
        Route::get('advanced-elements', function () { return view('pages.forms.advanced-elements'); });
        Route::get('dropify', function () { return view('pages.forms.dropify'); });
        Route::get('form-validation', function () { return view('pages.forms.form-validation'); });
        Route::get('step-wizard', function () { return view('pages.forms.step-wizard'); });
        Route::get('wizard', function () { return view('pages.forms.wizard'); });
    });

    Route::group(['prefix' => 'editors'], function(){
        Route::get('text-editor', function () { return view('pages.editors.text-editor'); });
        Route::get('code-editor', function () { return view('pages.editors.code-editor'); });
    });

    Route::group(['prefix' => 'charts'], function(){
        Route::get('chartjs', function () { return view('pages.charts.chartjs'); });
        Route::get('morris', function () { return view('pages.charts.morris'); });
        Route::get('flot', function () { return view('pages.charts.flot'); });
        Route::get('google-charts', function () { return view('pages.charts.google-charts'); });
        Route::get('sparklinejs', function () { return view('pages.charts.sparklinejs'); });
        Route::get('c3-charts', function () { return view('pages.charts.c3-charts'); });
        Route::get('chartist', function () { return view('pages.charts.chartist'); });
        Route::get('justgage', function () { return view('pages.charts.justgage'); });
    });

    Route::group(['prefix' => 'tables'], function(){
        Route::get('basic-table', function () { return view('pages.tables.basic-table'); });
        Route::get('data-table', function () { return view('pages.tables.data-table'); });
        Route::get('js-grid', function () { return view('pages.tables.js-grid'); });
        Route::get('sortable-table', function () { return view('pages.tables.sortable-table'); });
    });

    Route::get('notifications', function () {
        return view('pages.notifications.index');
    });

    Route::group(['prefix' => 'icons'], function(){
        Route::get('material', function () { return view('pages.icons.material'); });
        Route::get('flag-icons', function () { return view('pages.icons.flag-icons'); });
        Route::get('font-awesome', function () { return view('pages.icons.font-awesome'); });
        Route::get('simple-line-icons', function () { return view('pages.icons.simple-line-icons'); });
        Route::get('themify', function () { return view('pages.icons.themify'); });
    });

    Route::group(['prefix' => 'maps'], function(){
        Route::get('vector-map', function () { return view('pages.maps.vector-map'); });
        Route::get('mapael', function () { return view('pages.maps.mapael'); });
        Route::get('google-maps', function () { return view('pages.maps.google-maps'); });
    });

    Route::group(['prefix' => 'user-pages'], function(){
        Route::get('login', function () { return view('pages.user-pages.login'); });
        Route::get('login-2', function () { return view('pages.user-pages.login-2'); });
        Route::get('multi-step-login', function () { return view('pages.user-pages.multi-step-login'); });
        Route::get('register', function () { return view('pages.user-pages.register'); });
        Route::get('register-2', function () { return view('pages.user-pages.register-2'); });
        Route::get('lock-screen', function () { return view('pages.user-pages.lock-screen'); });
    });

    Route::group(['prefix' => 'error-pages'], function(){
        Route::get('error-404', function () { return view('pages.error-pages.error-404'); });
        Route::get('error-500', function () { return view('pages.error-pages.error-500'); });
    });

    Route::group(['prefix' => 'general-pages'], function(){
        Route::get('blank-page', function () { return view('pages.general-pages.blank-page'); });
        Route::get('landing-page', function () { return view('pages.general-pages.landing-page'); });
        Route::get('profile', function () { return view('pages.general-pages.profile'); });
        Route::get('email-templates', function () { return view('pages.general-pages.email-templates'); });
        Route::get('faq', function () { return view('pages.general-pages.faq'); });
        Route::get('faq-2', function () { return view('pages.general-pages.faq-2'); });
        Route::get('news-grid', function () { return view('pages.general-pages.news-grid'); });
        Route::get('timeline', function () { return view('pages.general-pages.timeline'); });
        Route::get('search-results', function () { return view('pages.general-pages.search-results'); });
        Route::get('portfolio', function () { return view('pages.general-pages.portfolio'); });
        Route::get('user-listing', function () { return view('pages.general-pages.user-listing'); });
    });

    Route::group(['prefix' => 'ecommerce'], function(){
        Route::get('invoice', function () { return view('pages.ecommerce.invoice'); });
        Route::get('invoice-2', function () { return view('pages.ecommerce.invoice-2'); });
        Route::get('pricing', function () { return view('pages.ecommerce.pricing'); });
        Route::get('product-catalogue', function () { return view('pages.ecommerce.product-catalogue'); });
        Route::get('project-list', function () { return view('pages.ecommerce.project-list'); });
        Route::get('orders', function () { return view('pages.ecommerce.orders'); });
    });

// For Clear cache
    Route::get('/clear-cache', function() {
        Artisan::call('cache:clear');
        return "Cache is cleared";
    });

// 404 for undefined routes
    Route::any('/{page?}',function(){
        return View::make('pages.error-pages.error-404');

    })->where('page','.*');



});


