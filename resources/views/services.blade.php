@php
$services = [
    [
        'id' => 'arch',
        'name' => 'Cloud Topology & Orchestration',
        'icon' => 'server',
        'tagline' => 'Scale-to-Infinity Architecture',
        'description' => 'Designing Kubernetes, AWS, and GCP infrastructures utilizing Infrastructure as Code (Terraform) with high-availability clustering and robust mesh configurations.',
        'basePrice' => 2500,
        'deliveryTime' => '2-3 Weeks',
        'features' => ['Complete Terraform configuration plans', 'Multi-region load balancing setup', 'Prometheus & Grafana dashboard telemetry', 'Fault tolerance audits'],
    ],
    [
        'id' => 'audit',
        'name' => 'Deep System & Database Audit',
        'icon' => 'shield',
        'tagline' => 'Security & Latency Remediations',
        'description' => 'Profiling existing applications to pinpoint database query deadlocks, API bottle-necks, and potential SOC-2/HIPAA security compliance loopholes.',
        'basePrice' => 1500,
        'deliveryTime' => '1-2 Weeks',
        'features' => ['Query optimization profiles', 'Memory leak & heap dump scans', 'Automated vulnerability diagnostics', 'Comprehensive remediation report'],
    ],
    [
        'id' => 'perf',
        'name' => 'High-Concurrency Development',
        'icon' => 'zap',
        'tagline' => 'High-Performance API Engines',
        'description' => 'Writing custom edge proxies, state replication drivers, and high-speed API routes in optimized TypeScript/Node.js, Go, or compiled Rust WASM.',
        'basePrice' => 3500,
        'deliveryTime' => '3-4 Weeks',
        'features' => ['Sub-15ms edge API routes', 'Redis cluster configuration and caching', 'gRPC communication buffers', 'Fully load-tested mock benchmark endpoints'],
    ],
];
@endphp

