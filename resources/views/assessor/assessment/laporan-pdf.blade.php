<!DOCTYPE html>
<html lang="id">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Laporan Assessment COBIT 2019</title>
  <style>
    @page {
      margin: 2cm 2cm 2cm 2.5cm;
      size: A4 portrait;
    }

    body {
      font-family: "Times-Roman", "Times New Roman", Times, serif;
      font-size: 12pt;
      line-height: 1.5;
      color: #000;
    }

    /* ===== KOP ===== */
    .kop-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 6px;
    }
    .kop-logo {
      width: 85px;
      vertical-align: middle;
    }
    .kop-logo img {
      width: 80px;
      height: auto;
    }
    .kop-text {
      text-align: center;
      vertical-align: middle;
      padding-left: 10px;
    }
    .kop-text .kementerian {
      font-size: 12pt;
      font-weight: bold;
      text-transform: uppercase;
      margin: 0;
      letter-spacing: 0.3px;
    }
    .kop-text .instansi {
      font-size: 15pt;
      font-weight: bold;
      text-transform: uppercase;
      margin: 2px 0 4px;
    }
    .kop-text .alamat {
      font-size: 9pt;
      margin: 0;
      line-height: 1.35;
    }
    .kop-line {
      border-top: 1.5px solid #000;
      border-bottom: 4px solid #000;
      margin: 4px 0 18px;
      height: 0;
    }

    /* ===== JUDUL ===== */
    .judul {
      text-align: center;
      margin-bottom: 18px;
    }
    .judul h1 {
      font-size: 14pt;
      font-weight: bold;
      text-transform: uppercase;
      /* text-decoration: underline; */
      margin: 0 0 4px;
      letter-spacing: 0.5px;
    }
    .judul .sub {
      font-size: 12pt;
      margin: 0;
    }

    /* ===== INFO ===== */
    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 8px;
    }
    .info-table td {
      vertical-align: top;
      padding: 2px 0;
      font-size: 12pt;
    }
    .info-table .lbl { width: 180px; }
    .info-table .sep { width: 14px; text-align: center; }

    .section-title {
      font-size: 12pt;
      font-weight: bold;
      margin: 16px 0 8px;
    }

    /* ===== TABEL ===== */
    table.data {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      font-size: 10.5pt;
    }
    table.data th,
    table.data td {
      border: 1px solid #333;
      padding: 5px 7px;
      vertical-align: top;
    }
    table.data th {
      background: #f1f5f9;
      font-weight: bold;
      text-align: center;
      font-size: 10pt;
    }
    .text-center { text-align: center; }

    .footer-note {
      margin-top: 24px;
      font-size: 9pt;
      border-top: 1px solid #666;
      padding-top: 6px;
      color: #333;
      line-height: 1.4;
    }
  </style>
</head>
<body>

{{-- ===================== KOP SURAT ===================== --}}
<table class="kop-table">
  <tr>
    <td class="kop-logo">
      <img src="{{ public_path('images/logo-pnm.png') }}" alt="Logo PNM">
    </td>
    <td class="kop-text">
      <p class="kementerian">Kementerian Pendidikan Tinggi, Sains, dan Teknologi</p>
      <p class="instansi">Politeknik Negeri Madiun</p>
      <p class="alamat">
        Jl. Serayu No. 84, Kelurahan Pandean, Kecamatan Taman, Kota Madiun, Jawa Timur 63133<br>
        Telepon: (0351) 452970 &nbsp;|&nbsp; Email: sekretariat@pnm.ac.id<br>
        Laman: https://pnm.ac.id/
      </p>
    </td>
  </tr>
</table>
<div class="kop-line"></div>

{{-- ===================== JUDUL ===================== --}}
<div class="judul">
  <h1>Laporan Assessment COBIT 2019</h1>
  {{-- <p class="sub">{{ $assessment->name ?? '-' }}</p> --}}
</div>

{{-- ===================== INFORMASI UMUM ===================== --}}
<table class="info-table">
  <tr>
    <td class="lbl">Institusi</td>
    <td class="sep">:</td>
    <td>{{ $institusi }}</td>
  </tr>
  <tr>
    <td class="lbl">Tahun Assessment</td>
    <td class="sep">:</td>
    <td>{{ $year }}</td>
  </tr>
  <tr>
    <td class="lbl">Nama Assessment</td>
    <td class="sep">:</td>
    <td>{{ $assessment->name ?? '-' }}</td>
  </tr>
  <tr>
    <td class="lbl">Unit / Program Studi</td>
    <td class="sep">:</td>
    <td>
      @if($assessment->is_all_department ?? false)
        Seluruh Unit (Kampus)
      @else
        {{ $assessment->department->name ?? '—' }}
      @endif
    </td>
  </tr>
</table>

{{-- ===================== RINGKASAN CAPABILITY ===================== --}}
<p class="section-title">A. Ringkasan Capability</p>
<table class="info-table">
  <tr>
    <td class="lbl">Current Capability</td>
    <td class="sep">:</td>
    <td>{{ number_format($currentCapability, 2) }}</td>
  </tr>
  <tr>
    <td class="lbl">Target Capability</td>
    <td class="sep">:</td>
    <td>{{ number_format($targetCapability, 2) }}</td>
  </tr>
  <tr>
    <td class="lbl">Average Gap</td>
    <td class="sep">:</td>
    <td>{{ number_format($averageGap, 2) }}</td>
  </tr>
</table>

{{-- ===================== RINGKASAN PRIORITAS ===================== --}}
<p class="section-title">B. Ringkasan Prioritas</p>
<table class="info-table">
  <tr>
    <td class="lbl">Objectives Assessed</td>
    <td class="sep">:</td>
    <td>{{ $objectivesAssessed }}</td>
  </tr>
  <tr>
    <td class="lbl">Very High Priority</td>
    <td class="sep">:</td>
    <td>{{ $veryHighPriority }}</td>
  </tr>
  <tr>
    <td class="lbl">High Priority</td>
    <td class="sep">:</td>
    <td>{{ $highPriority }}</td>
  </tr>
  <tr>
    <td class="lbl">Medium Priority</td>
    <td class="sep">:</td>
    <td>{{ $mediumPriority }}</td>
  </tr>
</table>

{{-- ===================== TABEL DETAIL ===================== --}}
<p class="section-title">C. Detail Hasil Setiap Domain</p>
<table class="data">
  <thead>
    <tr>
      <th width="12%" class="text-center">Kode</th>
      <th class="text-center">Nama Domain</th>
      <th width="10%" class="text-center">Rec</th>
      <th width="10%" class="text-center">Ach</th>
      <th width="10%" class="text-center">Gap</th>
      <th width="14%" class="text-center">Priority</th>
    </tr>
  </thead>
  <tbody>
    @forelse($items as $item)
      <tr>
        <td class="text-center">{{ $item->code }}</td>
        <td>{{ $item->name }}</td>
        <td class="text-center">{{ $item->recommended ?? '-' }}</td>
        <td class="text-center">{{ $item->achieved ?? '-' }}</td>
        <td class="text-center">{{ $item->gap }}</td>
        <td class="text-center">{{ $item->priority_label }}</td>
      </tr>
    @empty
      <tr>
        <td colspan="6" class="text-center">Belum ada data domain.</td>
      </tr>
    @endforelse
  </tbody>
</table>

<div class="footer-note">
  Dokumen ini digenerate secara otomatis oleh sistem DIGI-Campus · Politeknik Negeri Madiun.<br>
  Tanggal cetak: {{ now()->translatedFormat('d F Y H:i') }}
</div>

</body>
</html>