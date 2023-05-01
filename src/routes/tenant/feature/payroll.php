<?php

use App\Http\Controllers\Tenant\Payroll\BeneficiaryBadgeController;
use App\Http\Controllers\Tenant\Payroll\ManualPayrunController;
use App\Http\Controllers\Tenant\Payroll\PayrollSettingController;
use App\Http\Controllers\Tenant\Payroll\PayrunController;
use App\Http\Controllers\Tenant\Payroll\PayslipController;
use App\Http\Controllers\Tenant\Payroll\RunDefaultPayrun;
use Illuminate\Routing\Router;

Route::group(['prefix' => 'app', ], function (Router $router) {

    $router->get('settings/payrun', [PayrollSettingController::class, 'index'])
        ->name('payroll-settings.index');

    $router->post('settings/payrun', [PayrollSettingController::class, 'updateDefault'])
        ->name('payrun-period.update');

    $router->post('settings/payrun/audience', [PayrollSettingController::class, 'updateAudience'])
        ->name('payrun-audience.update');

    $router->post('settings/payrun/beneficiaries', [PayrollSettingController::class, 'updateBeneficiaries'])
        ->name('payrun-beneficiary.update');

    $router->apiResource('beneficiaries', BeneficiaryBadgeController::class);

    $router->get('payslip', [PayslipController::class, 'index'])
        ->name('payslips.index');

    $router->get('payslip/{payslip}/send', [PayslipController::class, 'sendPayslip'])
        ->name('individual-payslip.send');

    $router->get('payslip/{payslip}/delete', [PayslipController::class, 'destroy'])
        ->name('payslip.delete');

    $router->get('payslip/{payslip}/pdf', [PayslipController::class, 'showPdf'])
        ->name('payslip-pdf.index');

    $router->patch('payslip/{payslip}/update', [PayslipController::class, 'update'])
        ->name('payslip.update');

    $router->get('payslip/send-monthly', [PayslipController::class, 'sendMonthlyPayslip'])
        ->name('bulk-payslip.send');

    $router->post('payrun/default', [RunDefaultPayrun::class, 'store'])
        ->name('default-payrun.run');

    $router->post('payrun/manual', [ManualPayrunController::class, 'store'])
        ->name('manual-payrun.run');

    $router->get('payruns', [PayrunController::class, 'index'])
        ->name('payruns.index');

    $router->delete('payruns/{payrun}', [PayrunController::class, 'delete'])
        ->name('payruns.delete');

    $router->patch('payruns/{payrun}', [ManualPayrunController::class, 'update'])
        ->name('payruns.update');

    $router->get('payruns/{payrun}', [ManualPayrunController::class, 'index'])
        ->name('payruns.index');

    $router->get('payruns/{payrun}/send-payslip', [PayrunController::class, 'sendPayslips'])
        ->name('payrun-payslips.send');

});