<!DOCTYPE html>
<html lang="de">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
<div class="head">
    <table>
        <tr>
            <td>
                <span>{{$issuer['name']}}</span><br>
                <span>{{$issuer['street']}}</span><br>
                <span>{{$issuer['postcode']}} {{$issuer['city']}}</span>
            </td>
            <td style="height: 100px"></td>
            <td style="text-align: right">
                <img src="/img/logo-gsh.png" alt="" style="width: 200px">
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="height: 100px"></td>
            <td>
                <span>{{$receiver['name']}}</span><br>
                <span>{{$receiver['street']}}</span><br>
                <span>{{$receiver['postcode']}} {{$receiver['city']}}</span>
            </td>
        </tr>
    </table>
    <p>Ihre Bestellung vom {{$meta['issue_date']}}:</p>
    <table border="1" cellpadding="6">
        <tr>
            <td width="40%">Bezeichnung</td>
            <td width="20%" style="text-align: right">Preis</td>
            <td width="10%">Anzahl</td>
            <td width="10%">MwSt</td>
            <td width="20%" style="text-align: right">Gesamt</td>
        </tr>
        @foreach($items as $item)
            <tr>
                <td>{{$item['name']}}</td>
                <td  style="text-align: right">{{$item['single_price']}}</td>
                <td>{{$item['count']}}</td>
                <td>{{$item['vat'] ?? '0.00'}}</td>
                <td style="text-align: right">{{$item['total_price']}}</td>
            </tr>
        @endforeach
        <tr>
            <td>inkl MWST</td>
            <td></td>
            <td></td>
            <td></td>
            <td style="text-align: right"><b>{{$meta['total_vat']}}</b></td>
        </tr>
        <tr>
            <td><b>Total</b></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="text-align: right"><b>{{$meta['total_price']}}</b></td>
        </tr>
    </table>
    <p>{{$meta['summary_text']}}</p>
</div>
</body>
</html>
