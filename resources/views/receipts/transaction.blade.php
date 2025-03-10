<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Receipt {{ $transaction->code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }

        .receipt {
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .store-name {
            font-size: 16px;
            font-weight: bold;
        }

        .store-info {
            font-size: 10px;
            margin-bottom: 5px;
        }

        .transaction-info {
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #000;
        }

        .transaction-info p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            text-align: left;
            padding: 3px 0;
        }

        .text-right {
            text-align: right;
        }

        .total-section {
            border-top: 1px dashed #000;
            padding-top: 5px;
        }

        .total-row {
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="header">
            <div class="store-name">KASIRIN</div>
            <div class="store-info">
                Jl. Utan Kayu Utara<br>
                Telp: 021-12345678
            </div>
        </div>

        <div class="transaction-info">
            <p>No. Transaksi: {{ $transaction->code }}</p>
            <p>Tanggal: {{ $transaction->created_at->format('d/m/Y H:i') }}</p>
            <p>Kasir: {{ $transaction->user->name }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="50%">Item</th>
                    <th width="15%" class="text-right">Qty</th>
                    <th width="15%" class="text-right">Harga</th>
                    <th width="20%" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <table>
                <tr>
                    <td>Total</td>
                    <td class="text-right">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Tunai</td>
                    <td class="text-right">Rp {{ number_format($transaction->payment_amount, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td>Kembali</td>
                    <td class="text-right">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Terima Kasih Atas Kunjungan Anda</p>
            <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</p>
        </div>
    </div>
</body>

</html>
