<?php

namespace App\Support;

class HseItQuestionnaire
{
    public const DIMENSOES = [
        'demandas'              => 'Demandas',
        'controle'              => 'Controle',
        'apoio_chefia'          => 'Apoio da Chefia',
        'apoio_colegas'         => 'Apoio dos Colegas',
        'relacionamentos'       => 'Relacionamentos',
        'cargo_funcao'          => 'Cargo/Função',
        'comunicacao_mudancas'  => 'Comunicação e Mudanças',
    ];

    // Negative dimensions: HIGH score = HIGH risk
    public const DIMENSOES_NEGATIVAS = [
        'demandas',
        'relacionamentos',
    ];

    public const OPCOES_RESPOSTA = [
        0 => 'Nunca',
        1 => 'Raramente',
        2 => 'Às vezes',
        3 => 'Frequentemente',
        4 => 'Sempre',
    ];

    public const PERGUNTAS = [
        // ── DEMANDAS (8 perguntas) ──────────────────────────────────────────
        ['numero' => 1,  'dimensao' => 'demandas',             'texto' => 'Tenho que trabalhar em ritmo muito acelerado.'],
        ['numero' => 2,  'dimensao' => 'demandas',             'texto' => 'Tenho prazos impossíveis de cumprir.'],
        ['numero' => 3,  'dimensao' => 'demandas',             'texto' => 'Tenho que trabalhar horas extras ou nos fins de semana.'],
        ['numero' => 4,  'dimensao' => 'demandas',             'texto' => 'Diferentes grupos no trabalho me exigem coisas difíceis de conciliar.'],
        ['numero' => 5,  'dimensao' => 'demandas',             'texto' => 'Sinto que tenho mais trabalho do que consigo realizar.'],
        ['numero' => 6,  'dimensao' => 'demandas',             'texto' => 'Tenho que negligenciar algumas tarefas porque tenho muito trabalho.'],
        ['numero' => 7,  'dimensao' => 'demandas',             'texto' => 'Fico sob pressão excessiva no trabalho.'],
        ['numero' => 8,  'dimensao' => 'demandas',             'texto' => 'Não consigo realizar pausas suficientes durante a jornada de trabalho.'],

        // ── CONTROLE (6 perguntas) ──────────────────────────────────────────
        ['numero' => 9,  'dimensao' => 'controle',             'texto' => 'Posso decidir quando fazer pausas no trabalho.'],
        ['numero' => 10, 'dimensao' => 'controle',             'texto' => 'Tenho liberdade para escolher como realizar o meu trabalho.'],
        ['numero' => 11, 'dimensao' => 'controle',             'texto' => 'Posso influenciar no ritmo do meu trabalho.'],
        ['numero' => 12, 'dimensao' => 'controle',             'texto' => 'Tenho autonomia suficiente para realizar bem o meu trabalho.'],
        ['numero' => 13, 'dimensao' => 'controle',             'texto' => 'Consigo me desenvolver com as tarefas que realizo no trabalho.'],
        ['numero' => 14, 'dimensao' => 'controle',             'texto' => 'Meu trabalho me dá a oportunidade de usar minhas habilidades.'],

        // ── APOIO DA CHEFIA (6 perguntas) ───────────────────────────────────
        ['numero' => 15, 'dimensao' => 'apoio_chefia',         'texto' => 'Meu superior imediato me dá o apoio de que preciso.'],
        ['numero' => 16, 'dimensao' => 'apoio_chefia',         'texto' => 'Meu superior imediato está disposto a ouvir os meus problemas de trabalho.'],
        ['numero' => 17, 'dimensao' => 'apoio_chefia',         'texto' => 'Meu superior imediato me elogia quando faço um bom trabalho.'],
        ['numero' => 18, 'dimensao' => 'apoio_chefia',         'texto' => 'Meu superior imediato age de forma positiva com relação ao bem-estar dos colaboradores.'],
        ['numero' => 19, 'dimensao' => 'apoio_chefia',         'texto' => 'Meu superior imediato responde às minhas solicitações de flexibilidade quando necessário.'],
        ['numero' => 20, 'dimensao' => 'apoio_chefia',         'texto' => 'Meu superior imediato dá atenção adequada ao que digo.'],

        // ── APOIO DOS COLEGAS (4 perguntas) ─────────────────────────────────
        ['numero' => 21, 'dimensao' => 'apoio_colegas',        'texto' => 'Se precisar, meus colegas são capazes de me ajudar em situações difíceis no trabalho.'],
        ['numero' => 22, 'dimensao' => 'apoio_colegas',        'texto' => 'Recebo apoio dos meus colegas quando necessário.'],
        ['numero' => 23, 'dimensao' => 'apoio_colegas',        'texto' => 'Meus colegas estão dispostos a ouvir meus problemas de trabalho.'],
        ['numero' => 24, 'dimensao' => 'apoio_colegas',        'texto' => 'Meus colegas me ajudam quando há muitas tarefas a fazer.'],

        // ── RELACIONAMENTOS (4 perguntas) ────────────────────────────────────
        ['numero' => 25, 'dimensao' => 'relacionamentos',      'texto' => 'Sou submetido a comportamentos agressivos no trabalho, como ser intimidado ou assediado.'],
        ['numero' => 26, 'dimensao' => 'relacionamentos',      'texto' => 'Há tensão e conflitos no meu ambiente de trabalho.'],
        ['numero' => 27, 'dimensao' => 'relacionamentos',      'texto' => 'Sofro assédio moral de meus superiores ou colegas.'],
        ['numero' => 28, 'dimensao' => 'relacionamentos',      'texto' => 'Os relacionamentos no trabalho são hostis ou desrespeitosos.'],

        // ── CARGO/FUNÇÃO (5 perguntas) ───────────────────────────────────────
        ['numero' => 29, 'dimensao' => 'cargo_funcao',         'texto' => 'Sei claramente quais são as responsabilidades do meu cargo.'],
        ['numero' => 30, 'dimensao' => 'cargo_funcao',         'texto' => 'Tenho clareza sobre o que se espera de mim no trabalho.'],
        ['numero' => 31, 'dimensao' => 'cargo_funcao',         'texto' => 'Sei quais são os objetivos e metas do meu setor.'],
        ['numero' => 32, 'dimensao' => 'cargo_funcao',         'texto' => 'Compreendo como o meu trabalho contribui para os objetivos da organização.'],
        ['numero' => 33, 'dimensao' => 'cargo_funcao',         'texto' => 'Tenho tarefas claras e bem definidas.'],

        // ── COMUNICAÇÃO E MUDANÇAS (2 perguntas) ─────────────────────────────
        ['numero' => 34, 'dimensao' => 'comunicacao_mudancas', 'texto' => 'Sou informado previamente quando há mudanças importantes no trabalho.'],
        ['numero' => 35, 'dimensao' => 'comunicacao_mudancas', 'texto' => 'Recebo informações adequadas sobre como as mudanças me afetam.'],
    ];

