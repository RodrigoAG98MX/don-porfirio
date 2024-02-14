<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de ventas</title>
    <style>
        body {
            text-align: center;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
<h1>Reporte de Ventas</h1>
<hr>
<div class="contenido">
    <img
        src="{{'https://image-charts.com/chart?chs=700x500&cht=bvg&chd=a:' . $data . '&chxl=0:|' . $labels . '&chxt=x,y&chtt=Ventas-Diarias'}}">
</div>
</body>
</html>