<x-layouts.app title="VERTEX — Services">
    <div
        x-data="{
            selectedServices: ['arch'],
            trafficScale: 100000,
            needsMultiRegion: false,
            needsHighSecurity: false,
            services: {{ Js::from($services) }},
            toggleService(id) {
                if (this.selectedServices.includes(id)) {
                    this.selectedServices = this.selectedServices.filter(s => s !== id);
                } else {
                    this.selectedServices.push(id);
                }
            },
            get estimatedBudget() {
                let total = 0;
                this.services.forEach(s => {
                    if (this.selectedServices.includes(s.id)) total += s.basePrice;
                });
                if (this.trafficScale > 500000) total += 1200;
                else if (this.trafficScale > 100000) total += 500;
                if (this.needsMultiRegion) total += 1500;
                if (this.needsHighSecurity) total += 1000;
                return total;
            }
        }"
        class="space-y-12"
    >
        <div class="space-y-3">
            <h1 class="font-sans text-3xl font-bold text-white flex items-center gap-3">
                <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                <span>BLUEPRINT_CAPABILITIES</span>
            </h1>
            <p class="font-sans text-white/70 max-w-xl text-sm leading-relaxed">
                Premium systems engineering and technical advisory contracts designed for enterprise performance standards.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <template x-for="service in services" :key="service.id">
                <div
                    @click="toggleService(service.id)"
                    :class="selectedServices.includes(service.id) ? 'bg-indigo-500/10 border-indigo-400 shadow-lg shadow-indigo-500/10 backdrop-blur-md' : 'glass-card border-white/10 hover:border-indigo-400/40 hover:bg-white/10'"
                    class="p-6 rounded-2xl border transition-all duration-300 flex flex-col justify-between cursor-pointer group"
                >
                    <div class="space-y-4">
                        <div class="flex justify-between items-start">
                            <div class="p-2.5 bg-white/5 border border-white/10 rounded-xl">
                                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                            </div>
                            <div :class="selectedServices.includes(service.id) ? 'bg-indigo-500 border-indigo-400' : 'border-white/20'" class="w-5 h-5 rounded border flex items-center justify-center transition-all">
                                <svg x-show="selectedServices.includes(service.id)" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <span class="font-mono text-[9px] text-indigo-300 block tracking-widest uppercase" x-text="service.tagline"></span>
                            <h3 class="font-sans text-base font-bold text-white group-hover:text-indigo-300 transition-colors" x-text="service.name"></h3>
                            <p class="font-sans text-xs text-white/60 leading-relaxed" x-text="service.description"></p>
                        </div>
                        <div class="pt-4 border-t border-white/10 space-y-2">
                            <template x-for="feat in service.features" :key="feat">
                                <div class="flex items-center gap-2 font-sans text-xs text-white/70">
                                    <svg class="w-3.5 h-3.5 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span x-text="feat"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="pt-6 mt-6 border-t border-white/10 flex justify-between items-center font-mono">
                        <div>
                            <span class="text-[9px] text-white/40 block uppercase">Est. Delivery</span>
                            <span class="text-xs text-white/80" x-text="service.deliveryTime"></span>
                        </div>
                        <div class="text-right">
                            <span class="text-[9px] text-white/40 block uppercase">From</span>
                            <span class="text-sm font-bold text-indigo-400" x-text="'$' + service.basePrice"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <section class="glass-card-heavy border border-white/15 rounded-2xl overflow-hidden p-6 md:p-10 space-y-8 shadow-2xl">
            <div class="space-y-2 border-b border-white/10 pb-4">
                <h2 class="font-sans text-xl font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>INTERACTIVE_SYSTEMS_PLANNER</span>
                </h2>
                <p class="font-sans text-xs text-white/50">Customize your resource constraints and compute an estimated budget live.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <div class="lg:col-span-7 space-y-6">
                    <div class="space-y-3">
                        <div class="flex justify-between font-mono text-xs">
                            <span class="text-white/60">EXPECTED_TRAFFIC_VOLUME:</span>
                            <span class="text-indigo-300 font-bold" x-text="trafficScale.toLocaleString() + ' req/day'"></span>
                        </div>
                        <input type="range" min="10000" max="1000000" step="10000" x-model.number="trafficScale" class="w-full accent-indigo-400 bg-slate-950/40 border border-white/10 h-2 rounded-lg cursor-pointer"/>
                        <div class="flex justify-between font-mono text-[9px] text-white/40 select-none">
                            <span>10K REQ (LOW)</span>
                            <span>500K REQ (MEDIUM)</span>
                            <span>1M REQ (HIGH SCALE)</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div @click="needsMultiRegion = !needsMultiRegion" :class="needsMultiRegion ? 'bg-indigo-500/10 border-indigo-400' : 'bg-slate-950/20 border-white/10 hover:border-white/20'" class="p-4 border rounded-xl cursor-pointer transition-all flex items-center justify-between">
                            <div class="space-y-1">
                                <span class="font-sans text-xs font-bold text-white block">Multi-Region Router</span>
                                <span class="font-sans text-[11px] text-white/55 block">Cross-region fallback (+$1,500)</span>
                            </div>
                            <div :class="needsMultiRegion ? 'bg-indigo-500 border-indigo-400' : 'border-white/20'" class="w-4 h-4 rounded border flex items-center justify-center">
                                <svg x-show="needsMultiRegion" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <div @click="needsHighSecurity = !needsHighSecurity" :class="needsHighSecurity ? 'bg-indigo-500/10 border-indigo-400' : 'bg-slate-950/20 border-white/10 hover:border-white/20'" class="p-4 border rounded-xl cursor-pointer transition-all flex items-center justify-between">
                            <div class="space-y-1">
                                <span class="font-sans text-xs font-bold text-white block">SOC-2 Security Pack</span>
                                <span class="font-sans text-[11px] text-white/55 block">OIDC Auth & audits (+$1,000)</span>
                            </div>
                            <div :class="needsHighSecurity ? 'bg-indigo-500 border-indigo-400' : 'border-white/20'" class="w-4 h-4 rounded border flex items-center justify-center">
                                <svg x-show="needsHighSecurity" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 bg-slate-950/40 border border-white/15 rounded-2xl p-6 space-y-6 relative overflow-hidden backdrop-blur-md shadow-inner">
                    <div class="absolute top-3 right-3 flex items-center gap-1.5 bg-indigo-500/10 border border-indigo-500/20 px-2 py-0.5 rounded-md text-[9px] text-indigo-400 font-mono">
                        <span>SYS_ESTIMATE</span>
                    </div>
                    <div class="space-y-1">
                        <span class="font-mono text-[10px] text-white/40 uppercase block tracking-wider">Estimated Project Budget</span>
                        <p class="font-mono text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 to-white flex items-baseline">
                            <span x-text="'$' + estimatedBudget.toLocaleString()"></span>
                            <span class="text-xs text-white/50 font-normal tracking-normal font-sans ml-1">USD (Contract Fee)</span>
                        </p>
                    </div>
                    <div class="p-4 bg-slate-950/40 border border-white/10 rounded-xl font-mono text-[11px] text-white/70 space-y-2">
                        <p class="text-white/40 italic select-none">// Active Cost Logs:</p>
                        <template x-for="service in services" :key="service.id">
                            <p x-show="selectedServices.includes(service.id)" class="flex justify-between text-emerald-400">
                                <span x-text="'+ ' + service.name.substring(0, 18) + '...'"></span>
                                <span x-text="'$' + service.basePrice"></span>
                            </p>
                        </template>
                        <p x-show="trafficScale > 100000" class="flex justify-between text-indigo-200">
                            <span>+ High-Scale Ingestion Config</span>
                            <span x-text="trafficScale > 500000 ? '$1,200' : '$500'"></span>
                        </p>
                        <p x-show="needsMultiRegion" class="flex justify-between text-indigo-200">
                            <span>+ Multi-Region Active Fallback</span>
                            <span>$1,500</span>
                        </p>
                        <p x-show="needsHighSecurity" class="flex justify-between text-indigo-200">
                            <span>+ Security Hardening Suite</span>
                            <span>$1,000</span>
                        </p>
                        <p x-show="selectedServices.length === 0" class="text-red-400 italic">// Choose at least one core service blueprint.</p>
                    </div>
                    <p class="font-sans text-[11px] text-white/45 italic">
                        * Estimates are calculated on average system size parameters. Standard SLAs, cloud costs, and hosting credits are handled separately.
                    </p>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