    public static function getPerguntasPorDimensao(string $dimensao): array
    {
        return array_values(array_filter(
            self::PERGUNTAS,
            fn ($p) => $p['dimensao'] === $dimensao
        ));
    }

    public static function isDimensaoNegativa(string $dimensao): bool
    {
        return in_array($dimensao, self::DIMENSOES_NEGATIVAS, true);
    }

    /**
     * Calculate dimension score (0.0 – 4.0).
     *
     * @param  array<int, int>  $respostas  [questao_numero => valor]
     */
    public static function calcularScore(string $dimensao, array $respostas): float
    {
        $perguntas = self::getPerguntasPorDimensao($dimensao);

        if (empty($perguntas)) {
            return 0.0;
        }

        $soma = 0;
        $count = 0;

        foreach ($perguntas as $pergunta) {
            $numero = $pergunta['numero'];
            if (isset($respostas[$numero])) {
                $soma += $respostas[$numero];
                $count++;
            }
        }

        if ($count === 0) {
            return 0.0;
        }

        return round($soma / $count, 2);
    }

    /**
     * Calculate risk score (0.0 – 1.0).
     * For negative dimensions: high score = high risk.
     * For positive dimensions: low score = high risk.
     */
    public static function calcularScoreRisco(string $dimensao, float $score): float
    {
        if (self::isDimensaoNegativa($dimensao)) {
            return round($score / 4.0, 4);
        }

        return round(1.0 - ($score / 4.0), 4);
    }

    /**
     * Calculate probabilidade (1–4) from risk score.
     */
    public static function calcularProbabilidade(float $scoreRisco): int
    {
        return match (true) {
            $scoreRisco <= 0.25 => 1,
            $scoreRisco <= 0.50 => 2,
            $scoreRisco <= 0.75 => 3,
            default             => 4,
        };
    }

    /**
     * Fixed severidade of 3 for all psychosocial risk dimensions.
     */
    public static function calcularSeveridade(): int
    {
        return 3;
    }

    /**
     * NR = Probabilidade × Severidade.
     */
    public static function calcularNR(int $probabilidade, int $severidade): int
    {
        return $probabilidade * $severidade;
    }

    /**
     * Classify NR value.
     */
    public static function classificarRisco(int $nr): string
    {
        return match (true) {
            $nr <= 3  => 'aceitavel',
            $nr <= 6  => 'moderado',
            $nr <= 9  => 'importante',
            default   => 'critico',
        };
    }

    public static function getLabelClassificacao(string $classificacao): string
    {
        return match ($classificacao) {
            'aceitavel'  => 'Aceitável',
            'moderado'   => 'Moderado',
            'importante' => 'Importante',
            'critico'    => 'Crítico',
            default      => $classificacao,
        };
    }
}
