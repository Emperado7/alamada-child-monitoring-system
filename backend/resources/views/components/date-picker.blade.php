{{--
    Custom Date Picker Component
    Usage: <x-date-picker name="date" :value="$date" :form-id="'filterForm'" />

    Props:
    - name       : input name attribute (default: 'date')
    - value      : current selected date (Y-m-d)
    - formId     : form to submit on date change (optional)
    - minDate    : minimum selectable date (default: start of current month)
    - maxDate    : maximum selectable date (default: today)
    - inputClass : extra CSS classes for the display button
--}}
@php
    $name      = $name      ?? 'date';
    $value     = $value     ?? now()->toDateString();
    $formId    = $formId    ?? null;
    $minDate   = $minDate   ?? now()->startOfMonth()->toDateString();
    $maxDate   = $maxDate   ?? now()->toDateString();
    $uid       = 'dp_' . substr(md5($name . rand()), 0, 8);
@endphp

<div style="position:relative;display:inline-block" id="{{ $uid }}_wrap">
    {{-- Hidden input that actually submits --}}
    <input type="hidden" name="{{ $name }}" id="{{ $uid }}_val" value="{{ $value }}">

    {{-- Display button --}}
    <div class="input-group"
         style="max-width:200px;border:1.5px solid #e0e0e0;border-radius:10px;overflow:hidden;cursor:pointer"
         onclick="{{ $uid }}_toggle()">
        <span class="input-group-text bg-white border-0" style="color:#2e7d32;padding-right:4px">
            <i class="fas fa-calendar-alt" style="font-size:.9rem"></i>
        </span>
        <span id="{{ $uid }}_display"
              class="form-control form-control-sm border-0 shadow-none bg-white"
              style="cursor:pointer;font-size:.88rem;line-height:2">
            {{ \Carbon\Carbon::parse($value)->format('m/d/Y') }}
        </span>
        <span class="input-group-text bg-white border-0 text-muted" style="padding-left:4px">
            <i class="fas fa-chevron-down" style="font-size:.65rem"></i>
        </span>
    </div>

    {{-- Calendar popup --}}
    <div id="{{ $uid }}_cal"
         style="display:none;position:absolute;top:calc(100% + 4px);left:0;z-index:9999;
                background:#fff;border:1px solid #e0e0e0;border-radius:14px;
                box-shadow:0 8px 32px rgba(0,0,0,.15);padding:16px 14px 12px;width:276px">

        {{-- Month nav --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <button type="button" onclick="{{ $uid }}_nav(-1)"
                    style="background:none;border:none;cursor:pointer;color:#2e7d32;
                           font-size:1.1rem;padding:2px 10px;border-radius:6px"
                    onmouseover="this.style.background='#f0f0f0'"
                    onmouseout="this.style.background='none'">‹</button>
            <span id="{{ $uid }}_hdr" style="font-weight:700;font-size:.9rem;color:#1a1a1a"></span>
            <button type="button" onclick="{{ $uid }}_nav(1)"
                    style="background:none;border:none;cursor:pointer;color:#2e7d32;
                           font-size:1.1rem;padding:2px 10px;border-radius:6px"
                    onmouseover="this.style.background='#f0f0f0'"
                    onmouseout="this.style.background='none'">›</button>
        </div>

        {{-- Day-of-week headers --}}
        <div style="display:grid;grid-template-columns:repeat(7,1fr);text-align:center;margin-bottom:4px">
            @foreach(['Su','Mo','Tu','We','Th','Fr','Sa'] as $d)
            <div style="font-size:.7rem;font-weight:700;color:#9e9e9e;padding:3px 0">{{ $d }}</div>
            @endforeach
        </div>

        {{-- Days grid --}}
        <div id="{{ $uid }}_grid"
             style="display:grid;grid-template-columns:repeat(7,1fr);text-align:center;gap:1px">
        </div>

        {{-- Footer --}}
        <div style="display:flex;justify-content:space-between;margin-top:10px;
                    padding-top:8px;border-top:1px solid #f0f0f0">
            <button type="button" onclick="{{ $uid }}_today()"
                    style="background:none;border:none;color:#2e7d32;font-size:.8rem;
                           font-weight:700;cursor:pointer;padding:0">Today</button>
            <button type="button" onclick="{{ $uid }}_toggle()"
                    style="background:none;border:none;color:#9e9e9e;font-size:.8rem;
                           cursor:pointer;padding:0">Close</button>
        </div>
    </div>
</div>

<script>
(function(){
    var UID    = '{{ $uid }}';
    var selISO = '{{ $value }}';
    var minISO = '{{ $minDate }}';
    var maxISO = '{{ $maxDate }}';
    var FORM   = '{{ $formId }}';

    var selParts = selISO.split('-');
    var vY = parseInt(selParts[0]);
    var vM = parseInt(selParts[1]) - 1;

    var todayD = new Date(); todayD.setHours(0,0,0,0);
    var minD   = parseISO(minISO);
    var maxD   = parseISO(maxISO);

    var MONTHS = ['January','February','March','April','May','June',
                  'July','August','September','October','November','December'];

    function parseISO(s) {
        var p = s.split('-');
        var d = new Date(parseInt(p[0]), parseInt(p[1])-1, parseInt(p[2]));
        d.setHours(0,0,0,0);
        return d;
    }
    function toISO(d) {
        var mm = d.getMonth()+1, dd = d.getDate();
        return d.getFullYear()+'-'+(mm<10?'0':'')+mm+'-'+(dd<10?'0':'')+dd;
    }
    function fmt(iso) {
        var p=iso.split('-');
        return p[1]+'/'+p[2]+'/'+p[0];
    }

    function render() {
        document.getElementById(UID+'_hdr').textContent = MONTHS[vM]+' '+vY;
        var grid = document.getElementById(UID+'_grid');
        grid.innerHTML = '';

        var first = new Date(vY, vM, 1).getDay();
        var days  = new Date(vY, vM+1, 0).getDate();

        for(var i=0;i<first;i++) grid.innerHTML += '<div></div>';

        for(var d=1;d<=days;d++){
            var thisD  = new Date(vY, vM, d); thisD.setHours(0,0,0,0);
            var iso    = toISO(thisD);
            var isSel  = iso === selISO;
            var isTod  = thisD.getTime() === todayD.getTime();
            var before = thisD < minD;
            var after  = thisD > maxD;
            var dis    = before || after;

            // Colours
            var bg, color, fw, cursor, tdeco, border;
            if(isSel){
                bg='#2e7d32'; color='#fff'; fw='700'; cursor='pointer';
                tdeco='none'; border='none';
            } else if(dis){
                bg='transparent'; color='#000'; fw='700'; cursor='not-allowed';
                tdeco='none'; border='none';
            } else if(isTod){
                bg='#e8f5e9'; color='#2e7d32'; fw='700'; cursor='pointer';
                tdeco='none'; border='2px solid #2e7d32';
            } else {
                bg='transparent'; color='#333'; fw='400'; cursor='pointer';
                tdeco='none'; border='none';
            }

            var click = dis ? '' : 'onclick="'+UID+'_pick(\''+iso+'\')"';
            var hover = dis ? ''
                : ' onmouseover="if(this.dataset.sel!==\'1\')this.style.background=\'#f0fdf0\'"'
                + ' onmouseout="if(this.dataset.sel!==\'1\')this.style.background=\''+bg+'\'"';

            grid.innerHTML +=
                '<div '+click+hover
                +' data-sel="'+(isSel?'1':'0')+'"'
                +' style="width:34px;height:34px;border-radius:50%;display:flex;'
                +'align-items:center;justify-content:center;margin:auto;'
                +'font-size:.82rem;font-weight:'+fw+';background:'+bg+';color:'+color+';'
                +'cursor:'+cursor+';text-decoration:'+tdeco+';'
                +(border!=='none'?'border:'+border+';':'')
                +'transition:background .1s">'
                +d+'</div>';
        }
    }

    window[UID+'_toggle'] = function(){
        var cal = document.getElementById(UID+'_cal');
        cal.style.display = cal.style.display==='none' ? 'block' : 'none';
        if(cal.style.display==='block') render();
    };
    window[UID+'_nav'] = function(dir){
        vM += dir;
        if(vM<0){vM=11;vY--;}
        if(vM>11){vM=0;vY++;}
        render();
    };
    window[UID+'_pick'] = function(iso){
        selISO = iso;
        document.getElementById(UID+'_val').value = iso;
        document.getElementById(UID+'_display').textContent = fmt(iso);
        document.getElementById(UID+'_cal').style.display = 'none';
        if(FORM) document.getElementById(FORM).submit();
    };
    window[UID+'_today'] = function(){
        window[UID+'_pick'](toISO(todayD));
    };

    // Close on outside click
    document.addEventListener('click', function(e){
        var w = document.getElementById(UID+'_wrap');
        if(w && !w.contains(e.target)){
            var c = document.getElementById(UID+'_cal');
            if(c) c.style.display='none';
        }
    });
})();
</script>
