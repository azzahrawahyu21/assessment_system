<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Tanda Tangan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 640px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                @if($signature->status === 'signed')
                    <div class="text-success mb-2" style="font-size:2.5rem">✓</div>
                    <h4 class="fw-bold text-success">Dokumen Valid</h4>
                @else
                    <div class="text-danger mb-2" style="font-size:2.5rem">✗</div>
                    <h4 class="fw-bold text-danger">Dokumen Dicabut</h4>
                @endif
                <p class="text-muted small mb-0">Verifikasi tanda tangan digital Politeknik Negeri Madiun</p>
            </div>

            <table class="table table-sm">
                <tr>
                    <th width="40%">No. Dokumen</th>
                    <td><code>{{ $signature->document_number }}</code></td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td>{{ $signature->document_date?->format('d F Y') }}</td>
                </tr>
                <tr>
                    <th>Perencanaan</th>
                    <td>{{ $signature->planning->name ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Unit Kerja</th>
                    <td>{{ $signature->planning->department->name ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Direktur</th>
                    <td>{{ $signature->director_name ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Wakil Direktur</th>
                    <td>{{ $signature->wadir_name ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge {{ $signature->status === 'signed' ? 'bg-success' : 'bg-danger' }}">
                            {{ strtoupper($signature->status) }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
</body>
</html>