@extends('layouts.app')
@section('title', 'POS Terminal')

@section('content')
<div style="display:grid; grid-template-columns:1fr 320px; gap:16px; align-items:start;">

    {{-- ══════════════════════════════════════════
         LEFT  —  Item Builder
    ══════════════════════════════════════════ --}}
    <div style="display:flex; flex-direction:column; gap:12px;">

        {{-- Step 1: Cup Size --}}
        <div class="card fade-in" style="animation-delay:0s">
            <p class="card-title">01 — Cup Size</p>
            <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px;" id="size-grid">
                @foreach($cupSizes as $size)
                <button type="button" class="opt-btn"
                    data-type="size"
                    data-id="{{ $size->id }}"
                    data-price="{{ $size->price }}"
                    data-name="{{ $size->name }}"
                    data-vol="{{ $size->volume }}">
                    <span class="opt-name">{{ $size->name }}</span>
                    <span class="opt-sub">{{ $size->volume }}</span>
                    <span class="opt-price">{{ number_format($size->price, 2) }} EGP</span>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Step 2: Water Type --}}
        <div class="card fade-in" style="animation-delay:0.05s">
            <p class="card-title">02 — Water Type</p>
            <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px;" id="water-grid">
                @foreach($waterTypes as $water)
                <button type="button" class="opt-btn"
                    data-type="water"
                    data-id="{{ $water->id }}"
                    data-price="{{ $water->price }}"
                    data-name="{{ $water->name }}">
                    <span class="opt-name">{{ $water->name }}</span>
                    <span class="opt-sub">{{ $water->description }}</span>
                    <span class="opt-price">{{ $water->price > 0 ? '+'.number_format($water->price,2).' EGP' : 'Base' }}</span>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Step 3: Extras --}}
        <div class="card fade-in" style="animation-delay:0.1s">
            <p class="card-title">03 — Extras <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--muted);font-size:10px;">(optional · multi-select)</span></p>
            <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:8px;" id="extras-grid">
                @foreach($extras as $extra)
                <button type="button" class="ext-btn"
                    data-type="extra"
                    data-id="{{ $extra->id }}"
                    data-price="{{ $extra->price }}"
                    data-name="{{ $extra->name }}">
                    <div style="display:flex;align-items:center;">
                        <span class="ext-check">✓</span>
                        <span class="ext-name">{{ $extra->name }}</span>
                    </div>
                    <span class="ext-price">+{{ number_format($extra->price,2) }}</span>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Step 4: Quantity + Add --}}
        <div class="card fade-in" style="animation-delay:0.15s; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
            <div class="qty-ctrl">
                <span style="color:var(--muted);font-size:11px;letter-spacing:0.06em;text-transform:uppercase;">Qty</span>
                <button class="qty-btn" id="qty-minus" type="button">−</button>
                <span class="qty-num" id="qty-display">1</span>
                <button class="qty-btn" id="qty-plus"  type="button">+</button>
            </div>

            <div style="text-align:right;">
                <div style="font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:2px;">Item total</div>
                <div id="item-total-display" style="font-family:var(--font-head);font-size:22px;font-weight:700;color:var(--accent);">— EGP</div>
            </div>

            <button class="btn btn-primary" id="add-item-btn" disabled style="padding:11px 24px;font-size:13px;">
                + Add to Order
            </button>
        </div>

    </div>

    {{-- ══════════════════════════════════════════
         RIGHT  —  Order Receipt (sticky)
    ══════════════════════════════════════════ --}}
    <div style="position:sticky; top:68px;">
        <div class="card" style="display:flex;flex-direction:column;gap:0;">

            {{-- Header --}}
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <span style="font-family:var(--font-head);font-size:14px;font-weight:700;color:var(--text);">Order</span>
                <span id="receipt-count" style="font-size:10px;color:var(--muted);background:var(--surface2);border:1px solid var(--border);padding:2px 8px;border-radius:99px;">0 items</span>
            </div>

            {{-- Empty state --}}
            <div id="receipt-empty" style="padding:32px 0;text-align:center;color:var(--muted);font-size:12px;line-height:1.8;">
                No items yet.<br>
                <span style="font-size:11px;opacity:0.6;">Select size + water, then add.</span>
            </div>

            {{-- Items list --}}
            <div id="receipt-items" style="display:none;flex-direction:column;gap:8px;margin-bottom:12px;max-height:340px;overflow-y:auto;padding-right:2px;"></div>

            {{-- Footer totals + actions --}}
            <div id="receipt-footer" style="display:none;">
                <hr class="divider">

                <div style="display:flex;justify-content:space-between;margin-bottom:6px;color:var(--muted);font-size:11px;">
                    <span>Subtotal</span>
                    <span id="r-subtotal" style="font-family:var(--font-mono);">0.00 EGP</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                    <span style="font-family:var(--font-head);font-size:13px;font-weight:600;">Total</span>
                    <span id="r-total" style="font-family:var(--font-head);font-size:24px;font-weight:700;color:var(--accent);">0.00 EGP</span>
                </div>

                <textarea id="order-notes" rows="2" placeholder="Order notes (optional)..." style="margin-bottom:10px;"></textarea>

                <button class="btn btn-primary" id="confirm-btn" type="button" style="width:100%;margin-bottom:6px;">
                    Confirm Order
                </button>
                <button class="btn btn-ghost" id="reset-btn" type="button" style="width:100%;font-size:11px;">
                    Clear order
                </button>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ── State ────────────────────────────────────────────────
