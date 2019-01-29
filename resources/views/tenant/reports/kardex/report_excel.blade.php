<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Kardex</title>
    </head>
    <body>
        <div>
            <h3 align="center" class="title"><strong>Reporte Kardex</strong></h3>
        </div>
        <br>
        <div style="margin-top:20px; margin-bottom:15px;">
            <table>
                <tr>
                    <td>
                        <p><b>Empresa: </b></p>
                    </td>
                    <td align="center">
                        <p><strong>{{$company->name}}</strong></p>
                    </td>
                    <td>
                        <p><strong>Fecha: </strong></p>
                    </td>
                    <td align="center">
                        <p><strong>{{date('Y-m-d')}}</strong></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><strong>Ruc: </strong></p>
                    </td>
                    <td align="center">{{$company->number}}</td>
                    <td>
                        <p><strong>Establecimiento: </strong></p>
                    </td>
                    <td align="center">{{$establishment->address}} - {{$establishment->department->description}} - {{$establishment->district->description}}</td>
                </tr>
            </table>
        </div>
        <br>
        @if(!empty($records))
            <div class="">
                <div class=" ">
                    <table class="">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha y hora</th>
                                <th>Tipo transacción</th>
                                <th>Número</th>
                                <th>Entrada</th>
                                <th>Salida</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $key => $value)
                            <tr>
                                <td class="celda">{{$value->id}}</td>
                                <td class="celda">{{$value->created_at}}</td>
                                <td class="celda">{{($value->type == 'sale') ? 'Venta' : 'Compra'}}</td>
                                <td class="celda">{{($value->type == 'sale') ? "{$value->document->series}-{$value->document->number}" : "{$value->purchase->series}-{$value->purchase->number}"}}</td>
                                <td class="celda">{{($value->type == 'purchase') ? number_format($value->quantity, 2) : number_format(0, 2)}}</td>
                                <td class="celda">{{($value->type == 'sale') ? number_format($value->quantity, 2) : number_format(0, 2)}}</td>
                                @php
                                    if ($value->type == 'purchase') $balance += $value->quantity;
                                    if ($value->type == 'sale') $balance -= $value->quantity;
                                @endphp
                                <td class="celda">{{number_format($balance, 2)}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div>
                <p>No se encontraron registros.</p>
            </div>
        @endif
    </body>
</html>
