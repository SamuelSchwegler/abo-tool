<!DOCTYPE html>
<html lang="de">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
<div class="head">
    <table>
        <tr>
            <td style="text-align: left">
                <img src="/img/logo-gsh.png" alt="" style="width: 6cm">
            </td>
            <td style="height: 100px"></td>
            <td></td>
        </tr>
        <tr>
            <td>
                <span style="font-size: 10pt">{{$receiver['name']}}</span><br>
                <span style="font-size: 10pt">{{$receiver['street']}}</span><br>
                <span style="font-size: 10pt">{{$receiver['postcode']}} {{$receiver['city']}}</span>
            </td>
            <td style="height: 100px"></td>
            <td></td>
        </tr>
    </table>
    <p style="font-size: 10pt">Ihre Bestellung vom {{$meta['issue_date']}}:</p>
    <table border="1" cellpadding="6">
        <tr>
            <td style="font-size: 10pt" width="40%">Bezeichnung</td>
            <td style="font-size: 10pt; text-align: right" width="20%">Preis</td>
            <td style="font-size: 10pt" width="10%">Anzahl</td>
            <td style="font-size: 10pt" width="10%">MwSt</td>
            <td style="font-size: 10pt; text-align: right" width="20%">Gesamt</td>
        </tr>
        @foreach($items as $item)
            <tr>
                <td style="font-size: 10pt">{{$item['name']}}</td>
                <td style="font-size: 10pt; text-align: right">{{$item['single_price']}}</td>
                <td style="font-size: 10pt">{{$item['count']}}</td>
                <td style="font-size: 10pt">@if(isset($item['vat'])){{$item['vat']}} % @else 0.00 @endif</td>
                <td style="font-size: 10pt; text-align: right">{{$item['total_price']}}</td>
            </tr>
        @endforeach
        <tr>
            <td style="font-size: 10pt">inkl MWST</td>
            <td></td>
            <td></td>
            <td></td>
            <td style="font-size: 10pt; text-align: right"><b>{{$meta['total_vat']}}</b></td>
        </tr>
        <tr>
            <td><b style="font-size: 10pt">Total</b></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="font-size: 10pt; text-align: right"><b>{{$meta['total_price']}}</b></td>
        </tr>
    </table>
    <p style="font-size: 10pt">{{$meta['summary_text']}}</p>
</div>
</body>
</html>
