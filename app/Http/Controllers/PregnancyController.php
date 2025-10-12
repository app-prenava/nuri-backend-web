<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Support\AuthToken;

class PregnancyController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        [$uid] = AuthToken::assertRoleFresh($request, 'ibu_hamil');

        $v = Validator::make($request->all(), [
            'lmp_date'               => ['nullable','date'],
            'gestational_age_weeks'  => ['nullable','integer','min:0'],
            'status'                 => ['nullable','in:planned,ongoing,postpartum'],
            'multiple_gestation'     => ['nullable','boolean'],
        ]);
        if ($v->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $v->errors(),
            ], 422);
        }
        $d = $v->validated();

        $exists = DB::table('users')->where('user_id', $uid)->exists();
        if (! $exists) {
            return response()->json(['status'=>'error','message'=>'User not found.'], 404);
        }

        $existing = DB::table('pregnancies')
            ->where('user_id', $uid)
            ->select('pregnancy_id')
            ->first();

        if ($existing) {
            $update = [];
            if ($request->has('lmp_date'))              $update['lmp_date'] = $d['lmp_date'] ?? null;
            if ($request->has('gestational_age_weeks')) $update['gestational_age_weeks'] = $d['gestational_age_weeks'] ?? null;
            if ($request->has('status'))                $update['status'] = $d['status'] ?? 'ongoing';
            if ($request->has('multiple_gestation'))    $update['multiple_gestation'] = (bool) ($d['multiple_gestation'] ?? false);

            if (empty($update)) {
                return response()->json(['status'=>'error','message'=>'No fields to update.'], 400);
            }

            $update['updated_at'] = now();

            DB::table('pregnancies')
                ->where('pregnancy_id', $existing->pregnancy_id)
                ->update($update);

            return response()->json([
                'status'       => 'success',
                'message'      => 'Pregnancy updated.',
                'pregnancy_id' => (int) $existing->pregnancy_id,
            ], 200);
        }

        $payload = [
            'user_id'                => $uid,
            'lmp_date'               => $d['lmp_date'] ?? null,
            'gestational_age_weeks'  => $d['gestational_age_weeks'] ?? null,
            'status'                 => $d['status'] ?? 'ongoing',
            'multiple_gestation'     => (bool) ($d['multiple_gestation'] ?? false),
            'created_at'             => now(),
            'updated_at'             => now(),
        ];

        $pregnancyId = DB::table('pregnancies')->insertGetId($payload);

        return response()->json([
            'status'       => 'success',
            'message'      => 'Pregnancy created.',
            'pregnancy_id' => (int) $pregnancyId,
        ], 201);
    }
}
