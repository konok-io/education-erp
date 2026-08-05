<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\HR\HRController;
use App\Http\Controllers\Api\V1\HR\RecruitmentController;
use App\Http\Controllers\Api\V1\HR\EmployeeHRController;
use App\Http\Controllers\Api\V1\HR\TrainingAwardController;
use App\Http\Controllers\Api\V1\HR\CertificateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| HR, Payroll & Leave Management Routes
|--------------------------------------------------------------------------
*/

Route::prefix('hr')->middleware(['auth:sanctum'])->group(function () {

    // ===================== SALARY GRADES =====================
    Route::get('/salary-grades', [HRController::class, 'getSalaryGrades'])->name('hr.salary-grades');
    Route::post('/salary-grades', [HRController::class, 'createSalaryGrade'])->name('hr.salary-grades.create');

    // ===================== PAYROLL =====================
    Route::get('/payrolls', [HRController::class, 'getPayrolls'])->name('hr.payrolls');
    Route::post('/payrolls', [HRController::class, 'processPayroll'])->name('hr.payrolls.process');
    Route::post('/payrolls/bulk', [HRController::class, 'processBulkPayroll'])->name('hr.payrolls.bulk');
    Route::post('/payrolls/{uuid}/approve', [HRController::class, 'approvePayroll'])->name('hr.payrolls.approve');
    Route::post('/payrolls/{uuid}/pay', [HRController::class, 'payPayroll'])->name('hr.payrolls.pay');
    Route::get('/payrolls/{uuid}/payslip', [HRController::class, 'getPayslip'])->name('hr.payrolls.payslip');

    // ===================== LEAVE TYPES =====================
    Route::get('/leave-types', [HRController::class, 'getLeaveTypes'])->name('hr.leave-types');
    Route::post('/leave-types', [HRController::class, 'createLeaveType'])->name('hr.leave-types.create');

    // ===================== LEAVES =====================
    Route::get('/leaves', [HRController::class, 'getLeaves'])->name('hr.leaves');
    Route::post('/leaves', [HRController::class, 'applyLeave'])->name('hr.leaves.apply');
    Route::post('/leaves/{uuid}/approve', [HRController::class, 'approveLeave'])->name('hr.leaves.approve');
    Route::post('/leaves/{uuid}/reject', [HRController::class, 'rejectLeave'])->name('hr.leaves.reject');
    Route::get('/leaves/balance/{employeeId}', [HRController::class, 'getLeaveBalance'])->name('hr.leaves.balance');

    // ===================== HOLIDAYS =====================
    Route::get('/holidays', [HRController::class, 'getHolidays'])->name('hr.holidays');
    Route::post('/holidays', [HRController::class, 'createHoliday'])->name('hr.holidays.create');

    // ===================== LOANS =====================
    Route::get('/loans', [HRController::class, 'getLoans'])->name('hr.loans');
    Route::post('/loans', [HRController::class, 'createLoan'])->name('hr.loans.create');
    Route::post('/loans/{uuid}/approve', [HRController::class, 'approveLoan'])->name('hr.loans.approve');
    Route::get('/loans/balance/{employeeId}', [HRController::class, 'getLoanBalance'])->name('hr.loans.balance');

    // ===================== OVERTIME =====================
    Route::get('/overtimes', [HRController::class, 'getOvertimes'])->name('hr.overtimes');
    Route::post('/overtimes', [HRController::class, 'createOvertime'])->name('hr.overtimes.create');
    Route::post('/overtimes/{uuid}/approve', [HRController::class, 'approveOvertime'])->name('hr.overtimes.approve');

    // ===================== REPORTS =====================
    Route::get('/reports/payroll', [HRController::class, 'getPayrollReport'])->name('hr.reports.payroll');
    Route::get('/reports/leave', [HRController::class, 'getLeaveReport'])->name('hr.reports.leave');

    // ===================== DASHBOARD =====================
    Route::get('/dashboard', [HRController::class, 'getDashboard'])->name('hr.dashboard');

    // ===================== EXPORT =====================
    Route::get('/export/payslips', [HRController::class, 'exportPayslips'])->name('hr.export.payslips');

    // ===================== RECRUITMENT =====================
    Route::prefix('recruitment')->group(function () {
        Route::get('/circulars', [RecruitmentController::class, 'getJobCirculars'])->name('hr.recruitment.circulars');
        Route::post('/circulars', [RecruitmentController::class, 'createJobCircular'])->name('hr.recruitment.circulars.create');
        Route::post('/circulars/{uuid}/publish', [RecruitmentController::class, 'publishJobCircular'])->name('hr.recruitment.circulars.publish');
        Route::post('/circulars/{uuid}/close', [RecruitmentController::class, 'closeJobCircular'])->name('hr.recruitment.circulars.close');

        Route::get('/applications', [RecruitmentController::class, 'getJobApplications'])->name('hr.recruitment.applications');
        Route::post('/applications', [RecruitmentController::class, 'createJobApplication'])->name('hr.recruitment.applications.create');
        Route::post('/applications/{uuid}/status', [RecruitmentController::class, 'updateApplicationStatus'])->name('hr.recruitment.applications.status');

        Route::get('/interviews', [RecruitmentController::class, 'getInterviews'])->name('hr.recruitment.interviews');
        Route::post('/interviews', [RecruitmentController::class, 'scheduleInterview'])->name('hr.recruitment.interviews.schedule');
        Route::post('/interviews/{uuid}/evaluate', [RecruitmentController::class, 'evaluateCandidate'])->name('hr.recruitment.interviews.evaluate');

        Route::get('/offers', [RecruitmentController::class, 'getOfferLetters'])->name('hr.recruitment.offers');
        Route::post('/offers', [RecruitmentController::class, 'createOfferLetter'])->name('hr.recruitment.offers.create');
        Route::post('/offers/{uuid}/send', [RecruitmentController::class, 'sendOfferLetter'])->name('hr.recruitment.offers.send');
        Route::post('/offers/{uuid}/accept', [RecruitmentController::class, 'acceptOfferLetter'])->name('hr.recruitment.offers.accept');
        Route::post('/offers/{uuid}/decline', [RecruitmentController::class, 'declineOfferLetter'])->name('hr.recruitment.offers.decline');
        Route::post('/offers/{uuid}/joined', [RecruitmentController::class, 'markJoined'])->name('hr.recruitment.offers.joined');

        Route::get('/stats', [RecruitmentController::class, 'getRecruitmentStats'])->name('hr.recruitment.stats');
    });

    // ===================== ONBOARDING =====================
    Route::prefix('onboarding')->group(function () {
        Route::get('/checklists', [EmployeeHRController::class, 'getChecklists'])->name('hr.onboarding.checklists');
        Route::post('/checklists', [EmployeeHRController::class, 'createChecklist'])->name('hr.onboarding.checklists.create');
        Route::get('/', [EmployeeHRController::class, 'getOnboardings'])->name('hr.onboarding');
        Route::post('/', [EmployeeHRController::class, 'startOnboarding'])->name('hr.onboarding.start');
        Route::post('/{uuid}/checklist', [EmployeeHRController::class, 'completeChecklist'])->name('hr.onboarding.checklist.complete');
        Route::get('/{uuid}/progress', [EmployeeHRController::class, 'getOnboardingProgress'])->name('hr.onboarding.progress');
        Route::get('/stats', [EmployeeHRController::class, 'getOnboardingStats'])->name('hr.onboarding.stats');
    });

    // ===================== TRANSFERS =====================
    Route::prefix('transfers')->group(function () {
        Route::get('/', [EmployeeHRController::class, 'getTransfers'])->name('hr.transfers');
        Route::post('/', [EmployeeHRController::class, 'createTransfer'])->name('hr.transfers.create');
        Route::post('/{uuid}/recommend', [EmployeeHRController::class, 'recommendTransfer'])->name('hr.transfers.recommend');
        Route::post('/{uuid}/approve', [EmployeeHRController::class, 'approveTransfer'])->name('hr.transfers.approve');
        Route::post('/{uuid}/cancel', [EmployeeHRController::class, 'cancelTransfer'])->name('hr.transfers.cancel');
        Route::get('/stats', [EmployeeHRController::class, 'getTransferStats'])->name('hr.transfers.stats');
    });

    // ===================== CONFIRMATION =====================
    Route::prefix('confirmation')->group(function () {
        Route::get('/', [EmployeeHRController::class, 'getConfirmations'])->name('hr.confirmation');
        Route::post('/', [EmployeeHRController::class, 'createConfirmation'])->name('hr.confirmation.create');
        Route::post('/{uuid}/recommend', [EmployeeHRController::class, 'recommendConfirmation'])->name('hr.confirmation.recommend');
        Route::post('/{uuid}/approve', [EmployeeHRController::class, 'approveConfirmation'])->name('hr.confirmation.approve');
        Route::get('/stats', [EmployeeHRController::class, 'getConfirmationStats'])->name('hr.confirmation.stats');
    });

    // ===================== SERVICE BOOK =====================
    Route::prefix('service-book')->group(function () {
        Route::get('/', [EmployeeHRController::class, 'getServiceBooks'])->name('hr.service-book');
        Route::post('/', [EmployeeHRController::class, 'createServiceBookEntry'])->name('hr.service-book.create');
        Route::get('/employee/{employeeId}', [EmployeeHRController::class, 'getEmployeeServiceBook'])->name('hr.service-book.employee');
        Route::get('/employee/{employeeId}/timeline', [EmployeeHRController::class, 'getServiceBookTimeline'])->name('hr.service-book.employee.timeline');
        Route::get('/employee/{employeeId}/tenure', [EmployeeHRController::class, 'getEmployeeTenure'])->name('hr.service-book.employee.tenure');
    });

    // ===================== TRAINING =====================
    Route::prefix('training')->group(function () {
        Route::get('/types', [TrainingAwardController::class, 'getTrainingTypes'])->name('hr.training.types');
        Route::post('/types', [TrainingAwardController::class, 'createTrainingType'])->name('hr.training.types.create');
        Route::get('/', [TrainingAwardController::class, 'getTrainingRecords'])->name('hr.training');
        Route::post('/', [TrainingAwardController::class, 'createTrainingRecord'])->name('hr.training.create');
        Route::post('/{uuid}/result', [TrainingAwardController::class, 'updateTrainingResult'])->name('hr.training.result');
        Route::get('/employee/{employeeId}', [TrainingAwardController::class, 'getEmployeeTrainingHistory'])->name('hr.training.employee');
        Route::get('/stats', [TrainingAwardController::class, 'getTrainingStats'])->name('hr.training.stats');
    });

    // ===================== AWARDS =====================
    Route::prefix('awards')->group(function () {
        Route::get('/types', [TrainingAwardController::class, 'getAwardTypes'])->name('hr.awards.types');
        Route::post('/types', [TrainingAwardController::class, 'createAwardType'])->name('hr.awards.types.create');
        Route::get('/', [TrainingAwardController::class, 'getAwards'])->name('hr.awards');
        Route::post('/', [TrainingAwardController::class, 'createAward'])->name('hr.awards.create');
        Route::get('/employee/{employeeId}', [TrainingAwardController::class, 'getEmployeeAwards'])->name('hr.awards.employee');
        Route::get('/stats', [TrainingAwardController::class, 'getAwardStats'])->name('hr.awards.stats');
    });

    // ===================== CERTIFICATES =====================
    Route::prefix('certificates')->group(function () {
        Route::get('/experience', [CertificateController::class, 'getExperienceCertificates'])->name('hr.certificates.experience');
        Route::post('/experience', [CertificateController::class, 'createExperienceCertificate'])->name('hr.certificates.experience.create');
        Route::get('/experience/{uuid}/pdf', [CertificateController::class, 'generateExperiencePdf'])->name('hr.certificates.experience.pdf');
        Route::get('/experience/verify/{code}', [CertificateController::class, 'verifyExperienceCertificate'])->name('hr.certificates.experience.verify');

        Route::get('/noc', [CertificateController::class, 'getNocCertificates'])->name('hr.certificates.noc');
        Route::post('/noc', [CertificateController::class, 'createNocCertificate'])->name('hr.certificates.noc.create');
        Route::get('/noc/{uuid}/pdf', [CertificateController::class, 'generateNocPdf'])->name('hr.certificates.noc.pdf');
        Route::get('/noc/verify/{code}', [CertificateController::class, 'verifyNocCertificate'])->name('hr.certificates.noc.verify');
    });
});

// ===================== PUBLIC VERIFICATION (No Auth Required) =====================
Route::get('/verify/{type}/{code}', [CertificateController::class, 'publicVerify'])->name('hr.verify.public');
