<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Cobit;
use App\Models\CobitStatement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CobitDomainController extends Controller
{
    /**
     * Daftar domain COBIT
     */
    public function index(Request $request)
    {
        $domains = Cobit::withCount('statements')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                      ->orWhere('domain', 'like', "%{$search}%");
                });
            })
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();
            // ->get();

        // Samakan atribut yang dipakai view (tanpa ubah DB)
        $domains->transform(function ($d) {
            $d->name      = $d->domain;   // view pakai name
            $d->is_active = true;        // kolom tidak ada → anggap selalu aktif
            return $d;
        });

        $stats = [
            'total'      => $domains->count(),
            'active'     => $domains->count(), // semua dianggap aktif
            'statements' => $domains->sum('statements_count'),
        ];

        return view('administrator.cobit-domain.index', compact('domains', 'stats'));
    }

    public function create()
    {
        return view('administrator.cobit-domain.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'        => 'required|string|max:10|unique:cobits,code',
            'name'        => 'required|string|max:255', // dari form → disimpan ke kolom domain
            'description' => 'nullable|string',

            'levels'                      => 'nullable|array',
            'levels.*.statements'         => 'nullable|array',
            'levels.*.statements.*.text'  => 'nullable|string',
            'levels.*.statements.*.level' => 'nullable|integer|min:0|max:5',
        ]);

        DB::transaction(function () use ($validated) {
            $domain = Cobit::create([
                'code'        => strtoupper(trim($validated['code'])),
                'domain'      => $validated['name'], // form "name" → kolom "domain"
                'description' => $validated['description'] ?? null,
            ]);

            if (!empty($validated['levels'])) {
                foreach ($validated['levels'] as $levelNum => $levelData) {
                    if (empty($levelData['statements'])) {
                        continue;
                    }

                    foreach ($levelData['statements'] as $statement) {
                        $text = trim($statement['text'] ?? '');
                        if ($text === '') {
                            continue;
                        }

                        CobitStatement::create([
                            'cobit_id'  => $domain->id_cobit, // PK Cobit
                            'level'     => (int) ($statement['level'] ?? $levelNum),
                            'statement' => $text,             // form "text" → kolom "statement"
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route('administrator.cobit-domains.index') // sesuaikan nama route Anda
            ->with('success', 'Domain COBIT berhasil ditambahkan.');
    }

    public function show(Cobit $cobitDomain)
    {
        $cobitDomain->load([
            'statements' => fn ($q) => $q->orderBy('level')->orderBy('id_statement'),
        ]);

        // Samakan untuk view
        $cobitDomain->name = $cobitDomain->domain;

        return view('administrator.cobit-domain.show', compact('cobitDomain'));
    }

    public function edit(Cobit $cobitDomain)
    {
        $cobitDomain->load([
            'statements' => fn ($q) => $q->orderBy('level')->orderBy('id_statement'),
        ]);

        $cobitDomain->name = $cobitDomain->domain;

        return view('administrator.cobit-domain.edit', compact('cobitDomain'));
    }

    public function update(Request $request, Cobit $cobitDomain)
    {
        $validated = $request->validate([
            'code'        => 'required|string|max:10|unique:cobits,code,' . $cobitDomain->id_cobit . ',id_cobit',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',

            'levels'                              => 'nullable|array',
            'levels.*.statements'                 => 'nullable|array',
            'levels.*.statements.*.id'            => 'nullable|integer',
            'levels.*.statements.*.text'          => 'nullable|string',
            'levels.*.statements.*.level'         => 'nullable|integer|min:0|max:5',
        ]);

        DB::transaction(function () use ($validated, $cobitDomain) {

            $cobitDomain->update([
                'code'        => strtoupper(trim($validated['code'])),
                'domain'      => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            // Kumpulkan ID pernyataan yang masih dikirim
            $keptIds = [];
            if (!empty($validated['levels'])) {
                foreach ($validated['levels'] as $levelData) {
                    if (empty($levelData['statements'])) {
                        continue;
                    }
                    foreach ($levelData['statements'] as $statement) {
                        if (!empty($statement['id'])) {
                            $keptIds[] = (int) $statement['id'];
                        }
                    }
                }
            }

            // Hapus yang tidak ada di request
            if (empty($keptIds)) {
                $cobitDomain->statements()->delete();
            } else {
                $cobitDomain->statements()
                    ->whereNotIn('id_statement', $keptIds)
                    ->delete();
            }

            // Update / create
            if (!empty($validated['levels'])) {
                foreach ($validated['levels'] as $levelNum => $levelData) {
                    if (empty($levelData['statements'])) {
                        continue;
                    }

                    foreach ($levelData['statements'] as $statement) {
                        $text = trim($statement['text'] ?? '');
                        if ($text === '') {
                            continue;
                        }

                        $payload = [
                            'cobit_id'  => $cobitDomain->id_cobit,
                            'level'     => (int) ($statement['level'] ?? $levelNum),
                            'statement' => $text,
                        ];

                        if (!empty($statement['id'])) {
                            CobitStatement::where('id_statement', $statement['id'])
                                ->where('cobit_id', $cobitDomain->id_cobit)
                                ->update($payload);
                        } else {
                            CobitStatement::create($payload);
                        }
                    }
                }
            }
        });

        return redirect()
            ->route('administrator.cobit-domains.index')
            ->with('success', 'Domain COBIT berhasil diperbarui.');
    }

    public function destroy(Cobit $cobitDomain)
    {
        // Cascade sudah di migration → statements ikut terhapus
        $cobitDomain->delete();

        return redirect()
            ->route('administrator.cobit-domains.index')
            ->with('success', 'Domain COBIT berhasil dihapus.');
    }
}