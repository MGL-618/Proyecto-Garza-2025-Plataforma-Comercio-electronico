<?php
require_once 'Generador.php';

$x0 = $_POST['x0'] ?? '15';
$a  = $_POST['a'] ?? '8';
$c  = $_POST['c'] ?? '13';
$m  = $_POST['m'] ?? '100';
$n  = $_POST['n'] ?? '60';

$simulacion = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $engine = new ValidadorEstocastico();
    $simulacion = $engine->ejecutarAnalisis($x0, $a, $c, $m, $n);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SIM-PRO | Industrial Validation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f4f7f6; }
        .navbar-dark { background-color: #212529; }
        .card-header { background-color: #ffffff; font-weight: bold; border-bottom: 2px solid #198754; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4 shadow">
        <div class="container">
            <span class="navbar-brand mb-0 h1">SIM-PRO v1.0 | Dashboard de Control</span>
        </div>
    </nav>

    <div class="container">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header">CONFIGURACIÓN DE SENSORES</div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-2"><label>Semilla (X0)</label><input type="number" name="x0" class="form-control form-control-sm" value="<?php echo $x0; ?>"></div>
                            <div class="mb-2"><label>Multiplicador (a)</label><input type="number" name="a" class="form-control form-control-sm" value="<?php echo $a; ?>"></div>
                            <div class="mb-2"><label>Incremento (c)</label><input type="number" name="c" class="form-control form-control-sm" value="<?php echo $c; ?>"></div>
                            <div class="mb-2"><label>Módulo (m)</label><input type="number" name="m" class="form-control form-control-sm" value="<?php echo $m; ?>"></div>
                            <div class="mb-3"><label>Muestras (n)</label><input type="number" name="n" class="form-control form-control-sm" value="<?php echo $n; ?>"></div>
                            <button type="submit" class="btn btn-success w-100 fw-bold text-uppercase">Iniciar Validación</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <?php if ($simulacion): ?>
                    <div class="alert <?php echo $simulacion['estatus_final'] ? 'alert-success' : 'alert-danger'; ?> shadow-sm">
                        <h4 class="alert-heading text-center">
                            <?php echo $simulacion['estatus_final'] ? 'SISTEMA CONFIABLE ✅' : 'ALERTA: SESGO DETECTADO ❌'; ?>
                        </h4>
                        <p class="text-center mb-0">Estadístico Ji-Cuadrada: <strong><?php echo $simulacion['ji_calculado']; ?></strong> (Límite: 9.48)</p>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-body"><canvas id="graficaFrecuencias"></canvas></div>
                    </div>

                    <div class="card shadow-sm overflow-auto" style="max-height: 250px;">
                        <table class="table table-sm table-striped">
                            <thead class="table-dark"><tr><th>#</th><th>Xn</th><th>Valor Ri</th></tr></thead>
                            <tbody>
                                <?php foreach ($simulacion['lista_numeros'] as $item): ?>
                                    <tr><td><?php echo $item['id']; ?></td><td><?php echo $item['valor_xn']; ?></td><td><?php echo $item['valor_ri']; ?></td></tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <script>
                        new Chart(document.getElementById('graficaFrecuencias'), {
                            type: 'bar',
                            data: {
                                labels: ['I1', 'I2', 'I3', 'I4', 'I5'],
                                datasets: [{
                                    label: 'Distribución por Intervalo',
                                    data: <?php echo json_encode($simulacion['frecuencias']); ?>,
                                    backgroundColor: 'rgba(25, 135, 84, 0.7)',
                                    borderColor: '#198754',
                                    borderWidth: 2
                                }]
                            }
                        });
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>