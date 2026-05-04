@extends('layouts.app')
@section('title', 'Orders')

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
    <div>
        <h1 style="font-family:var(--font-head);font-size:18px;font-weight:700;color:var(--text);">Order History</h1>
        <p style="font-size:11px;color:var(--muted);margin-top:2px;">All confirmed transactions</p>
    </div>
    <a href="{{ route('pos.index') }}" class="btn btn-primary" style="text-decoration:none;padding:10px 18px;">+ New Order</a>
</div>

<div class="card fade-in" style="padding:0;overflow:hidden;">

    @if($orders->isEmpty())
        <div style="padding:60px 0;text-align:center;color:var(--muted);font-size:13px;">
            No orders yet. <a href="{{ route('pos.index') }}" style="color:var(--accent);text-decoration:none;">Create the first one →</a>
        </div>
    @else

        <table class="tbl">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Items</th>
                    <th style="text-align:right;">Total</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:right;">Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    {{-- Order number --}}
                    <td>
                        <span style="font-family:var(--font-mono);font-weight:500;color:var(--accent);font-size:13px;">
                            {{ $order->order_number }}
                        </span>
                    </td>

                    {{-- Items summary --}}
                    <td>
                        <div style="display:flex;flex-direction:column;gap:3px;">
                            @foreach($order->items->take(2) as $item)
                            <span style="font-size:11px;color:var(--text);">
                                {{ $item->quantity }}× {{ $item->cupSize->name }} · {{ $item->waterType->name }}
                                @if($item->extras->isNotEmpty())
                                <span style="color:var(--muted);">+ {{ $item->extras->pluck('name')->join(', ') }}</span>
                                @endif
                            </span>
                            @endforeach
                            @if($order->items->count() > 2)
                            <span style="font-size:10px;color:var(--muted);">+{{ $order->items->count() - 2 }} more item(s)</span>
                            @endif
                        </div>
                    </td>

                    {{-- Total --}}
                    <td style="text-align:right;">
                        <span style="font-family:var(--font-head);font-size:15px;font-weight:700;color:var(--text);">
                            {{ number_format($order->total, 2) }}
                        </span>
                        <span style="font-size:10px;color:var(--muted);margin-left:2px;">EGP</span>
                    </td>

                    {{-- Status --}}
                    <td style="text-align:center;">
                        @if($order->status === 'completed')
                            <span class="badge badge-green">Completed</span>
                        @elseif($order->status === 'cancelled')
                            <span class="badge badge-red">Cancelled</span>
                        @else
                            <span class="badge badge-amber">Pending</span>
                        @endif
                    </td>

                    {{-- Date --}}
                    <td style="text-align:right;color:var(--muted);font-size:11px;white-space:nowrap;">
                        {{ $order->created_at->format('d M y') }}<br>
                        <span style="opacity:0.6;">{{ $order->created_at->format('H:i') }}</span>
                    </td>

                    {{-- View link --}}
                    <td style="text-align:right;">
                        <a href="{{ route('pos.show', $order) }}"
                           style="color:var(--accent);text-decoration:none;font-size:11px;opacity:0.8;"
                           onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.8">
                            View →
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($orders->hasPages())
        <div style="padding:14px 16px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;">
            <div class="pagination">
                {{ $orders->links() }}
            </div>
        </div>
        @endif

    @endif
</div>
@endsection
