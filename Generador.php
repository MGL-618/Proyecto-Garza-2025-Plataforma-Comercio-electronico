<?php
// Motor de Procesamiento Estocástico SIM-PRO
class ValidadorEstocastico {
    public function ejecutarAnalisis($semilla, $const_a, $const_c, $modulo, $iteraciones) {
        $data_serie = [];
        $conteo_clases = array_fill(0, 5, 0); 
        $valor_actual = $semilla;

        for ($j = 0; $j < $iteraciones; $j++) {
            // Algoritmo LCG
            $valor_actual = ($const_a * $valor_actual + $const_c) % $modulo;
            $resultado_ri = $valor_actual / ($modulo - 1);

            // Clasificación de datos
            $segmento = min(floor($resultado_ri / 0.2), 4);
            $conteo_clases[$segmento]++;

            $data_serie[] = [
                'id' => $j + 1,
                'valor_xn' => $valor_actual,
                'valor_ri' => round($resultado_ri, 4)
            ];
        }

        // Análisis Ji-Cuadrada
        $esperado = $iteraciones / 5;
        $chi_cuadrada = 0;
        foreach ($conteo_clases as $observado) {
            $chi_cuadrada += pow($observado - $esperado, 2) / $esperado;
        }

        return [
            'lista_numeros' => $data_serie,
            'frecuencias' => $conteo_clases,
            'ji_calculado' => round($chi_cuadrada, 4),
            'estatus_final' => ($chi_cuadrada < 9.48) 
        ];
    }
}