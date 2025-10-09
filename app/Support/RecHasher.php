<?php

namespace App\Support;

final class RecHasher
{
    private const USE_BUCKET = false;

    public static function normalize(array $f): array
    {
        foreach ([
            'hypertension','gestational_diabetes','placenta_previa',
            'pre_eclampsia','low_impact_pref','water_access','back_pain'
        ] as $k) {
            if (array_key_exists($k, $f)) {
                $f[$k] = (bool) $f[$k];
            }
        }

        if (isset($f['bmi'])) {
            $f['bmi'] = round((float) $f['bmi'], 1);
        }

        if (isset($f['gestational_age_weeks'])) {
            $w = (int) $f['gestational_age_weeks'];
            $f['gestational_age_weeks'] = $w;
            $f['trimester'] = $w <= 13 ? 1 : ($w <= 27 ? 2 : 3);

            if (self::USE_BUCKET) {
                $f['ga_bucket'] = intdiv($w, 2) * 2; 
            }
        }

        ksort($f);
        return $f;
    }

    public static function hash(array $features): string
    {
        $norm = self::normalize($features);
        $json = json_encode($norm, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION);
        return hash('sha256', $json);
    }
}