let cur = {
    sizeId:null, sizeName:'', sizeVol:'', sizePrice:0,
    waterId:null, waterName:'', waterPrice:0,
    extraIds: new Set(), extraNames:{}, extraPrices:{},
    qty: 1,
};
let orderItems = [];

// ── Selection ────────────────────────────────────────────
document.querySelectorAll('[data-type="size"]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('[data-type="size"]').forEach(b => b.classList.remove('sel'));
        btn.classList.add('sel');
        cur.sizeId    = btn.dataset.id;
        cur.sizeName  = btn.dataset.name;
        cur.sizeVol   = btn.dataset.vol;
        cur.sizePrice = parseFloat(btn.dataset.price);
        refreshTotal();
    });
});

document.querySelectorAll('[data-type="water"]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('[data-type="water"]').forEach(b => b.classList.remove('sel'));
        btn.classList.add('sel');
        cur.waterId    = btn.dataset.id;
        cur.waterName  = btn.dataset.name;
        cur.waterPrice = parseFloat(btn.dataset.price);
        refreshTotal();
    });
});

document.querySelectorAll('[data-type="extra"]').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        if (cur.extraIds.has(id)) {
            cur.extraIds.delete(id);
            btn.classList.remove('sel');
        } else {
            cur.extraIds.add(id);
            cur.extraNames[id]  = btn.dataset.name;
            cur.extraPrices[id] = parseFloat(btn.dataset.price);
            btn.classList.add('sel');
        }
        refreshTotal();
    });
});

// ── Quantity ─────────────────────────────────────────────
document.getElementById('qty-minus').addEventListener('click', () => {
    if (cur.qty > 1) { cur.qty--; document.getElementById('qty-display').textContent = cur.qty; refreshTotal(); }
});
document.getElementById('qty-plus').addEventListener('click', () => {
    if (cur.qty < 99) { cur.qty++; document.getElementById('qty-display').textContent = cur.qty; refreshTotal(); }
});

// ── Refresh item total preview ───────────────────────────
function refreshTotal() {
    const addBtn = document.getElementById('add-item-btn');
    const totalEl = document.getElementById('item-total-display');

    if (!cur.sizeId || !cur.waterId) {
        totalEl.textContent = '— EGP';
        addBtn.disabled = true;
        return;
    }

    const extrasSum = [...cur.extraIds].reduce((s, id) => s + cur.extraPrices[id], 0);
    const unit = cur.sizePrice + cur.waterPrice + extrasSum;
    const line = unit * cur.qty;
    totalEl.textContent = fmt(line) + ' EGP';
    addBtn.disabled = false;
}

// ── Add item to receipt ──────────────────────────────────
document.getElementById('add-item-btn').addEventListener('click', () => {
    const extrasArr  = [...cur.extraIds].map(id => ({ id, name: cur.extraNames[id], price: cur.extraPrices[id] }));
    const extrasSum  = extrasArr.reduce((s, e) => s + e.price, 0);
    const unitPrice  = cur.sizePrice + cur.waterPrice + extrasSum;
    const lineTotal  = unitPrice * cur.qty;

    orderItems.push({
        cup_size_id:   cur.sizeId,
        water_type_id: cur.waterId,
        extra_ids:     [...cur.extraIds],
        quantity:      cur.qty,
        // display
        _cup:      `${cur.sizeName} · ${cur.sizeVol}`,
        _water:    cur.waterName,
        _extras:   extrasArr.map(e => e.name),
        _unit:     unitPrice,
        _line:     lineTotal,
        _qty:      cur.qty,
    });

    renderReceipt();
    resetCurrent();
});

