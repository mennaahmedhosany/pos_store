@extends('layouts.app')
@section('title', $order->order_number)

@section('content')

{{-- Back + header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
    <div>
        <a href="{{ route('pos.orders') }}" style="font-size:11px;color:var(--muted);text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-bottom:6px;">
            ← Orders
        </a>
        <h1 style="font-family:var(--font-head);font-size:22px;font-weight:700;color:var(--accent);letter-spacing:-0.01em;">
            {{ $order->order_number }}
        </h1>
        <p style="font-size:11px;color:var(--muted);margin-top:2px;">
            {{ $order->created_at->format('l, d F Y · H:i') }}
        </p>
    </div>

    <div style="display:flex;align-items:center;gap:10px;">
        @if($order->status === 'completed')
            <span class="badge badge-green" style="padding:5px 12px;font-size:11px;">Completed</span>
        @elseif($order->status === 'cancelled')
            <span class="badge badge-red" style="padding:5px 12px;font-size:11px;">Cancelled</span>
        @else
            <span class="badge badge-amber" style="padding:5px 12px;font-size:11px;">Pending</span>
        @endif
        <span style="font-family:var(--font-head);font-size:24px;font-weight:700;color:var(--text);">
            {{ number_format($order->total, 2) }} <span style="font-size:13px;color:var(--muted);font-weight:500;">EGP</span>
        </span>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 280px;gap:14px;align-items:start;">

    {{-- ══ LEFT: Order items ══ --}}
    <div style="display:flex;flex-direction:column;gap:10px;">

        <div class="card" style="padding:0;overflow:hidden;">
            <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
                <span style="font-family:var(--font-head);font-size:12px;font-weight:600;color:var(--text);">Order Items</span>
                <span style="font-size:11px;color:var(--muted);">{{ $order->items->count() }} item{{ $order->items->count() !== 1 ? 's' : '' }}</span>
            </div>

            @foreach($order->items as $i => $item)
            <div style="padding:16px 20px; {{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }} display:grid; grid-template-columns:auto 1fr auto; gap:14px; align-items:start;">

                {{-- Index badge --}}
                <div style="width:28px;height:28px;border-radius:8px;background:var(--surface2);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:11px;color:var(--muted);font-weight:500;flex-shrink:0;margin-top:2px;">
                    {{ $i + 1 }}
                </div>

                {{-- Item details --}}
                <div>
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px;">
                        <span style="font-size:13px;font-weight:500;color:var(--text);">{{ $item->cupSize->name }}</span>
                        <span style="font-size:10px;color:var(--muted);background:var(--surface2);border:1px solid var(--border);padding:1px 7px;border-radius:99px;">{{ $item->cupSize->volume }}</span>
                        <span style="font-size:11px;color:var(--border2);">×</span>
                        <span style="font-size:13px;font-weight:500;color:var(--blue);">{{ $item->waterType->name }}</span>
                    </div>

                    {{-- Extras --}}
                    @if($item->extras->isNotEmpty())
                    <div style="display:flex;flex-wrap:wrap;gap:5px;margin-top:6px;">
                        @foreach($item->extras as $extra)
                        <span style="font-size:10px;background:rgba(62,207,142,0.08);border:1px solid rgba(62,207,142,0.15);color:var(--green);padding:2px 8px;border-radius:99px;">
                            + {{ $extra->name }}
                            <span style="opacity:0.6;margin-left:2px;">{{ number_format($extra->pivot->price_at_time, 2) }}</span>
                        </span>
                        @endforeach
                    </div>
                    @endif

                    {{-- Price breakdown --}}
                    <div style="display:flex;gap:12px;margin-top:8px;flex-wrap:wrap;">
                        <span style="font-size:10px;color:var(--muted);">
                            Cup <span style="color:var(--text);">{{ number_format($item->cup_price, 2) }}</span>
                        </span>
                        <span style="font-size:10px;color:var(--muted);">
                            Water <span style="color:var(--text);">{{ number_format($item->water_price, 2) }}</span>
                        </span>
                        @if($item->extras_price > 0)
                        <span style="font-size:10px;color:var(--muted);">
                            Extras <span style="color:var(--text);">{{ number_format($item->extras_price, 2) }}</span>
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Right: qty + line total --}}
                <div style="text-align:right;flex-shrink:0;">
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">
                        {{ $item->quantity }}× {{ number_format($item->unit_price, 2) }} EGP
                    </div>
                    <div style="font-family:var(--font-head);font-size:16px;font-weight:700;color:var(--accent);">
                        {{ number_format($item->total_price, 2) }} EGP
                    </div>
                </div>

            </div>
            @endforeach
        </div>

    </div>

    {{-- ══ RIGHT: Order summary ══ --}}
    <div style="position:sticky;top:68px;display:flex;flex-direction:column;gap:10px;">

        {{-- Totals card --}}
        <div class="card">
            <p class="card-title">Summary</p>

            <div style="display:flex;flex-direction:column;gap:8px;">
                @foreach($order->items as $item)
                <div style="display:flex;justify-content:space-between;font-size:11px;">
                    <span style="color:var(--muted);">{{ $item->cupSize->name }} {{ $item->waterType->name }} ×{{ $item->quantity }}</span>
                    <span style="color:var(--text);">{{ number_format($item->total_price, 2) }}</span>
                </div>
                @endforeach
            </div>

            <hr class="divider">

            <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:8px;">
                <span style="color:var(--muted);">Subtotal</span>
                <span style="color:var(--text);">{{ number_format($order->total, 2) }} EGP</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-family:var(--font-head);font-size:13px;font-weight:600;">Total</span>
                <span style="font-family:var(--font-head);font-size:20px;font-weight:700;color:var(--accent);">{{ number_format($order->total, 2) }} EGP</span>
            </div>
        </div>

        {{-- Notes (if any) --}}
        @if($order->notes)
        <div class="card">
            <p class="card-title">Notes</p>
            <p style="font-size:12px;color:var(--muted);line-height:1.6;">{{ $order->notes }}</p>
        </div>
        @endif

        {{-- Meta --}}
        <div class="card" style="display:flex;flex-direction:column;gap:8px;">
            <div style="display:flex;justify-content:space-between;font-size:11px;">
                <span style="color:var(--muted);">Order #</span>
                <span style="font-family:var(--font-mono);color:var(--accent);">{{ $order->order_number }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:11px;">
                <span style="color:var(--muted);">Created</span>
                <span style="color:var(--text);">{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:11px;">
                <span style="color:var(--muted);">Items</span>
                <span style="color:var(--text);">{{ $order->items->count() }}</span>
            </div>
        </div>

        <a href="{{ route('pos.index') }}" class="btn btn-primary" style="text-decoration:none;width:100%;justify-content:center;padding:11px;">
            + New Order
        </a>

    </div>

</div>
@endsection
