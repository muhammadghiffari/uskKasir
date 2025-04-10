<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transactions Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .store-name {
            font-size: 20px;
            font-weight: bold;
        }

        .report-title {
            font-size: 16px;
            margin: 10px 0;
        }

        .store-info {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .report-meta {
            margin-bottom: 20px;
        }

        .report-meta p {
            margin: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .summary {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="store-name">KASIRIN</div>
        <div class="report-title">Transactions Report</div>
        <div class="store-info">
            Jl. Utan Kayu Utara<br>
            Telp: 021-12345678
        </div>
    </div>

    <div class="report-meta">
        <p><strong>Period:</strong> {{ $period }}</p>
        <p><strong>Cashier:</strong> {{ $cashierName }}</p>
        <p><strong>Generated on:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="summary">
        <p><strong>Total Transactions:</strong> {{ $transactionCount }}</p>
        <p><strong>Total Sales:</strong> Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
        <p><strong>Average Transaction Value:</strong> Rp {{ $transactionCount > 0 ? number_format($totalSales / $transactionCount, 0, ',', '.') : 0 }}</p>
    </div>

    <h3>Transaction List</h3>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Transaction Code</th>
                <th>Cashier</th>
                <th>Date & Time</th>
                <th>Items</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $transaction)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $transaction->code }}</td>
                    <td>{{ $transaction->user->name }}</td>
                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $transaction->items->count() }}</td>
                    <td class="text-right">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No transactions found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($transactions->count() > 0)
        <div class="page-break"></div>

        <h3>Transaction Details</h3>
        @foreach($transactions as $index => $transaction)
            <div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px dashed #ccc;">
                <p>
                    <strong>Transaction #{{ $index + 1 }}</strong><br>
                    Code: {{ $transaction->code }}<br>
                    Cashier: {{ $transaction->user->name }}<br>
                    Date: {{ $transaction->created_at->format('d/m/Y H:i') }}
                </p>

                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="text-right">{{ $item->quantity }}</td>
                                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" class="text-right"><strong>Total</strong></td>
                            <td class="text-right"><strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right">Payment</td>
                            <td class="text-right">Rp {{ number_format($transaction->payment_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right">Change</td>
                            <td class="text-right">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if(!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    @endif

    <div style="text-align: center; margin-top: 30px; font-size: 10px;">
        <p>This report was generated automatically by KASIRIN POS System</p>
        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
