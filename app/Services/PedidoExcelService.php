<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class PedidoExcelService
{
    /**
     * Obtener pedidos RECOGERA de hoy.
     *
     * Si $numero viene informado, devuelve solamente ese pedido.
     */
    public function recogeraDeHoy(?string $numero = null): array
    {
        $url = config('services.pedidos_excel.url');

        if (!$url) {
            return [];
        }

        try {
            $response = Http::timeout(10)->get($url);

            if (!$response->successful()) {
                return [];
            }

            $body = trim($response->body());

            if ($body === '') {
                return [];
            }

            $lines = preg_split('/\r\n|\n|\r/', $body);

            if (empty($lines)) {
                return [];
            }

            $headers = array_map(
                fn ($header) => $this->normalizarTexto($header),
                str_getcsv(array_shift($lines))
            );

            $pedidos = [];

            foreach ($lines as $line) {

                if (trim($line) === '') {
                    continue;
                }

                $values = str_getcsv($line);
                $row = [];

                foreach ($headers as $index => $header) {
                    $row[$header] = trim($values[$index] ?? '');
                }

                /*
                 * FECHA
                 */
                $fecha = $this->valorColumna($row, [
                    'fecha de entrega',
                    'fecha',
                    'dia',
                ]);

                /*
                 * RECOGERA
                 *
                 * Por ahora utilizamos la misma lógica que ya tenía
                 * tu sistema: buscar "recoger" dentro de toda la fila.
                 */
                $textoFila = $this->normalizarTexto(
                    implode(' ', $values)
                );

                if (
                    !$this->esFechaDeHoy($fecha) ||
                    !str_contains($textoFila, 'recoger')
                ) {
                    continue;
                }

                /*
                 * NÚMERO DE PEDIDO
                 */
                $numeroPedido = $this->valorColumna($row, [
                    'n',
                    'n°',
                    'numero',
                    'numero de pedido',
                    'pedido',
                ]);

                /*
                 * Si estamos buscando un número concreto,
                 * solamente devolvemos ese pedido.
                 */
                if (
                    $numero !== null &&
                    !$this->mismoNumeroPedido(
                        $numeroPedido,
                        $numero
                    )
                ) {
                    continue;
                }

                $pedidos[] = [
                    'numero_pedido' => $numeroPedido,

                    'fecha' => $fecha,

                    'cliente' => $this->valorColumna($row, [
                        'nombre',
                        'cliente',
                    ]),

                    'telefono' => $this->valorColumna($row, [
                        'telefono',
                        'celular',
                        'telefono cliente',
                        'celular cliente',
                    ]),

                    'tipo_entrega' => 'RECOGERA',

                    'transferencia' => $this->valorColumna($row, [
                        'transferencia',
                        'forma de pago',
                        'metodo de pago',
                    ]),

                    'saldo' => $this->valorColumna($row, [
                        'saldo',
                        'restante',
                        'pendiente',
                    ]),

                    /*
                     * TODA LA FILA ORIGINAL DEL EXCEL
                     */
                    'fila' => $row,
                ];
            }

            return $pedidos;

        } catch (\Throwable $exception) {

            report($exception);

            return [];
        }
    }


    /**
     * Buscar una columna entre varios nombres posibles.
     */
    private function valorColumna(
        array $row,
        array $nombres
    ): string {

        foreach ($nombres as $nombre) {

            $normalizado = $this->normalizarTexto($nombre);

            if (
                isset($row[$normalizado]) &&
                $row[$normalizado] !== ''
            ) {
                return $row[$normalizado];
            }
        }

        return '';
    }


    /**
     * Normalizar texto.
     */
    private function normalizarTexto(string $texto): string
    {
        $texto = mb_strtolower(trim($texto));

        return strtr($texto, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ñ' => 'n',
        ]);
    }


    /**
     * Comprobar si una fecha corresponde a hoy.
     */
    private function esFechaDeHoy(string $fecha): bool
    {
        foreach ([
            'd/m/Y',
            'd-m-Y',
            'Y-m-d',
            'd/m/Y H:i',
            'Y-m-d H:i:s',
        ] as $formato) {

            try {

                if (
                    Carbon::createFromFormat(
                        $formato,
                        trim($fecha)
                    )->isToday()
                ) {
                    return true;
                }

            } catch (\Throwable $exception) {
                continue;
            }
        }

        return false;
    }


    /**
     * Comparar números de pedido.
     *
     * También permite que Excel devuelva, por ejemplo, 56.0
     * mientras nosotros buscamos 56.
     */
    private function mismoNumeroPedido(
        string $numeroExcel,
        string $numeroBuscado
    ): bool {

        $numeroExcel = trim($numeroExcel);
        $numeroBuscado = trim($numeroBuscado);

        $numeroExcel = preg_replace(
            '/\.0+$/',
            '',
            $numeroExcel
        );

        $numeroBuscado = preg_replace(
            '/\.0+$/',
            '',
            $numeroBuscado
        );

        return $numeroExcel === $numeroBuscado;
    }
}