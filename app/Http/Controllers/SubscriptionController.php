<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\BidanApplication;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    /**
     * Get all active subscription plans (Public)
     * GET /api/public/subscription-plans
     */
    public function getPlans(): JsonResponse
    {
        $plans = SubscriptionPlan::active()
            ->ordered()
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Subscription plans retrieved successfully',
            'data' => $plans,
        ]);
    }

    /**
     * Submit bidan application (Public)
     * POST /api/public/bidan-applications
     */
    public function submitApplication(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subscription_plan_id' => 'required|exists:subscription_plans,subscription_plan_id',
            'full_name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:20',
            'bidan_name' => 'required|string|max:150',
            'full_address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'str_number' => 'nullable|string|max:100',
            'sip_number' => 'nullable|string|max:100',
            'document_url' => 'nullable|url|max:500',
        ], [
            'subscription_plan_id.required' => 'Pilih paket langganan',
            'subscription_plan_id.exists' => 'Paket langganan tidak valid',
            'full_name.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'phone.required' => 'Nomor telepon wajib diisi',
            'bidan_name.required' => 'Nama praktik bidan wajib diisi',
            'full_address.required' => 'Alamat lengkap wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if email already has pending application
        $existingApplication = BidanApplication::where('email', $request->email)
            ->where('status', 'pending')
            ->first();

        if ($existingApplication) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email sudah memiliki aplikasi yang sedang diproses',
            ], 409);
        }

        // Check if plan is active
        $plan = SubscriptionPlan::find($request->subscription_plan_id);
        if (!$plan || !$plan->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Paket langganan tidak tersedia',
            ], 400);
        }

        $application = BidanApplication::create([
            'subscription_plan_id' => $request->subscription_plan_id,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'bidan_name' => $request->bidan_name,
            'full_address' => $request->full_address,
            'city' => $request->city,
            'province' => $request->province,
            'str_number' => $request->str_number,
            'sip_number' => $request->sip_number,
            'document_url' => $request->document_url,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Aplikasi berhasil dikirim. Tim kami akan meninjau aplikasi Anda.',
            'data' => [
                'application_id' => $application->bidan_application_id,
                'status' => $application->status,
            ],
        ], 201);
    }

    /**
     * Check application status by email (Public)
     * GET /api/public/bidan-applications/status?email=xxx
     */
    public function checkApplicationStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email tidak valid',
            ], 422);
        }

        $application = BidanApplication::where('email', $request->email)
            ->latest()
            ->first();

        if (!$application) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ditemukan aplikasi dengan email tersebut',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'application_id' => $application->bidan_application_id,
                'status' => $application->status,
                'bidan_name' => $application->bidan_name,
                'submitted_at' => $application->created_at,
                'approved_at' => $application->approved_at,
                'rejected_at' => $application->rejected_at,
                'rejection_reason' => $application->rejection_reason,
            ],
        ]);
    }
}
