<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleSheetsService
{
    public function obtenerPedidos(): array
    {
        $spreadsheetId = env('GOOGLE_SHEETS_ID');

        $url = "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/gviz/tq";

        $response = Http::timeout(20)->get($url, [
            'tqx' => 'out:csv',
            'gid' => 0,
        ]);

        if (!$response->successful()) {
            throw new \Exception(
                'No se pudo conectar con Google Sheets.'
            );
        }

        return $this->convertirCsv($response->body());
    }

    private function convertirCsv(string $csv): array
    {
        $filas = array_map(
            'str_getcsv',
            preg_split("/\r\n|\n|\r/", trim($csv))
        );

        if (empty($filas)) {
            return [];
        }

        $encabezados = array_map(
            fn ($valor) => trim($valor),
            array_shift($filas)
        );

        $resultado = [];

        foreach ($filas as $fila) {

            if (count($fila) === 1 && trim($fila[0]) === '') {
                continue;
            }

            $fila = array_pad(
                $fila,
                count($encabezados),
                ''
            );

            $resultado[] = array_combine(
                $encabezados,
                array_slice($fila, 0, count($encabezados))
            );
        }

        return $resultado;
    }
}