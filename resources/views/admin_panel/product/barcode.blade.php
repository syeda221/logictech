<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode - {{ $product->item_name ?? 'Product' }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        body {
            background-color: #f1f5f9;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .action-bar {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none;
            transition: all .15s ease;
        }
        .btn-print {
            background: #6366f1;
            color: #fff;
            box-shadow: 0 2px 6px rgba(99,102,241,0.3);
        }
        .btn-print:hover {
            background: #4f46e5;
        }
        .btn-close {
            background: #fff;
            color: #475569;
            border-color: #cbd5e1;
        }
        .btn-close:hover {
            background: #f8fafc;
        }

        .barcode-sticker {
            background: #ffffff;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            width: 320px;
            padding: 16px;
            text-align: center;
            box-shadow: 0 4px 16px rgba(15,23,42,0.06);
        }
        .company-name {
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: #0f172a;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .item-title {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.25;
            margin-bottom: 8px;
            word-break: break-word;
        }
        .barcode-img-wrap {
            margin: 6px 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .barcode-img-wrap img {
            max-width: 100%;
            height: 52px;
            image-rendering: pixelated;
        }
        .barcode-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 2.5px;
            color: #1e293b;
            margin-top: 3px;
        }
        .meta-row {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
        }
        .meta-type {
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
        }
        .meta-price {
            font-size: 14px;
            font-weight: 800;
            color: #059669;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
                margin: 0;
                min-height: auto;
                display: block;
            }
            .action-bar {
                display: none !important;
            }
            .barcode-sticker {
                border: none;
                box-shadow: none;
                width: 100%;
                max-width: 50mm;
                padding: 4mm 2mm;
                margin: 0 auto;
                page-break-inside: avoid;
            }
            .barcode-img-wrap img {
                height: 38px;
            }
        }
    </style>
</head>
<body>

<div class="action-bar">
    <button type="button" class="btn btn-print" onclick="window.print()">
        🖨️ Print Barcode
    </button>
    <button type="button" class="btn btn-close" onclick="window.close()">
        Close
    </button>
</div>

<div class="barcode-sticker">
    <div class="company-name">LogicTech</div>
    <div class="item-title">{{ $product->item_name ?? 'Item # ' . ($product->id ?? '') }}</div>
    
    <div class="barcode-img-wrap">
        <img src="{{ $barcode_image }}" alt="Barcode">
    </div>
    
    <div class="barcode-code">{{ $barcode_number }}</div>

    <div class="meta-row">
        <div class="meta-type">
            {{ ($product && $product->item_type === 'finish_goods') ? '⚙️ Finished Goods' : '📦 Raw Material' }}
        </div>
        <div class="meta-price">
            @if(isset($product_data['sale_price']))
                Rs. {{ $product_data['sale_price'] }}
            @elseif($product)
                Rs. {{ number_format($product->sale_price_per_piece ?: $product->sale_price_per_box ?: 0, 2) }}
            @endif
        </div>
    </div>
</div>

</body>
</html>
