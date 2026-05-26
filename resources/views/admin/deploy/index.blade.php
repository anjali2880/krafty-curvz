@extends('layouts.admin')

@section('title', 'Deployment Manager')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Deployment Manager</h1>
            <p class="text-sm text-gray-500 mt-1">Deploy the latest code from <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs font-mono">main</code> branch to the production server.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Back to Dashboard
        </a>
    </div>

    {{-- ── Status Banner ────────────────────────────────────────────────────── --}}
    @if($isRunning)
    <div id="running-banner" class="flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-800 px-5 py-3 rounded-xl mb-6">
        <svg class="w-5 h-5 animate-spin text-amber-500 flex-shrink-0" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        <span class="font-medium text-sm">A deployment is currently in progress. The Deploy button is disabled until it finishes.</span>
    </div>
    @endif

    {{-- ── Deploy Card ──────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-6">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-gray-900">Deploy to Production</p>
                <p class="text-xs text-gray-500">Pulls from git, installs Composer deps, runs migrations &amp; clears cache.</p>
            </div>
        </div>

        <div class="px-6 py-5">
            {{-- Server info (masked password) --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 mb-1">Host</p>
                    <p class="text-sm font-mono text-gray-800 truncate">{{ config('deploy.host') ?: '—' }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 mb-1">Port</p>
                    <p class="text-sm font-mono text-gray-800">{{ config('deploy.port') }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 mb-1">User</p>
                    <p class="text-sm font-mono text-gray-800 truncate">{{ config('deploy.username') ?: '—' }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 mb-1">Path</p>
                    <p class="text-sm font-mono text-gray-800 truncate" title="{{ config('deploy.path') }}">{{ Str::limit(config('deploy.path'), 28) ?: '—' }}</p>
                </div>
            </div>

            {{-- Commands preview --}}
            <div class="bg-gray-900 rounded-xl p-4 mb-6 font-mono text-xs text-gray-300 space-y-1.5">
                <p class="text-gray-500 text-[10px] uppercase tracking-widest mb-2">Commands that will run</p>
                @foreach(config('deploy.commands') as $cmd)
                <div class="flex items-start gap-2">
                    <span class="text-emerald-400 select-none">$</span>
                    <span>{{ $cmd }}</span>
                </div>
                @endforeach
            </div>

            {{-- Deploy button --}}
            <form id="deploy-form">
                @csrf
                <button
                    id="deploy-btn"
                    type="submit"
                    {{ $isRunning ? 'disabled' : '' }}
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-sm transition-all
                        {{ $isRunning
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                            : 'bg-indigo-600 text-white hover:bg-indigo-700 active:scale-95 shadow-sm' }}"
                >
                    <svg id="btn-icon-rocket" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.82m5.84-2.56a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.82m2.56-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                    </svg>
                    <svg id="btn-icon-spin" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span id="btn-label">{{ $isRunning ? 'Deployment Running…' : 'Deploy Now' }}</span>
                </button>
            </form>
        </div>
    </div>

    {{-- ── Live Output Panel ─────────────────────────────────────────────────── --}}
    <div id="output-panel" class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-6 hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span id="status-dot" class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                <p class="font-semibold text-gray-900 text-sm">Deployment Output</p>
                <span id="status-badge" class="ml-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">Running…</span>
            </div>
            <span id="duration-badge" class="text-xs text-gray-400 hidden"></span>
        </div>
        <pre id="output-body" class="p-5 text-xs font-mono bg-gray-950 text-gray-200 rounded-b-2xl overflow-x-auto min-h-[160px] max-h-[480px] overflow-y-auto whitespace-pre-wrap leading-relaxed"></pre>
    </div>

    {{-- ── Deployment History ───────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <p class="font-semibold text-gray-900 text-sm">Deployment History</p>
            <span class="text-xs text-gray-400">Last 15 deployments</span>
        </div>

        @if($logs->isEmpty())
            <div class="px-6 py-12 text-center text-sm text-gray-400">No deployments yet.</div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($logs as $log)
            <div class="px-6 py-4 flex items-start gap-4 hover:bg-gray-50/50 transition-colors">
                {{-- Status icon --}}
                <div class="flex-shrink-0 mt-0.5">
                    @if($log->status === 'success')
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-100">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                    @elseif($log->status === 'failed')
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-rose-100">
                            <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </span>
                    @elseif($log->status === 'running')
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-100">
                            <svg class="w-4 h-4 text-amber-600 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        </span>
                    @else
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                    @endif
                </div>

                {{-- Details --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span @class([
                            'text-xs font-semibold px-2 py-0.5 rounded-full',
                            'bg-emerald-100 text-emerald-700' => $log->status === 'success',
                            'bg-rose-100 text-rose-700'       => $log->status === 'failed',
                            'bg-amber-100 text-amber-700'     => $log->status === 'running',
                            'bg-gray-100 text-gray-600'       => $log->status === 'pending',
                        ])>{{ ucfirst($log->status) }}</span>
                        <span class="text-xs text-gray-500">by {{ optional($log->user)->email ?? 'Unknown' }}</span>
                        @if($log->durationSeconds() !== null)
                            <span class="text-xs text-gray-400">— {{ $log->durationSeconds() }}s</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 mt-1">
                        Started: {{ $log->started_at?->format('d M Y, H:i:s') ?? 'N/A' }}
                        @if($log->finished_at)
                            &nbsp;·&nbsp; Finished: {{ $log->finished_at->format('H:i:s') }}
                        @endif
                    </p>
                </div>

                {{-- Toggle log output --}}
                @if($log->output)
                <button
                    type="button"
                    onclick="toggleLog({{ $log->id }})"
                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex-shrink-0 mt-1"
                >View log</button>
                @endif
            </div>

            {{-- Collapsible log output --}}
            @if($log->output)
            <div id="log-{{ $log->id }}" class="hidden px-6 pb-4">
                <pre class="text-xs font-mono bg-gray-950 text-gray-200 p-4 rounded-xl overflow-x-auto max-h-72 overflow-y-auto whitespace-pre-wrap leading-relaxed">{{ $log->output }}</pre>
            </div>
            @endif
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const DEPLOY_URL  = "{{ route('admin.deploy.run') }}";
    const STATUS_BASE = "{{ url('admin/deploy') }}/";
    const CSRF        = document.querySelector('meta[name="csrf-token"]').content;

    let pollingTimer  = null;
    let activeLogId   = null;

    /* ── DOM refs ─────────────────────────────────────────────────────────── */
    const form        = document.getElementById('deploy-form');
    const btn         = document.getElementById('deploy-btn');
    const btnLabel    = document.getElementById('btn-label');
    const btnIconRocket = document.getElementById('btn-icon-rocket');
    const btnIconSpin = document.getElementById('btn-icon-spin');
    const outputPanel = document.getElementById('output-panel');
    const outputBody  = document.getElementById('output-body');
    const statusDot   = document.getElementById('status-dot');
    const statusBadge = document.getElementById('status-badge');
    const durationBadge = document.getElementById('duration-badge');

    /* ── Helpers ──────────────────────────────────────────────────────────── */
    function setButtonLoading(loading) {
        btn.disabled = loading;
        btnLabel.textContent = loading ? 'Deploying…' : 'Deploy Now';
        btnIconRocket.classList.toggle('hidden', loading);
        btnIconSpin.classList.toggle('hidden', !loading);
        btn.className = loading
            ? 'inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-sm bg-gray-100 text-gray-400 cursor-not-allowed'
            : 'inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-sm bg-indigo-600 text-white hover:bg-indigo-700 active:scale-95 shadow-sm transition-all';
    }

    function showOutput(text) {
        outputPanel.classList.remove('hidden');
        outputBody.textContent = text;
        // Auto-scroll to bottom
        outputBody.scrollTop = outputBody.scrollHeight;
    }

    function setStatus(status, duration) {
        const map = {
            running: { dot: 'bg-amber-400 animate-pulse', badge: 'bg-amber-100 text-amber-700', label: 'Running…' },
            success: { dot: 'bg-emerald-500',             badge: 'bg-emerald-100 text-emerald-700', label: 'Success' },
            failed:  { dot: 'bg-rose-500',                badge: 'bg-rose-100 text-rose-700',     label: 'Failed'  },
        };
        const s = map[status] || map.running;

        statusDot.className   = `w-2.5 h-2.5 rounded-full ${s.dot}`;
        statusBadge.className = `ml-1 text-xs font-semibold px-2 py-0.5 rounded-full ${s.badge}`;
        statusBadge.textContent = s.label;

        if (duration !== undefined && duration !== null) {
            durationBadge.textContent = `${duration}s`;
            durationBadge.classList.remove('hidden');
        }
    }

    function stopPolling() {
        if (pollingTimer) {
            clearInterval(pollingTimer);
            pollingTimer = null;
        }
    }

    function startPolling(logId) {
        stopPolling();
        activeLogId = logId;

        pollingTimer = setInterval(async () => {
            try {
                const res  = await fetch(`${STATUS_BASE}${logId}/status`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await res.json();

                showOutput(data.output || '');
                setStatus(data.status, data.duration);

                if (data.status === 'success' || data.status === 'failed') {
                    stopPolling();
                    setButtonLoading(false);
                    // Reload history section after 1.5s
                    setTimeout(() => window.location.reload(), 1500);
                }
            } catch {
                // Silently ignore polling errors
            }
        }, 2000);
    }

    /* ── Form submit ──────────────────────────────────────────────────────── */
    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (btn.disabled) return;

        setButtonLoading(true);
        showOutput('Connecting to server…\n');
        setStatus('running');

        try {
            const res  = await fetch(DEPLOY_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (res.status === 409) {
                // Another deployment is already running — start polling to show it
                const data = await res.json();
                showOutput(data.error + '\n\nPolling current deployment status…\n');

                // Try to find the running log from history (first row)
                const firstLogEl = document.querySelector('[data-log-id]');
                if (firstLogEl) startPolling(parseInt(firstLogEl.dataset.logId));
                else setButtonLoading(false);

                return;
            }

            const data = await res.json();

            // Deployment finished synchronously (common on shared hosting)
            showOutput(data.output || '');
            setStatus(data.status, data.duration);
            setButtonLoading(false);

            // Brief delay then reload to refresh history table
            setTimeout(() => window.location.reload(), 2000);

        } catch (err) {
            showOutput(`[Client Error] Failed to reach the server.\n${err.message}`);
            setStatus('failed');
            setButtonLoading(false);
        }
    });

    /* ── History toggle ───────────────────────────────────────────────────── */
    window.toggleLog = function (id) {
        const el = document.getElementById(`log-${id}`);
        if (el) el.classList.toggle('hidden');
    };

})();
</script>
@endpush
