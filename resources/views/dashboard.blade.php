<x-layouts.app title="Lead Dashboard">
    <div class="rounded-lg bg-white p-6 shadow-sm">
        <form id="filters" class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <label for="industry" class="block text-sm font-medium text-slate-700">Industry</label>
                <input id="industry" name="industry" type="text" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" placeholder="e.g. oil">
            </div>
            <div>
                <label for="company_industry" class="block text-sm font-medium text-slate-700">Company Industry</label>
                <input id="company_industry" name="company_industry" type="text" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" placeholder="e.g. oil">
            </div>
            <div>
                <label for="location" class="block text-sm font-medium text-slate-700">Location</label>
                <input id="location" name="location" type="text" class="mt-1 w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" placeholder="e.g. texas">
            </div>
        </form>

        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-4">
            <label class="flex items-center gap-2 text-sm text-slate-700"><input id="require_phone" type="checkbox" class="rounded border-slate-300"> Require Phone</label>
            <label class="flex items-center gap-2 text-sm text-slate-700"><input id="require_email" type="checkbox" class="rounded border-slate-300"> Require Email</label>
            <label class="flex items-center gap-2 text-sm text-slate-700"><input id="require_company_email" type="checkbox" class="rounded border-slate-300"> Require Company Email</label>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-md border border-slate-200 p-4">
                <div class="text-xs text-slate-500">Total</div>
                <div id="count_total" class="text-2xl font-semibold">0</div>
            </div>
            <div class="rounded-md border border-slate-200 p-4">
                <div class="text-xs text-slate-500">With Phone</div>
                <div id="count_phone" class="text-2xl font-semibold">0</div>
            </div>
            <div class="rounded-md border border-slate-200 p-4">
                <div class="text-xs text-slate-500">With Email</div>
                <div id="count_email" class="text-2xl font-semibold">0</div>
            </div>
            <div class="rounded-md border border-slate-200 p-4">
                <div class="text-xs text-slate-500">With Company Email</div>
                <div id="count_company_email" class="text-2xl font-semibold">0</div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between">
            <div class="text-sm text-slate-600">Preview first 100 results under current filters.</div>
            <button id="pull_btn" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-700">Pull to Destination</button>
        </div>

        <div class="mt-3 overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-600">
                <tr id="preview_head"><th class="px-3 py-2">Preview</th></tr>
                </thead>
                <tbody id="preview_body" class="divide-y divide-slate-100"></tbody>
            </table>
        </div>

        <div id="pull_status" class="mt-4 hidden rounded-md border border-slate-200 p-4 text-sm text-slate-700"></div>
    </div>

    <script>
        const csrf = document.querySelector('meta[name=csrf-token]')?.content;
        const inputs = ['industry','company_industry','location','require_phone','require_email','require_company_email'].map(id => document.getElementById(id));
        const countsEls = { total: document.getElementById('count_total'), phone: document.getElementById('count_phone'), email: document.getElementById('count_email'), company_email: document.getElementById('count_company_email') };
        const headEl = document.getElementById('preview_head');
        const bodyEl = document.getElementById('preview_body');
        const pullBtn = document.getElementById('pull_btn');
        const pullStatus = document.getElementById('pull_status');

        function gatherParams() {
            return {
                industry: inputs[0].value || '',
                company_industry: inputs[1].value || '',
                location: inputs[2].value || '',
                require_phone: inputs[3].checked,
                require_email: inputs[4].checked,
                require_company_email: inputs[5].checked,
            };
        }

        function debounce(fn, wait=350){ let t; return (...args)=>{ clearTimeout(t); t=setTimeout(()=>fn(...args), wait); } }

        async function fetchCounts() {
            const res = await fetch('{{ route('leads.counts') }}', {
                method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(gatherParams())
            });
            if (!res.ok) return;
            const json = await res.json();
            countsEls.total.textContent = json.total ?? 0;
            countsEls.phone.textContent = json.with_phone ?? 0;
            countsEls.email.textContent = json.with_email ?? 0;
            countsEls.company_email.textContent = json.with_company_email ?? 0;
        }

        async function fetchPreview(page=1) {
            const params = new URLSearchParams(Object.entries(gatherParams()).map(([k,v])=>[k, typeof v==='boolean'? (v? '1':'0') : v]));
            params.set('page', page);
            const res = await fetch('{{ route('leads.index') }}' + '?' + params.toString());
            if (!res.ok) return;
            const json = await res.json();
            renderPreview(json.data || []);
        }

        function renderPreview(rows){
            bodyEl.innerHTML = '';
            if (!rows.length) { headEl.innerHTML = '<th class="px-3 py-2">No results</th>'; return; }
            // Build header from keys of first row
            const keys = Object.keys(rows[0]);
            headEl.innerHTML = keys.map(k=>`<th class=\"px-3 py-2\">${k}</th>`).join('');
            for (const r of rows){
                const tr = document.createElement('tr');
                tr.innerHTML = keys.map(k=>`<td class=\"px-3 py-2\">${r[k] ?? ''}</td>`).join('');
                bodyEl.appendChild(tr);
            }
        }

        const onChange = debounce(()=>{ fetchCounts(); fetchPreview(1); });
        inputs.forEach(el => el.addEventListener('input', onChange));
        inputs.slice(3).forEach(el => el.addEventListener('change', onChange));

        pullBtn.addEventListener('click', async (e)=>{
            e.preventDefault();
            pullBtn.disabled = true;
            pullBtn.textContent = 'Starting...';
            pullStatus.classList.remove('hidden');
            pullStatus.textContent = 'Starting pull job...';
            const res = await fetch('{{ route('pull.start') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(gatherParams()) });
            if (!res.ok) { pullStatus.textContent = 'Failed to start pull job'; pullBtn.disabled=false; pullBtn.textContent='Pull to Destination'; return; }
            const { id } = await res.json();
            pullBtn.textContent = 'Running...';
            pollStatus(id);
        });

        async function pollStatus(id){
            const url = '{{ url('/pull') }}' + '/' + id + '/status';
            const res = await fetch(url);
            if (!res.ok) { pullStatus.textContent = 'Status error'; return; }
            const s = await res.json();
            pullStatus.textContent = `Job ${s.status}. ${s.processed}/${s.total} processed, ${s.failed} failed.`;
            if (s.status === 'queued' || s.status === 'running') {
                setTimeout(()=>pollStatus(id), 1000);
            } else {
                pullBtn.disabled = false;
                pullBtn.textContent = 'Pull to Destination';
            }
        }

        // initial
        fetchCounts();
        fetchPreview();
    </script>
</x-layouts.app>
