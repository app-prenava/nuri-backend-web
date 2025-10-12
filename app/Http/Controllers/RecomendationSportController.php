<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Support\AuthToken;


class RecomendationSportController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        [$uid] = AuthToken::assertRoleFresh($request, 'ibu_hamil');

        $v = Validator::make($request->all(), [
            'age'                       => ['required','integer','min:10','max:70'],
            'gestational_age_weeks'     => ['required','numeric','min:0','max:45'],
            'bmi'                       => ['required','numeric','min:10','max:60'],

            'hypertension'              => ['required','boolean'],
            'is_diabetes'               => ['required','boolean'],
            'gestational_diabetes'      => ['required','boolean'],
            'is_fever'                  => ['required','boolean'],
            'is_high_heart_rate'        => ['required','boolean'],
            'previous_complications'    => ['required','boolean'],
            'mental_health_issue'       => ['required','boolean'],

            'low_impact_pref'           => ['required','boolean'],
            'water_access'              => ['required','boolean'],
            'back_pain'                 => ['required','boolean'],

            'placenta_position_restriction' => ['nullable','boolean'],
        ]);
        if ($v->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $v->errors(),
            ], 422);
        }
        $d = $v->validated();

        $preg = DB::table('pregnancies')
            ->select('pregnancy_id')
            ->where('user_id', $uid)
            ->where('status', 'ongoing')
            ->orderByDesc('pregnancy_id')
            ->first();

        if (! $preg) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No ongoing pregnancy found for this user.',
            ], 404);
        }

        $forward = [
            'age'                        => (int) $d['age'],
            'gestational_age_weeks'      => (float) $d['gestational_age_weeks'],
            'bmi'                        => (float) $d['bmi'],
            'blood_pressure_systolic'    => $d['hypertension']       ? 150 : 90,
            'blood_pressure_diastolic'   => $d['hypertension']       ? 100 : 80,
            'blood_sugar'                => $d['is_diabetes']        ? 200 : 80,
            'body_temp'                  => $d['is_fever']           ? 40.0 : 36.5,
            'heart_rate'                 => $d['is_high_heart_rate'] ? 120  : 80,
            'previous_complications'     => (bool) $d['previous_complications'],
            'preexisting_diabetes'       => (bool) $d['is_diabetes'],
            'gestational_diabetes'       => (bool) $d['gestational_diabetes'],
            'mental_health_issue'        => (bool) $d['mental_health_issue'],
            'placenta_position_restriction' => (bool) ($d['placenta_position_restriction'] ?? false),
            'low_impact_pref'            => (bool) $d['low_impact_pref'],
            'water_access'               => (bool) $d['water_access'],
            'back_pain'                  => (bool) $d['back_pain'],
        ];

        $mlUrl = rtrim(env('URL_ML_SPORTS'), '/') . '/predict';

        $resp = Http::withOptions(['timeout' => 3])
            ->retry(2, 100)
            ->acceptJson()
            ->post($mlUrl, $forward);

        if (! $resp->ok()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Upstream prediction service error.',
                'upstream_status' => $resp->status(),
                'upstream_body'   => $resp->json() ?? $resp->body(),
            ], 502);
        }

        $assessmentId = DB::table('pregnancy_assessments')->insertGetId([
            'pregnancy_id'           => $preg->pregnancy_id,
            'bmi'                    => $d['bmi'],
            'hypertension'           => $d['hypertension'],
            'is_diabetes'            => $d['is_diabetes'],
            'gestational_diabetes'   => $d['gestational_diabetes'],
            'is_fever'               => $d['is_fever'],
            'is_high_heart_rate'     => $d['is_high_heart_rate'],
            'previous_complications' => $d['previous_complications'],
            'mental_health_issue'    => $d['mental_health_issue'],
            'back_pain'              => $d['back_pain'],
            'low_impact_pref'        => $d['low_impact_pref'],
            'water_access'           => $d['water_access'],
            'placenta_previa'        => (bool) ($d['placenta_position_restriction'] ?? false),
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);

        return response()->json([
            'status'          => 'success',
            'message'         => 'Sport recommendation created.',
            'assessment_id'   => $assessmentId,
            'forward_payload' => $forward,
            'model_response'  => $resp->json(),
        ], 201);
    }

}
