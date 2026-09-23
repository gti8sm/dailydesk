<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menus cantine — {{ $monthName }} {{ $year }}</title>
    <style>
        @page { size: A4; margin: 12mm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1f2937; font-size: 10px; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 6mm; padding-bottom: 4mm; border-bottom: 2px solid #16a34a; }
        .header h1 { font-size: 20px; color: #16a34a; margin-bottom: 2px; }
        .header .subtitle { font-size: 12px; color: #6b7280; }
        .header .org { font-size: 11px; color: #374151; margin-top: 2px; font-weight: 600; }

        .weekdays { display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; margin-bottom: 1px; }
        .weekdays div { background: #16a34a; color: #fff; text-align: center; font-weight: 700; font-size: 9px; padding: 3px 0; text-transform: uppercase; }

        .calendar { display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; }
        .cell { border: 1px solid #d1d5db; min-height: 28mm; padding: 2px; position: relative; }
        .cell.empty { background: #f9fafb; border: 1px solid #e5e7eb; }
        .cell.today { border: 2px solid #2563eb; }
        .day-num { font-size: 11px; font-weight: 700; color: #374151; margin-bottom: 2px; }
        .day-num.weekend { color: #9ca3af; }
        .meal-type { font-size: 8px; font-weight: 700; text-transform: uppercase; color: #16a34a; margin-top: 2px; }
        .meal-type.snack { color: #ea580c; }
        .meal-items { font-size: 8px; color: #4b5563; }
        .meal-items div { margin: 1px 0; }
        .meal-items .label { color: #9ca3af; font-size: 7px; text-transform: uppercase; }
        .vege-badge { display: inline-block; font-size: 7px; background: #dcfce7; color: #15803d; padding: 0 3px; border-radius: 3px; margin-top: 1px; }
        .allergens { font-size: 7px; color: #dc2626; margin-top: 1px; }

        .footer { margin-top: 6mm; padding-top: 3mm; border-top: 1px solid #e5e7eb; text-align: center; font-size: 8px; color: #9ca3af; }
        .legend { display: flex; gap: 12px; justify-content: center; margin-top: 3mm; font-size: 8px; color: #6b7280; }
        .legend span { display: flex; align-items: center; gap: 3px; }
        .legend .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }

        @media print {
            .no-print { display: none !important; }
            .cell { min-height: 30mm; }
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Menus de la cantine</h1>
    <div class="subtitle">{{ ucfirst($monthName) }} {{ $year }}</div>
    @if($tenantName)<div class="org">{{ $tenantName }}</div>@endif
</div>

<div class="weekdays">
    <div>Lun</div><div>Mar</div><div>Mer</div><div>Jeu</div><div>Ven</div><div>Sam</div><div>Dim</div>
</div>

<div class="calendar">
    @php
        $startOffset = $firstDay->dayOfWeekIso - 1;
        for ($i = 0; $i < $startOffset; $i++) {
            echo '<div class="cell empty"></div>';
        }
        $daysInMonth = $firstDay->daysInMonth;
        $menusByDate = $menus->groupBy(fn($m) => $m->menu_date->format('Y-m-d'));
        $today = now()->format('Y-m-d');
    @endphp

    @for($d = 1; $d <= $daysInMonth; $d++)
        @php
            $dateKey = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $dayMenus = $menusByDate->get($dateKey, collect());
            $dateObj = \Carbon\Carbon::create($year, $month, $d);
            $isWeekend = $dateObj->isWeekend();
            $isToday = $dateKey === $today;
        @endphp
        <div class="cell{{ $isToday ? ' today' : '' }}">
            <div class="day-num{{ $isWeekend ? ' weekend' : '' }}">{{ $d }}</div>
            @foreach($dayMenus as $menu)
                <div class="meal-type{{ $menu->meal_type === 'snack' ? ' snack' : '' }}">
                    {{ $menu->meal_type === 'lunch' ? 'Déjeuner' : 'Goûter' }}
                    @if($menu->vegetarian)<span class="vege-badge">Végé</span>@endif
                </div>
                <div class="meal-items">
                    @if($menu->starter)<div><span class="label">Entrée</span> {{ $menu->starter }}</div>@endif
                    @if($menu->main_course)<div><span class="label">Plat</span> {{ $menu->main_course }}</div>@endif
                    @if($menu->side_dish)<div><span class="label">Garn.</span> {{ $menu->side_dish }}</div>@endif
                    @if($menu->dessert)<div><span class="label">Dessert</span> {{ $menu->dessert }}</div>@endif
                </div>
                @if(!empty($menu->allergens))
                <div class="allergens">Allergènes : {{ implode(', ', $menu->allergens) }}</div>
                @endif
            @endforeach
        </div>
    @endfor
</div>

<div class="legend">
    <span><span class="dot" style="background:#16a34a"></span> Déjeuner</span>
    <span><span class="dot" style="background:#ea580c"></span> Goûter</span>
    <span><span class="dot" style="background:#dcfce7;border:1px solid #15803d"></span> Végétarien</span>
    <span><span class="dot" style="background:#2563eb"></span> Aujourd'hui</span>
</div>

<div class="footer">
    Document généré le {{ now()->locale('fr')->isoFormat('DD/MM/YYYY à HH:mm') }} — {{ $tenantName ?? 'DailyDesk' }}
</div>

@if(!request()->routeIs('parent.menus.pdf'))
<div class="no-print" style="text-align:center; margin-top: 6mm;">
    <button onclick="window.print()" style="background:#16a34a;color:#fff;border:none;padding:8px 24px;border-radius:6px;font-size:12px;cursor:pointer;">
        Imprimer / Enregistrer en PDF
    </button>
</div>
@endif

</body>
</html>
