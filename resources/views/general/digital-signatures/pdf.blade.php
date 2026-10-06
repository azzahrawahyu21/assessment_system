<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Proposal - {{ $planning->no_letter }}</title>
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
            margin: 4px 0 20px;
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
            text-decoration: underline;
            margin: 0 0 4px;
            letter-spacing: 0.5px;
        }
        .judul .nomor {
            font-size: 12pt;
            margin: 0;
        }

        /* ===== META ===== */
        .meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .meta td {
            vertical-align: top;
            padding: 2px 0;
            font-size: 12pt;
        }
        .meta .lbl { width: 145px; }
        .meta .sep { width: 14px; text-align: center; }

        /* ===== ISI ===== */
        .bab {
            font-size: 12pt;
            font-weight: bold;
            margin: 16px 0 6px;
        }
        .isi {
            text-align: justify;
            margin: 0 0 10px;
            font-size: 12pt;
        }
        .isi-indent {
            text-align: justify;
            text-indent: 30px;
            margin: 0 0 12px;
        }
        .penutup {
            text-align: justify;
            text-indent: 30px;
            margin-top: 16px;
            margin-bottom: 8px;
        }

        /* ===== CATATAN LAMPIRAN (hanya info, isi PDF digabung via FPDI) ===== */
        .lampiran-note {
            margin-top: 18px;
            margin-bottom: 8px;
            padding: 8px 12px;
            border: 1px solid #333;
            font-size: 11pt;
            text-align: justify;
        }
        .lampiran-note strong {
            display: block;
            margin-bottom: 3px;
            font-size: 12pt;
        }

        /* ===== TTD ===== */
        .ttd-wrap {
            margin-top: 36px;
            width: 100%;
        }
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
        }
        .ttd-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }
        .ttd-jabatan {
            font-size: 12pt;
            margin-bottom: 6px;
        }
        .ttd-qr {
            margin: 6px auto 4px;
            width: 95px;
            height: 95px;
        }
        .ttd-qr img {
            width: 95px;
            height: 95px;
        }
        .ttd-nama {
            margin-top: 4px;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
        }
        .ttd-label {
            font-size: 10.5pt;
            margin-top: 2px;
        }

        .footer-note {
            margin-top: 28px;
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
    <h1>Proposal Perencanaan Kegiatan</h1>
    <p class="nomor">Nomor: {{ $planning->no_letter }}</p>
</div>

{{-- ===================== DATA SURAT ===================== --}}
<table class="meta">
    <tr>
        <td class="lbl">Perihal</td>
        <td class="sep">:</td>
        <td>Proposal Perencanaan Kegiatan {{ $planning->name }}</td>
    </tr>
    <tr>
        <td class="lbl">Tanggal</td>
        <td class="sep">:</td>
        <td>{{ $planning->date?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}</td>
    </tr>
    <tr>
        <td class="lbl">Unit Kerja</td>
        <td class="sep">:</td>
        <td>{{ $planning->department->name ?? '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">Pengaju</td>
        <td class="sep">:</td>
        <td>{{ $planning->creator->name ?? '—' }}</td>
    </tr>
</table>

<p class="isi-indent">
    Dengan hormat, bersama ini kami sampaikan proposal perencanaan kegiatan sebagai berikut.
</p>

{{-- ===================== ISI ===================== --}}
<p class="bab">A. Identitas Kegiatan</p>
<table class="meta">
    <tr>
        <td class="lbl">Nama Kegiatan</td>
        <td class="sep">:</td>
        <td>{{ $planning->name }}</td>
    </tr>
    <tr>
        <td class="lbl">Jenis Perencanaan</td>
        <td class="sep">:</td>
        <td>{{ $planning->planningType->name ?? '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">Periode</td>
        <td class="sep">:</td>
        <td>
            @if($planning->period === 'ganjil')
                Semester Ganjil
            @elseif($planning->period === 'genap')
                Semester Genap
            @else
                {{ $planning->period ?? '—' }}
            @endif
        </td>
    </tr>
    <tr>
        <td class="lbl">Estimasi Anggaran</td>
        <td class="sep">:</td>
        <td>
            Rp {{ number_format($planning->budget ?? 0, 0, ',', '.') }}
            @if($planning->funding_source)
                &nbsp;(Sumber: {{ $planning->funding_source }})
            @endif
        </td>
    </tr>
</table>

<p class="bab">B. Tujuan</p>
<p class="isi">{{ $planning->objective ?: 'Tujuan kegiatan belum diisi.' }}</p>

<p class="bab">C. Sasaran</p>
<p class="isi">{{ $planning->target ?: 'Sasaran kegiatan belum diisi.' }}</p>

<p class="bab">D. Indikator Keberhasilan</p>
<p class="isi">{{ $planning->success_indicator ?: 'Indikator keberhasilan belum diisi.' }}</p>

@if($planning->budget_note)
    <p class="bab">E. Keterangan Anggaran</p>
    <p class="isi">{{ $planning->budget_note }}</p>
@endif

<p class="penutup">
    Demikian proposal perencanaan kegiatan ini disusun untuk digunakan sebagaimana mestinya.
    Atas perhatian dan kerjasamanya, diucapkan terima kasih.
</p>

{{-- ===================== CATATAN LAMPIRAN ===================== --}}
@if($planning->document_path)
    <div class="lampiran-note">
        <strong>Lampiran</strong>
        Dokumen pendukung dari proposal ini terlampir pada halaman berikutnya.
    </div>
@endif

{{-- ===================== TTD + QR ===================== --}}
<div class="ttd-wrap">
    <table class="ttd-table">
        <tr>
            <td>
                <div class="ttd-jabatan">Wakil Direktur Bidang Akademik</div>
                <div class="ttd-qr">
                    @if(!empty($qrBase64))
                        <img src="data:image/svg+xml;base64,{{ $qrBase64 }}" alt="QR Wadir" style="width:95px;height:95px;">
                    @endif
                </div>
                <div class="ttd-nama">{{ $wadirName ?? $signature->wadir_name ?? '—' }}</div>
                <div class="ttd-label">Wakil Direktur 1</div>
            </td>
            <td>
                <div class="ttd-jabatan">Direktur</div>
                <div class="ttd-qr">
                    @if(!empty($qrBase64))
                        <img src="data:image/svg+xml;base64,{{ $qrBase64 }}" alt="QR Direktur" style="width:95px;height:95px;">
                    @endif
                </div>
                <div class="ttd-nama">{{ $directorName ?? $signature->director_name ?? '—' }}</div>
                <div class="ttd-label">Direktur</div>
            </td>
        </tr>
    </table>
</div>

<div class="footer-note">
    Dokumen ini ditandatangani secara elektronik. Scan QR Code untuk verifikasi keaslian dokumen.<br>
    Nomor: {{ $planning->no_letter }}
    @if(!empty($signature->document_number))
        &nbsp;|&nbsp; No. TTE: {{ $signature->document_number }}
    @endif
    @if(!empty($signature->hash))
        &nbsp;|&nbsp; Hash: {{ substr($signature->hash, 0, 16) }}…
    @endif
</div>

</body>
</html>