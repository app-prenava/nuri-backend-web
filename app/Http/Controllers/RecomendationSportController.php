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
    public function getRecomendation(Request $request): JsonResponse
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

        $ml = $resp->json();

        $fallback = 'data not found';

        $activities = collect($ml['recommendations'] ?? [])
            ->pluck('activity')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $metaByActivity = DB::table('data_ml_sport')
            ->select([
                'activity',
                'video_link',
                'long_text',
                'picture_1',
                'picture_2',
                'picture_3',
            ])
            ->whereIn('activity', $activities)
            ->get()
            ->keyBy('activity');

        $enrich = function(array $item) use ($metaByActivity, $fallback) {
            $act  = $item['activity'] ?? null;
            $meta = $act ? ($metaByActivity[$act] ?? null) : null;

            return array_merge($item, [
                'video_link' => $meta->video_link ?? $fallback,
                'long_text'  => $meta->long_text  ?? $fallback,
                'picture_1'  => $meta->picture_1  ?? $fallback,
                'picture_2'  => $meta->picture_2  ?? $fallback,
                'picture_3'  => $meta->picture_3  ?? $fallback,
            ]);
        };

        $ml['recommendations'] = array_map($enrich, $ml['recommendations'] ?? []);

        if (!empty($ml['all_ranked']) && is_array($ml['all_ranked'])) {
            $ml['all_ranked'] = array_map($enrich, $ml['all_ranked']);
        }

        return response()->json([
            'status'          => 'success',
            'message'         => 'Sport recommendation success.',
            'forward_payload' => $forward,
            'model_response'  => $ml,
        ], 201);
    }

    public function indexSportMeta(Request $request): JsonResponse
    {
        [$uid] = AuthToken::assertRoleFresh($request, 'admin');

        $q = trim((string) $request->query('q', ''));
        $perPage = (int) $request->query('per_page', 50);
        $perPage = max(1, min($perPage, 200));

        $query = DB::table('data_ml_sport')
            ->select([
                'activity',
                'video_link',
                'long_text',
                'picture_1',
                'picture_2',
                'picture_3',
                'created_at',
                'updated_at',
            ])
            ->orderBy('activity');

        if ($q !== '') {
            $query->where('activity', 'like', '%' . $q . '%');
        }

        $items = $query->limit($perPage)->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'List sport metadata.',
            'data'    => $items,
        ], 200);
    }

    public function showSportMeta(Request $request, string $activity): JsonResponse
    {
        [$uid] = AuthToken::assertRoleFresh($request, 'admin');

        $row = DB::table('data_ml_sport')
            ->select([
                'activity',
                'video_link',
                'long_text',
                'picture_1',
                'picture_2',
                'picture_3',
                'created_at',
                'updated_at',
            ])
            ->where('activity', $activity)
            ->first();

        if (! $row) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data not found.',
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Sport metadata detail.',
            'data'    => $row,
        ], 200);
    }

    public function storeSportMeta(Request $request): JsonResponse
    {
        [$uid] = AuthToken::assertRoleFresh($request, 'admin');

        $v = Validator::make($request->all(), [
            'activity'   => ['required','string','max:100'],
            'video_link' => ['nullable','string','max:2048'],
            'long_text'  => ['nullable','string'],

            'picture_1'  => ['nullable','file','image','max:2048'],
            'picture_2'  => ['nullable','file','image','max:2048'],
            'picture_3'  => ['nullable','file','image','max:2048'],
        ]);

        if ($v->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $v->errors(),
            ], 422);
        }

        $d = $v->validated();

        $exists = DB::table('data_ml_sport')->where('activity', $d['activity'])->exists();
        if ($exists) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Activity already exists.',
            ], 409);
        }

        $folder = 'ml_sport';
        $picture1Url = null;
        $picture2Url = null;
        $picture3Url = null;

        if ($request->hasFile('picture_1')) {
            $path = $request->file('picture_1')->store($folder, 'supabase');
            $picture1Url = \Illuminate\Support\Facades\Storage::disk('supabase')->url($path);
        }
        if ($request->hasFile('picture_2')) {
            $path = $request->file('picture_2')->store($folder, 'supabase');
            $picture2Url = \Illuminate\Support\Facades\Storage::disk('supabase')->url($path);
        }
        if ($request->hasFile('picture_3')) {
            $path = $request->file('picture_3')->store($folder, 'supabase');
            $picture3Url = \Illuminate\Support\Facades\Storage::disk('supabase')->url($path);
        }

        $now = now();

        DB::table('data_ml_sport')->insert([
            'activity'   => $d['activity'],
            'video_link' => $d['video_link'] ?? null,
            'long_text'  => $d['long_text'] ?? null,
            'picture_1'  => $picture1Url,
            'picture_2'  => $picture2Url,
            'picture_3'  => $picture3Url,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $row = DB::table('data_ml_sport')
            ->where('activity', $d['activity'])
            ->first();

        return response()->json([
            'status'  => 'success',
            'message' => 'Sport metadata created.',
            'data'    => $row,
        ], 201);
    }


    public function updateSportMeta(Request $request, string $activity): JsonResponse
    {
        [$uid] = AuthToken::assertRoleFresh($request, 'admin');

        $v = Validator::make($request->all(), [
            'video_link' => ['nullable','string','max:2048'],
            'long_text'  => ['nullable','string'],

            'picture_1'  => ['nullable','file','image','max:5120'],
            'picture_2'  => ['nullable','file','image','max:5120'],
            'picture_3'  => ['nullable','file','image','max:5120'],

            'remove_picture_1' => ['nullable','boolean'],
            'remove_picture_2' => ['nullable','boolean'],
            'remove_picture_3' => ['nullable','boolean'],
        ]);

        if ($v->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $v->errors(),
            ], 422);
        }

        $exists = DB::table('data_ml_sport')->where('activity', $activity)->exists();
        if (! $exists) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data not found.',
            ], 404);
        }

        $d = $v->validated();

        $folder = 'ml_sport';

        $old = DB::table('data_ml_sport')->where('activity', $activity)->first();

        $payload = ['updated_at' => now()];

        if ($request->has('video_link')) {
            $payload['video_link'] = $d['video_link'] ?? null;
        }
        if ($request->has('long_text')) {
            $payload['long_text'] = $d['long_text'] ?? null;
        }

        if ($request->hasFile('picture_1')) {
            $path = $request->file('picture_1')->store($folder, 'supabase');
            $payload['picture_1'] = \Illuminate\Support\Facades\Storage::disk('supabase')->url($path);
        } elseif (!empty($d['remove_picture_1'])) {
            $payload['picture_1'] = null;
        }

        if ($request->hasFile('picture_2')) {
            $path = $request->file('picture_2')->store($folder, 'supabase');
            $payload['picture_2'] = \Illuminate\Support\Facades\Storage::disk('supabase')->url($path);
        } elseif (!empty($d['remove_picture_2'])) {
            $payload['picture_2'] = null;
        }

        if ($request->hasFile('picture_3')) {
            $path = $request->file('picture_3')->store($folder, 'supabase');
            $payload['picture_3'] = \Illuminate\Support\Facades\Storage::disk('supabase')->url($path);
        } elseif (!empty($d['remove_picture_3'])) {
            $payload['picture_3'] = null;
        }

        DB::table('data_ml_sport')->where('activity', $activity)->update($payload);

        $row = DB::table('data_ml_sport')->where('activity', $activity)->first();

        return response()->json([
            'status'  => 'success',
            'message' => 'Sport metadata updated.',
            'data'    => $row,
        ], 200);
    }


    public function deleteSportMeta(Request $request, string $activity): JsonResponse
    {
        [$uid] = AuthToken::assertRoleFresh($request, 'admin');

        $deleted = DB::table('data_ml_sport')
            ->where('activity', $activity)
            ->delete();

        if ($deleted < 1) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data not found.',
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Sport metadata deleted.',
            'activity'=> $activity,
        ], 200);
    }
}