// ── Render receipt ───────────────────────────────────────
function renderReceipt() {
    const empty  = document.getElementById('receipt-empty');
    const list   = document.getElementById('receipt-items');
    const footer = document.getElementById('receipt-footer');
    const count  = document.getElementById('receipt-count');

    count.textContent = orderItems.length + ' item' + (orderItems.length !== 1 ? 's' : '');

    if (orderItems.length === 0) {
        empty.style.display  = 'block';
        list.style.display   = 'none';
        footer.style.display = 'none';
        return;
    }

    empty.style.display  = 'none';
    list.style.display   = 'flex';
    footer.style.display = 'block';

    list.innerHTML = orderItems.map((item, idx) => `
        <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:10px 12px;position:relative;animation:fadeIn 0.2s ease both;">
            <button onclick="removeItem(${idx})" class="btn btn-danger" style="position:absolute;top:8px;right:8px;padding:2px 7px;font-size:11px;cursor:pointer;">✕</button>
            <div style="font-weight:500;color:var(--text);font-size:12px;padding-right:36px;">${item._cup}</div>
            <div style="font-size:11px;color:var(--blue);margin-top:1px;">${item._water}</div>
            ${item._extras.length ? `<div style="font-size:10px;color:var(--muted);margin-top:3px;">+ ${item._extras.join(', ')}</div>` : ''}
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;padding-top:8px;border-top:1px dashed var(--border);">
                <span style="font-size:10px;color:var(--muted);">${item._qty}× ${fmt(item._unit)} EGP</span>
                <span style="font-size:13px;font-weight:600;color:var(--accent);">${fmt(item._line)} EGP</span>
            </div>
        </div>
    `).join('');

    const total = orderItems.reduce((s, i) => s + i._line, 0);
    document.getElementById('r-subtotal').textContent = fmt(total) + ' EGP';
    document.getElementById('r-total').textContent    = fmt(total) + ' EGP';
}

function removeItem(idx) {
    orderItems.splice(idx, 1);
    renderReceipt();
}

// ── Reset current item builder ───────────────────────────
function resetCurrent() {
    cur = { sizeId:null, sizeName:'', sizeVol:'', sizePrice:0, waterId:null, waterName:'', waterPrice:0, extraIds:new Set(), extraNames:{}, extraPrices:{}, qty:1 };
    document.querySelectorAll('[data-type]').forEach(b => b.classList.remove('sel'));
    document.getElementById('qty-display').textContent    = 1;
    document.getElementById('item-total-display').textContent = '— EGP';
    document.getElementById('add-item-btn').disabled = true;
}

// ── Confirm order  (POST /orders) ────────────────────────
document.getElementById('confirm-btn').addEventListener('click', async () => {
    if (!orderItems.length) return;

    const btn = document.getElementById('confirm-btn');
    btn.disabled = true;
    btn.textContent = 'Saving…';

    try {
        const res  = await fetch('{{ route("pos.store") }}', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body:    JSON.stringify({
                notes: document.getElementById('order-notes').value,
                items: orderItems.map(i => ({
                    cup_size_id:   i.cup_size_id,
                    water_type_id: i.water_type_id,
                    extra_ids:     i.extra_ids,
                    quantity:      i.quantity,
                })),
            }),
        });

        const data = await res.json();

        if (data.success) {
            showToast('✓ ' + data.message, 'success');
            orderItems = [];
            renderReceipt();
            document.getElementById('order-notes').value = '';
            resetCurrent();
        } else {
            showToast(data.message || 'Something went wrong.', 'error');
        }
    } catch {
        showToast('Network error. Please try again.', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Confirm Order';
    }
});

// ── Clear full order ─────────────────────────────────────
document.getElementById('reset-btn').addEventListener('click', () => {
    orderItems = [];
    renderReceipt();
    resetCurrent();
    document.getElementById('order-notes').value = '';
});

function fmt(n) { return parseFloat(n).toFixed(2); }
</script>
@endpush
