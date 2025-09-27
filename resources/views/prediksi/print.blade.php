<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Prediksi</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #222; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; }
        .logo { font-size: 22px; font-weight: bold; letter-spacing: 1px; }
        .brand { text-align: right; }
        .brand-title { font-weight: bold; font-size: 16px; letter-spacing: 1px; }
        .brand-sub { font-size: 12px; color: #777; margin-top: 2px; }
        hr { border: 0; border-top: 2px solid #222; margin: 22px 0; }
        h2 { font-size: 23px; font-weight: bold; margin-bottom: 0px; color: #222; }
        .section-title { font-weight: bold; margin: 18px 0 6px; font-size: 16px;}
        .faktor-list { margin: 10px 0 16px 0; padding-left: 22px;}
        .faktor-list li { margin-bottom: 4px; }
        .label { display: inline-block; width: 170px; font-weight: bold; vertical-align: top;}
        .row { margin-bottom: 3px;}
        .catatan, .rekomendasi { margin-top: 18px;}
        .footer { position: fixed; left: 0; bottom: 22px; width: 100%; font-size: 11px; color: #555; text-align: center;}
        .small { font-size: 13px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h2 style="margin:0;">Hasil Prediksi</h2>
        </div>
        <div class="brand">
            <div class="brand-title">SEHATI</div>
            <div class="brand-sub">Sehat Bersama Buah Hati</div>
            <div class="small" style="margin-top:14px; color:#555;">
                {{ \Carbon\Carbon::now('Asia/Jakarta')->format('d F Y') }}
            </div>
        </div>
    </div>
    <hr>
    <div style="margin-bottom:12px;">
        <div class="row"><span class="label">Nama :</span> {{ $prediction->user->name ?? '-' }}</div>
        <div class="row"><span class="label">Nomor Telp :</span> {{ $prediction->user->nomor_telepon ?? '-' }}</div>
        <div class="row"><span class="label">Hari Perkiraan Lahir :</span>
            {{ $prediction->hpl && $prediction->hpl->hpl ? \Carbon\Carbon::parse($prediction->hpl->hpl)->format('d F Y') : '-' }}
        </div>
    </div>
    <div class="section-title">
        Hasil Prediksi Metode Persalinan :
        <span style="text-transform:capitalize">{{ $prediction->metode_persalinan }}</span>
    </div>
    <div class="row" style="margin-bottom:6px;">
        <span class="label">Faktor Penentu :</span>
        {{ $prediction->faktor ?? '-' }}
    </div>
    <div class="row" style="margin-bottom:12px;">
        <span class="label">Confidence :</span>
        {{ is_numeric($prediction->confidence) ? round($prediction->confidence) . '%' : '-' }}
    </div>
    <div style="margin-bottom:6px; font-weight:bold;">Faktor :</div>
    <ul class="faktor-list">
        <li>Usia Ibu : {{ $prediction->usia_ibu }} tahun</li>
        <li>Tekanan Darah : {{ ucfirst($prediction->tekanan_darah) }}</li>
        <li>Riwayat Persalinan : {{ ucfirst($prediction->riwayat_persalinan) }}</li>
        <li>Riwayat Kesehatan Ibu : {{ ucfirst($prediction->riwayat_kesehatan_ibu) }}</li>
        <li>Posisi Janin : {{ ucfirst($prediction->posisi_janin) }}</li>
        <li>Kondisi Kesehatan Janin : {{ ucfirst($prediction->kondisi_kesehatan_janin) }}</li>
    </ul>
    <div class="rekomendasi"><b>Rekomendasi :</b><br>
        Bunda, segera konsultasikan dengan dokter untuk persiapan persalinan {{ ucfirst($prediction->metode_persalinan) }} yang aman. Tetap tenang dan jaga kondisi tubuh dengan baik.
    </div>
    <div class="catatan">
        <b>Catatan :</b><br>
        Hasil prediksi ini hanya bersifat informatif dan tidak menggantikan konsultasi medis langsung dengan dokter.
    </div>

    <div class="footer">
        © {{ date('Y') }} SEHATI | www.sehati.id | Kontak Bantuan: 0800-123-456
    </div>
</body>
</html>
