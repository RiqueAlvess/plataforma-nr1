<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CsvImportRequest;
use App\Models\CsvImport;
use App\Services\CsvImportService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CsvImportController extends Controller
{
    public function __construct(
        private readonly CsvImportService $csvImportService
    ) {}

    public function index(): Response
    {
        $imports = $this->csvImportService->paginate(15);

        return Inertia::render('Importacao/Index', [
            'imports' => $imports,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Importacao/Create');
    }

    public function store(CsvImportRequest $request): RedirectResponse
    {
        $import = $this->csvImportService->processarComCabecalho(
            $request->file('arquivo'),
            $request->user()
        );

        if ($import->hasErro()) {
            return redirect()->route('tenant.importacao.show', $import)
                ->with('error', 'Erro ao processar o arquivo: ' . $import->mensagem_erro);
        }

        return redirect()->route('tenant.importacao.show', $import)
            ->with('success', "Importação concluída: {$import->registros_importados} registros importados.");
    }

    public function show(CsvImport $import): Response
    {
        $import->load(['user', 'records.unidade', 'records.setor']);

        return Inertia::render('Importacao/Show', [
            'import' => $import,
        ]);
    }

    public function destroy(CsvImport $import): RedirectResponse
    {
        $import->records()->delete();
        $import->delete();

        return redirect()->route('tenant.importacao.index')
            ->with('success', 'Importação excluída com sucesso.');
    }
}
