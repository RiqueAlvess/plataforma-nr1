<?php

namespace App\Services;

use App\Models\CsvImport;
use App\Models\CsvRecord;
use App\Models\Setor;
use App\Models\Unidade;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

class CsvImportService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return CsvImport::with('user')
            ->latest()
            ->paginate($perPage);
    }

    public function processarComCabecalho(UploadedFile $arquivo, User $usuario): CsvImport
    {
        $import = CsvImport::create([
            'user_id' => $usuario->id,
            'nome_arquivo' => $arquivo->getClientOriginalName(),
            'status' => 'processando',
        ]);

        try {
            $linhas = $this->lerCsv($arquivo);
            $this->validarEstrutura($linhas);

            $cabecalho = array_map('strtoupper', array_map('trim', $linhas[0]));
            $registros = array_slice($linhas, 1);

            $totalRegistros = count($registros);
            $importados = 0;
            $erros = 0;

            foreach ($registros as $indice => $linha) {
                if (count(array_filter($linha)) === 0) {
                    $totalRegistros--;
                    continue;
                }

                try {
                    $dados = array_combine($cabecalho, array_pad($linha, count($cabecalho), ''));
                    $this->processarDados($dados, $import, $indice + 2);
                    $importados++;
                } catch (\Exception $e) {
                    $erros++;
                }
            }

            $import->update([
                'status' => 'concluido',
                'total_registros' => $totalRegistros,
                'registros_importados' => $importados,
                'registros_com_erro' => $erros,
            ]);
        } catch (\Exception $e) {
            $import->update([
                'status' => 'erro',
                'mensagem_erro' => $e->getMessage(),
            ]);
        }

        return $import->fresh(['user', 'records']);
    }

    private function lerCsv(UploadedFile $arquivo): array
    {
        $linhas = [];
        $handle = fopen($arquivo->getRealPath(), 'r');

        if ($handle === false) {
            throw new \RuntimeException('Não foi possível abrir o arquivo CSV.');
        }

        while (($linha = fgetcsv($handle, 1000, ',')) !== false) {
            $linhas[] = array_map('trim', $linha);
        }

        fclose($handle);

        if (empty($linhas)) {
            throw new \RuntimeException('O arquivo CSV está vazio.');
        }

        return $linhas;
    }

    private function validarEstrutura(array $linhas): void
    {
        $cabecalho = array_map('strtoupper', array_map('trim', $linhas[0]));
        $colunasObrigatorias = ['UNIDADE', 'SETOR', 'EMAIL'];

        foreach ($colunasObrigatorias as $coluna) {
            if (!in_array($coluna, $cabecalho)) {
                throw new \RuntimeException(
                    "Coluna obrigatória não encontrada: {$coluna}. "
                    . "Colunas esperadas: " . implode(', ', $colunasObrigatorias)
                );
            }
        }
    }

    private function processarDados(array $dados, CsvImport $import, int $numeroLinha): void
    {
        $nomeUnidade = $dados['UNIDADE'] ?? null;
        $nomeSetor = $dados['SETOR'] ?? null;
        $email = $dados['EMAIL'] ?? null;

        if (empty($nomeUnidade) || empty($nomeSetor) || empty($email)) {
            throw new \RuntimeException("Linha {$numeroLinha}: campos obrigatórios vazios.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException("Linha {$numeroLinha}: email inválido '{$email}'.");
        }

        $unidade = Unidade::firstOrCreate(
            ['nome' => $nomeUnidade],
            ['codigo' => null, 'is_active' => true]
        );

        $setor = Setor::firstOrCreate(
            ['nome' => $nomeSetor, 'unidade_id' => $unidade->id],
            ['codigo' => null, 'is_active' => true]
        );

        CsvRecord::create([
            'csv_import_id' => $import->id,
            'unidade_id'    => $unidade->id,
            'setor_id'      => $setor->id,
            'email'         => strtolower($email),
            'email_hash'    => hash('sha256', strtolower(trim($email))),
            'linha_csv'     => $numeroLinha,
        ]);
    }
}
