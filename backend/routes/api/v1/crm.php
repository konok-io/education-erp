<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\CRM\CrmController;
use App\Http\Controllers\Api\V1\CRM\CommunicationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CRM, Communication & Helpdesk Routes
|--------------------------------------------------------------------------
*/

Route::prefix('crm')->middleware(['auth:sanctum'])->group(function () {

    // ===================== DASHBOARD =====================
    Route::get('/dashboard', [CrmController::class, 'getDashboard'])->name('crm.dashboard');

    // ===================== CONTACTS =====================
    Route::prefix('contacts')->group(function () {
        Route::get('/', [CrmController::class, 'getContacts'])->name('crm.contacts');
        Route::post('/', [CrmController::class, 'createContact'])->name('crm.contacts.create');
        Route::put('/{uuid}', [CrmController::class, 'updateContact'])->name('crm.contacts.update');
        Route::get('/stats', [CrmController::class, 'getContactStats'])->name('crm.contacts.stats');
    });

    // ===================== LEADS =====================
    Route::prefix('leads')->group(function () {
        Route::get('/', [CrmController::class, 'getLeads'])->name('crm.leads');
        Route::post('/', [CrmController::class, 'createLead'])->name('crm.leads.create');
        Route::post('/{uuid}/stage', [CrmController::class, 'updateLeadStage'])->name('crm.leads.stage');
        Route::post('/{uuid}/assign', [CrmController::class, 'assignCounselor'])->name('crm.leads.assign');
        Route::get('/pipeline', [CrmController::class, 'getLeadPipeline'])->name('crm.leads.pipeline');
        Route::get('/stats', [CrmController::class, 'getLeadStats'])->name('crm.leads.stats');
    });

    // ===================== TICKETS =====================
    Route::prefix('tickets')->group(function () {
        Route::get('/', [CrmController::class, 'getTickets'])->name('crm.tickets');
        Route::post('/', [CrmController::class, 'createTicket'])->name('crm.tickets.create');
        Route::post('/{uuid}/assign', [CrmController::class, 'assignTicket'])->name('crm.tickets.assign');
        Route::post('/{uuid}/status', [CrmController::class, 'updateTicketStatus'])->name('crm.tickets.status');
        Route::post('/{uuid}/reply', [CrmController::class, 'addTicketReply'])->name('crm.tickets.reply');
        Route::get('/stats', [CrmController::class, 'getTicketStats'])->name('crm.tickets.stats');
    });

    // ===================== CAMPAIGNS =====================
    Route::prefix('campaigns')->group(function () {
        Route::get('/', [CrmController::class, 'getCampaigns'])->name('crm.campaigns');
        Route::post('/', [CrmController::class, 'createCampaign'])->name('crm.campaigns.create');
        Route::post('/{uuid}/status', [CrmController::class, 'updateCampaignStatus'])->name('crm.campaigns.status');
        Route::get('/stats', [CrmController::class, 'getCampaignStats'])->name('crm.campaigns.stats');
    });

    // ===================== COMMUNICATIONS =====================
    Route::prefix('communications')->group(function () {
        Route::get('/', [CommunicationController::class, 'getCommunications'])->name('crm.communications');
        Route::post('/', [CommunicationController::class, 'sendCommunication'])->name('crm.communications.send');
    });

    // ===================== ANNOUNCEMENTS =====================
    Route::prefix('announcements')->group(function () {
        Route::get('/', [CommunicationController::class, 'getAnnouncements'])->name('crm.announcements');
        Route::post('/', [CommunicationController::class, 'createAnnouncement'])->name('crm.announcements.create');
        Route::post('/{uuid}/publish', [CommunicationController::class, 'publishAnnouncement'])->name('crm.announcements.publish');
    });

    // ===================== FEEDBACK =====================
    Route::prefix('feedback')->group(function () {
        Route::get('/', [CommunicationController::class, 'getFeedbacks'])->name('crm.feedback');
        Route::post('/', [CommunicationController::class, 'submitFeedback'])->name('crm.feedback.submit');
    });

    // ===================== SURVEYS =====================
    Route::prefix('surveys')->group(function () {
        Route::get('/', [CommunicationController::class, 'getSurveys'])->name('crm.surveys');
        Route::post('/', [CommunicationController::class, 'createSurvey'])->name('crm.surveys.create');
        Route::post('/{uuid}/respond', [CommunicationController::class, 'submitSurveyResponse'])->name('crm.surveys.respond');
    });
});
