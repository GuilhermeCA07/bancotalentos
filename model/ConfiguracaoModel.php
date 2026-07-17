<?php

require_once 'config/Conexao.php';

class ConfiguracaoModel
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::getConnection();
    }

    public function buscar()
    {
        $padrao = [
            'idConfiguracao' => null,
            'corPrimaria' => '#7C3AED',
            'corSecundaria' => '#1E1733',
            'identidade' => 'padrao'
        ];

        $resultado = $this->conexao->query(
            'SELECT * FROM configuracoes ORDER BY idConfiguracao LIMIT 1'
        );

        $registro = $resultado
            ? $resultado->fetch_assoc()
            : null;
        $dados = $registro
            ? array_merge($padrao, $registro)
            : $padrao;

        foreach (['corPrimaria', 'corSecundaria', 'identidade'] as $campo) {
            if (empty($dados[$campo])) {
                $dados[$campo] = $padrao[$campo];
            }
        }

        $dados['corTextoPrimaria'] =
            $this->corContraste($dados['corPrimaria']);
        $dados['corTextoMenu'] =
            $this->corContraste($dados['corSecundaria']);
        $dados['corSecundariaEscura'] =
            $this->escurecer($dados['corSecundaria'], 18);
        $dados['corDestaque'] =
            $dados['corTextoPrimaria'] === '#0F172A'
            ? $dados['corSecundaria']
            : $dados['corPrimaria'];
        $dados['corDestaqueEscura'] =
            $this->escurecer($dados['corDestaque'], 22);
        $dados['corDestaqueOverlay'] =
            $this->rgba($dados['corDestaque'], 0.94);
        $dados['corDestaqueEscuraOverlay'] =
            $this->rgba($dados['corDestaqueEscura'], 0.88);

        $identidade =
            $this->resolverIdentidade($dados['identidade'] ?? 'padrao');
        $dados = array_merge($dados, $identidade);

        return $dados;
    }

    public function salvar($corPrimaria, $corSecundaria, $identidade)
    {
        $atual = $this->buscar();
        $identidade = $this->normalizarIdentidade($identidade);

        if (!empty($atual['idConfiguracao'])) {
            $comando = $this->conexao->prepare(
                'UPDATE configuracoes
                 SET corPrimaria = ?, corSecundaria = ?, identidade = ?,
                     atualizado_em = NOW()
                 WHERE idConfiguracao = ?'
            );
            $comando->bind_param(
                'sssi',
                $corPrimaria,
                $corSecundaria,
                $identidade,
                $atual['idConfiguracao']
            );

            return $comando->execute();
        }

        $comando = $this->conexao->prepare(
            'INSERT INTO configuracoes (corPrimaria, corSecundaria, identidade)
             VALUES (?, ?, ?)'
        );
        $comando->bind_param(
            'sss',
            $corPrimaria,
            $corSecundaria,
            $identidade
        );

        return $comando->execute();
    }

    private function resolverIdentidade($identidade)
    {
        $identidade = $this->normalizarIdentidade($identidade);
        $marcas = [
            'padrao' => [
                'nomeMarca' => 'Talentos',
                'iconeMarca' => 'public/img/branding/talentos-icon.svg',
                'logoMarca' => 'public/img/branding/talentos-icon.svg',
                'tipoIconeMarca' => 'image/svg+xml'
            ],
            'netcom' => [
                'nomeMarca' => 'Netcom',
                'iconeMarca' => 'public/img/branding/netcom-icon.png',
                'logoMarca' => 'public/img/branding/netcom-logo.png',
                'tipoIconeMarca' => 'image/png'
            ],
            'sumernet' => [
                'nomeMarca' => 'SumerNet',
                'iconeMarca' => 'public/img/branding/sumernet-icon.png',
                'logoMarca' => 'public/img/branding/sumernet-logo.png',
                'tipoIconeMarca' => 'image/png'
            ],
            'netaki' => [
                'nomeMarca' => 'NetAki',
                'iconeMarca' => 'public/img/branding/netaki-icon.png',
                'logoMarca' => 'public/img/branding/netaki-logo.png',
                'tipoIconeMarca' => 'image/png'
            ]
        ];

        return array_merge(
            ['identidade' => $identidade],
            $marcas[$identidade]
        );
    }

    private function normalizarIdentidade($identidade)
    {
        $identidade = strtolower(trim((string)$identidade));

        return in_array(
            $identidade,
            ['padrao', 'netcom', 'sumernet', 'netaki'],
            true
        )
            ? $identidade
            : 'padrao';
    }

    private function corContraste($hex)
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) !== 6) {
            return '#FFFFFF';
        }

        $rgb = [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        ];
        $luminancia =
            (0.299 * $rgb[0]
            + 0.587 * $rgb[1]
            + 0.114 * $rgb[2]) / 255;

        return $luminancia > 0.58
            ? '#0F172A'
            : '#FFFFFF';
    }

    private function escurecer($hex, $percentual)
    {
        $hex = ltrim($hex, '#');
        $fator = (100 - $percentual) / 100;
        $partes = [];

        foreach ([0, 2, 4] as $inicio) {
            $partes[] = str_pad(
                dechex((int)round(hexdec(substr($hex, $inicio, 2)) * $fator)),
                2,
                '0',
                STR_PAD_LEFT
            );
        }

        return '#' . strtoupper(implode('', $partes));
    }

    private function rgba($hex, $opacidade)
    {
        $hex = ltrim($hex, '#');
        $rgb = [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        ];

        return sprintf(
            'rgba(%d, %d, %d, %.2F)',
            $rgb[0],
            $rgb[1],
            $rgb[2],
            $opacidade
        );
    }
}
