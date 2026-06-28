<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist #{{ $checklist->id }} — Print</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 13px; color: #111; padding: 30px; }
        .header { text-align: center; border-bottom: 2px solid #22c55e; padding-bottom: 16px; margin-bottom: 24px; }
        .header h1 { font-size: 20px; color: #15803d; }
        .header p  { font-size: 12px; color: #555; margin-top: 4px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-good    { background: #dcfce7; color: #166534; }
        .badge-average { background: #fef9c3; color: #854d0e; }
        .badge-bad     { background: #fee2e2; color: #991b1b; }
        .section { margin-bottom: 20px; }
        .section h2 { font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em;
                      color: #555; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; margin-bottom: 10px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .field label { display: block; font-size: 11px; color: #888; margin-bottom: 2px; }
        .field p { font-weight: 600; }
        .sig-box { border: 1px solid #d1d5db; border-radius: 8px; padding: 12px; min-height: 100px;
                   display: flex; align-items: center; justify-content: center; }
        .sig-box img { max-height: 90px; }
        .sig-empty { color: #aaa; font-style: italic; font-size: 12px; }
        .footer { margin-top: 40px; font-size: 11px; color: #888; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 12px; }
        @media print {
            body { padding: 10px; }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🌸 Flower Quality Inspection Checklist</h1>
        <p>Zimbabwe Centre for High Performance Computing (ZCHPC)</p>
        <p>Record #{{ $checklist->id }} &middot; Printed {{ now()->format('d M Y H:i') }}</p>
    </div>

    <div class="section">
        <h2>Inspection Details</h2>
        <div class="grid">
            <div class="field">
                <label>Date</label>
                <p>{{ $checklist->check_date->format('l, d F Y') }}</p>
            </div>
            <div class="field">
                <label>Time</label>
                <p>{{ $checklist->check_time }}</p>
            </div>
            <div class="field">
                <label>Condition</label>
                <p>
                    <span class="badge badge-{{ $checklist->condition }}">
                        {{ $checklist->condition_label }}
                    </span>
                </p>
            </div>
            <div class="field">
                <label>Recorded By</label>
                <p>{{ $checklist->user->name ?? 'N/A' }}</p>
            </div>
            <div class="field" style="grid-column: span 2;">
                <label>Remarks</label>
                <p>{{ $checklist->remarks ?? '—' }}</p>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Signatures</h2>
        <div class="grid">
            <div>
                <label style="font-size: 11px; color:#888; display:block; margin-bottom:6px;">Staff Member Signature</label>
                <div class="sig-box">
                    @if ($checklist->staff_signature)
                        <img src="{{ public_path('storage/' . $checklist->staff_signature) }}" alt="Staff Signature">
                    @else
                        <span class="sig-empty">Not captured</span>
                    @endif
                </div>
            </div>
            <div>
                <label style="font-size: 11px; color:#888; display:block; margin-bottom:6px;">Flower Supplier Signature</label>
                <div class="sig-box">
                    @if ($checklist->supplier_signature)
                        <img src="{{ public_path('storage/' . $checklist->supplier_signature) }}" alt="Supplier Signature">
                    @else
                        <span class="sig-empty">Not captured</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        Created {{ $checklist->created_at->format('d M Y H:i') }} &middot;
        Last updated {{ $checklist->updated_at->format('d M Y H:i') }}
    </div>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
