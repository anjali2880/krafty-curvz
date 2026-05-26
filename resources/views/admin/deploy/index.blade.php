@extends('layouts.admin')

@section('title', 'Deployment Manager')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Deployment Manager</h1>
            <p class="text-sm text-gray-500 mt-1">Push the latest code from <span class="font-mono bg-gray-100 px-1 rounded">main</span> to your production server via SSH.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-800 flex items-center gap-1">
            &larr; Dashboard
        </a>
    </div>

    {{-- Running Warning --}}
    @if($isRunning)
    <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
        <span class="font-medium text-sm">A deployment is already running. Please wait for it to finish.</span>
    </div>
    @endif

    {{-- Config not set warning --}}
    @if(!config('deploy.host') || !config('deploy.username'))
    <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm">
        <strong>SSH not configured.</strong> Please set <code>DEPLOY_HOST</code>, <code>DEPLOY_USER</code>, <code>DEPLOY_PASSWORD</code>, and <code>DEPLOY_PATH</code> in your <code>.env</code> file, then run <code>php artisan config:clear</code>.
    </div>
    @endif

    {{-- ── Main Deploy Card ── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Card Header --}}
        <div class="bg-gray-800 px-6 py-4">
            <h2 class="text-white font-semibold text-lg">Deploy to Production</h2>
            <p class="text-gray-400 text-sm mt-0.5">git pull &rarr; composer install &rarr; migrate &rarr; optimize:clear</p>
        </div>

        <div class="p-6 space-y-6">

            {{-- Server Info Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-gray-50 border border-gray-100 rounded-lg p-3">
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Host</p>
                    <p class="text-sm font-mono text-gray-800 truncate">{{ config('deploy.host') ?: '(not set)' }}</p>
                </div>
                <div class="bg-gray-50 border border-gray-100 rounded-lg p-3">
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Port</p>
                    <p class="text-sm font-mono text-gray-800">{{ config('deploy.port') }}</p>
                </div>
                <div class="bg-gray-50 border border-gray-100 rounded-lg p-3">
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">User</p>
                    <p class="text-sm font-mono text-gray-800 truncate">{{ config('deploy.username') ?: '(not set)' }}</p>
                </div>
                <div class="bg-gray-50 border border-gray-100 rounded-lg p-3">
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Password</p>
                    <p class="text-sm font-mono text-gray-800">{{ config('deploy.password') ? '••••••••' : '(not set)' }}</p>
                </div>
            </div>

            {{-- Path --}}
            <div class="bg-gray-50 border border-gray-100 rounded-lg px-4 py-3">
                <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Project Path</p>
                <p class="text-sm font-mono text-gray-800 break-all">{{ config('deploy.path') ?: '(not set)' }}</p>
            </div>

            {{-- Commands Preview --}}
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-2">Commands that will execute on server</p>
                <div class="bg-gray-900 rounded-lg p-4 space-y-2">
                    @foreach(config('deploy.commands') as $index => $cmd)
                    <div class="flex items-center gap-3 text-sm font-mono">
                        <span class="text-gray-500 select-none w-4 text-right flex-shrink-0">{{ $index + 1 }}</span>
                        <span class="text-green-400">$</span>
                        <span class="text-gray-200">{{ $cmd }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── DEPLOY BUTTON ── --}}
            <div class="pt-2 border-t border-gray-100">
                <button
                    id="deploy-btn"
                    type="button"
                    @if($isRunning || !config('deploy.host')) disabled @endif
                    style="background-color: #4f46e5; color: #ffffff;"
                    class="w-full py-4 rounded-xl font-bold text-lg tracking-wide flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed hover:opacity-90 transition-opacity"
                >
                    <svg id="btn-icon" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.82m5.84-2.56a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.82m2.56-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                    </svg>
                    <span id="btn-label">
                        @if($isRunning)
                            Deployment Running…
                        @elseif(!config('deploy.host'))
                            Configure SSH First
                        @else
                            🚀 Deploy Now
                        @endif
                    </span>
                </button>
                <p class="text-center text-xs text-gray-400 mt-2">This will run the commands above on your production server.</p>
            </div>
        </div>
    </div>

    {{-- ── Live Output ── --}}
    <div id="output-panel" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" style="display:none;">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between bg-gray-50">
            <div class="flex items-center gap-2">
                <span id="status-dot" class="inline-block w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
                <span class="font-semibold text-sm text-gray-800">Deployment Output</span>
                <span id="status-badge" class="text-xs font-semibold px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 ml-1">Running…</span>
            </div>
            <span id="duration-text" class="text-xs text-gray-400"></span>
        </div>
        <pre id="output-body" style="background:#0f172a; color:#e2e8f0; padding:1.25rem; font-size:0.75rem; font-family:monospace; min-height:180px; max-height:500px; overflow-y:auto; white-space:pre-wrap; line-height:1.6; margin:0;"></pre>
    </div>

    {{-- ── Deployment History ── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Deployment History</h3>
            <span class="text-xs text-gray-400">Last 15 runs</span>
        </div>

        @if($logs->isEmpty())
            <div class="py-14 text-center text-gray-400 text-sm">No deployments yet. Click "Deploy Now" to start.</div>
        @else
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide border-b border-gray-100">
                    <th class="px-5 py-3 text-left font-semibold">Status</th>
                    <th class="px-5 py-3 text-left font-semibold">Started</th>
                    <th class="px-5 py-3 text-left font-semibold">Duration</th>
                    <th class="px-5 py-3 text-left font-semibold">By</th>
                    <th class="px-5 py-3 text-left font-semibold">Log</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($logs as $log)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        @if($log->status === 'success')
                            <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Success
                            </span>
                        @elseif($log->status === 'failed')
                            <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                Failed
                            </span>
                        @elseif($log->status === 'running')
                            <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                                Running
                            </span>
                        @else
                            <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-2.5 py-1 rounded-full">{{ ucfirst($log->status) }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-600">{{ $log->started_at?->format('d M Y, H:i:s') ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $log->durationSeconds() !== null ? $log->durationSeconds() . 's' : '—' }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ optional($log->user)->email ?? 'Unknown' }}</td>
                    <td class="px-5 py-3">
                        @if($log->output)
                            <button type="button" onclick="toggleLog({{ $log->id }})" class="text-indigo-600 hover:underline text-xs font-medium">View</button>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                </tr>
                @if($log->output)
                <tr id="log-{{ $log->id }}" style="display:none;">
                    <td colspan="5" class="px-5 pb-4">
                        <pre style="background:#0f172a; color:#e2e8f0; padding:1rem; font-size:0.7rem; font-family:monospace; border-radius:0.5rem; max-height:280px; overflow-y:auto; white-space:pre-wrap; line-height:1.5; margin:0;">{{ $log->output }}</pre>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const DEPLOY_URL  = "{{ route('admin.deploy.run') }}";
    const CSRF        = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const btn          = document.getElementById('deploy-btn');
    const btnLabel     = document.getElementById('btn-label');
    const btnIcon      = document.getElementById('btn-icon');
    const outputPanel  = document.getElementById('output-panel');
    const outputBody   = document.getElementById('output-body');
    const statusDot    = document.getElementById('status-dot');
    const statusBadge  = document.getElementById('status-badge');
    const durationText = document.getElementById('duration-text');

    function setLoading(on) {
        btn.disabled = on;
        btn.style.opacity = on ? '0.7' : '1';
        btnLabel.textContent = on ? 'Deploying… please wait' : '🚀 Deploy Now';
    }

    function showOutput(text) {
        outputPanel.style.display = 'block';
        outputBody.textContent = text;
        outputBody.scrollTop = outputBody.scrollHeight;
    }

    function setStatus(status, seconds) {
        if (status === 'success') {
            statusDot.style.background   = '#22c55e';
            statusBadge.textContent      = '✓ Success';
            statusBadge.style.background = '#dcfce7';
            statusBadge.style.color      = '#15803d';
        } else if (status === 'failed') {
            statusDot.style.background   = '#ef4444';
            statusBadge.textContent      = '✗ Failed';
            statusBadge.style.background = '#fee2e2';
            statusBadge.style.color      = '#b91c1c';
        } else {
            statusDot.style.background   = '#f59e0b';
            statusBadge.textContent      = 'Running…';
            statusBadge.style.background = '#fef3c7';
            statusBadge.style.color      = '#92400e';
        }
        if (seconds != null) durationText.textContent = seconds + 's';
    }

    btn.addEventListener('click', async function () {
        if (btn.disabled) return;

        if (!confirm('This will deploy to the production server. Continue?')) return;

        setLoading(true);
        showOutput('Connecting to SSH server…\n');
        setStatus('running', null);

        try {
            const res  = await fetch(DEPLOY_URL, {
                method:  'POST',
                headers: {
                    'X-CSRF-TOKEN':     CSRF,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept':           'application/json',
                },
            });

            const data = await res.json();

            if (res.status === 409) {
                showOutput('⚠ ' + data.error);
                setStatus('failed', null);
                setLoading(false);
                return;
            }

            showOutput(data.output || '(no output)');
            setStatus(data.status, data.duration);
            setLoading(false);

            setTimeout(function () { window.location.reload(); }, 2500);

        } catch (err) {
            showOutput('Connection error: ' + err.message);
            setStatus('failed', null);
            setLoading(false);
        }
    });

    window.toggleLog = function (id) {
        const row = document.getElementById('log-' + id);
        if (row) row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
    };
})();
</script>
@endpush
