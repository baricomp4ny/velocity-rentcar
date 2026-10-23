<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * CRUD bersama untuk data master sederhana (nama, urutan, aktif):
 * kategori mobil, lokasi layanan, dan metode pembayaran.
 */
abstract class MasterDataController extends Controller
{
    /** @return class-string<Model> */
    abstract protected function model(): string;

    /**
     * Teks dan konfigurasi halaman: title, subtitle, label, route, usage => [relasi, label].
     */
    abstract protected function meta(): array;

    /**
     * Kolom tambahan selain nama/urutan/aktif: [nama => [label, opsi select]].
     */
    protected function extraFields(): array
    {
        return [];
    }

    public function index()
    {
        [$relation] = $this->meta()['usage'];

        return view('admin.master.index', [
            ...$this->viewData(),
            'items' => $this->model()::withCount($relation)->ordered()->get(),
        ]);
    }

    public function create()
    {
        $item = new ($this->model())(['is_active' => true, 'sort_order' => $this->model()::max('sort_order') + 1]);

        return view('admin.master.form', [...$this->viewData(), 'item' => $item]);
    }

    public function store(Request $request)
    {
        $item = $this->model()::create($this->validated($request));

        return $this->backToIndex("{$this->meta()['label']} {$item->name} berhasil ditambahkan.");
    }

    public function edit(string $id)
    {
        return view('admin.master.form', [...$this->viewData(), 'item' => $this->model()::findOrFail($id)]);
    }

    public function update(Request $request, string $id)
    {
        $item = $this->model()::findOrFail($id);
        $item->update($this->validated($request, $item));

        return $this->backToIndex("{$this->meta()['label']} {$item->name} berhasil diperbarui.");
    }

    public function destroy(string $id)
    {
        $item = $this->model()::findOrFail($id);

        if ($item->isUsed()) {
            return back()->with('error', "{$this->meta()['label']} {$item->name} masih dipakai. Nonaktifkan saja agar tidak muncul lagi.");
        }

        $item->delete();

        return back()->with('success', "{$this->meta()['label']} {$item->name} berhasil dihapus.");
    }

    private function validated(Request $request, ?Model $item = null): array
    {
        $table = (new ($this->model()))->getTable();

        $extraRules = collect($this->extraFields())
            ->map(fn ($field) => ['required', Rule::in(array_keys($field[1]))])
            ->all();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique($table)->ignore($item)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            ...$extraRules,
        ], [], ['name' => 'Nama', 'sort_order' => 'Urutan']);

        return [...$data, 'sort_order' => (int) ($data['sort_order'] ?? 0), 'is_active' => $request->boolean('is_active')];
    }

    private function viewData(): array
    {
        return ['meta' => $this->meta(), 'extraFields' => $this->extraFields()];
    }

    private function backToIndex(string $message)
    {
        return redirect()->route("admin.{$this->meta()['route']}.index")->with('success', $message);
    }
}
