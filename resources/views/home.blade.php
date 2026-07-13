<x-layouts.app title="VERTEX — Home">
    <div x-data="{
        activeFileTab: 'bio',
        terminalCommand: '',
        terminalHistory: [{ cmd: 'whoami', output: ['systems_architect.bin', 'Location: Remote/Global', 'Specialization: Multi-region Kubernetes, sub-15ms edge routing, state replication.'] }],
        isRunningScript: false,
        testRunSuccess: null,
        commandMap: {
            help: ['Available commands:', '  whoami         - View system architect biography', '  neofetch       - Display tech hardware & OS info', '  skills         - List architectural proficiencies', '  clear          - Clear terminal interface', '  terraform plan - View current cloud state simulation'],
            whoami: ['systems_architect.bin', 'Name: VERTEX', 'Focus: Robust, high-concurrency cloud environments & edge topologies.', 'Core Mission: Designing bulletproof backends with millisecond-level precision.'],
            neofetch: ['VERTEX@CoreSystemOS', '-------------------------', 'OS: Kubernetes v1.29 / Alpine Linux', 'Kernel: Custom-RT-Scheduler-6.1', 'Uptime: 99.999% continuous', 'Shell: zsh 5.9', 'Resolution: Fluid Grid Dynamic', 'IDE: Vim / VS Code CJS Mode', 'CPU: Multi-Region Distributed Threads', 'Memory: Cache Optimized Edge Layers'],
            skills: ['ARCHITECTURAL COMPONENT MATRIX:', '  [Cloud Providers]  AWS (EKS, Aurora), GCP (GKE, Cloud SQL), Cloudflare', '  [Container/Mesh]  Kubernetes, Docker, Linkerd, Istio, Consul', '  [Data Replication] Redis Cluster, Postgres (Drizzle, PGPool-II), Cassandra', '  [Performance Engine] Rust, Go, Node.js (Express), Bun, Edge Workers'],
            'terraform plan': ['Terraform v1.5.0', 'Refreshing Terraform state in-memory...', 'No changes. Infrastructure is perfectly in sync with state config.', 'Active resources:', '  + aws_eks_cluster.core_prod (Healthy, 3 Nodes)', '  + google_compute_global_forwarding_rule.cdn_gateway (0.0.0.0/0)', '  + postgresql_replication_group.primary_sync (Replica Lag: 2.1ms)']
        },
        submitCommand(e) {
            e.preventDefault();
            const cmdClean = this.terminalCommand.trim().toLowerCase();
            if (!cmdClean) return;
            if (cmdClean === 'clear') { this.terminalHistory = []; this.terminalCommand = ''; return; }
            const output = this.commandMap[cmdClean] || [`Command not found: \"${this.terminalCommand}\".`, 'Type \"help\" to list available systems protocols.'];
            this.terminalHistory.push({ cmd: this.terminalCommand, output });
            this.terminalCommand = '';
        },
        runSystemSelfTest() {
            if (this.isRunningScript) return;
            this.isRunningScript = true;
            this.testRunSuccess = null;
            setTimeout(() => { this.isRunningScript = false; this.testRunSuccess = true; }, 2000);
        }
    }" class="space-y-16">

        {{-- Hero --}}
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-6 space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 border border-white/10 rounded-full font-mono text-xs text-white/95 backdrop-blur-md">
                    <svg class="w-3.5 h-3.5 animate-bounce text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>LATENCY CRITICAL // SECURE DIRECTORIES</span>
                </div>

                <h1 class="font-sans text-4xl md:text-6xl font-bold tracking-tight text-white leading-none">
                    We Design <span class="text-indigo-400">Bulletproof</span> Cloud Topology.
                </h1>

                <p class="font-sans text-base md:text-lg text-white/70 leading-relaxed max-w-xl">
                    A boutique consulting agency specialized in multi-region deployments, Kubernetes clusters, database replication, and millisecond-level code optimization. We build platforms that don't fall down.
                </p>

                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="{{ route('contact.index') }}" class="bg-indigo-500/80 hover:bg-indigo-500 text-white font-sans text-sm px-6 py-3.5 rounded-lg font-semibold active:scale-[0.98] transition-all flex items-center gap-2 shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/30">
                        <span>Initialize Deployment</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </a>
                    <a href="{{ route('projects') }}" class="border border-white/10 text-white hover:text-indigo-300 hover:bg-white/10 font-mono text-sm px-6 py-3.5 rounded-lg transition-all bg-white/5 backdrop-blur-md">
                        &gt; View Schematic Logs
                    </a>
                </div>
            </div>

            {{-- Mock terminal --}}
            <div class="lg:col-span-6">
                <div class="glass-card rounded-2xl overflow-hidden shadow-2xl relative">
                    <div class="bg-white/5 p-4 flex justify-between items-center border-b border-white/10 select-none">
                        <div class="flex gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-red-500/40 border border-red-500/20"></span>
                            <span class="w-3 h-3 rounded-full bg-yellow-500/40 border border-yellow-500/20"></span>
                            <span class="w-3 h-3 rounded-full bg-green-500/40 border border-green-500/20"></span>
                        </div>
                        <div class="font-mono text-[11px] text-white/50 flex items-center gap-2">
                            <svg class="w-3 h-3 text-indigo-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>active_node_terminal.zsh</span>
                        </div>
                    </div>
                    <div class="p-5 font-mono text-xs space-y-4 h-80 overflow-y-auto bg-slate-950/45 text-white/90">
                        <p class="text-white/40 italic">// Welcome to DEV_ARCHITECT cloud portal.</p>
                        <p class="text-white/40 italic">// Type "help" to show available commands or explore systems metrics.</p>
                        <template x-for="(item, idx) in terminalHistory" :key="idx">
                            <div class="space-y-1">
                                <p class="text-indigo-400 font-bold" x-text="'&gt; ' + item.cmd"></p>
                                <template x-for="(line, oIdx) in item.output" :key="oIdx">
                                    <p class="text-white/70 leading-relaxed whitespace-pre-wrap" x-text="line"></p>
                                </template>
                            </div>
                        </template>
                        <form @submit="submitCommand($event)" class="flex items-center gap-2 pt-2 border-t border-white/10">
                            <span class="text-indigo-400 font-bold select-none">&gt;</span>
                            <input type="text" x-model="terminalCommand" placeholder="Type system commands here..." class="w-full bg-transparent border-none text-indigo-300 focus:outline-none placeholder:text-white/20 font-mono text-xs"/>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        {{-- Stats grid --}}
        <section class="space-y-6 mb-2">
            <div class="space-y-2">
                <h2 class="font-mono text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                    <span>SYSTEMS_EFFICIENCY_METRICS</span>
                </h2>
                <p class="font-sans text-xs text-white/50">Live benchmarks compiled from cloud service providers</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="glass-card relative overflow-hidden p-6 flex flex-col justify-between hover:bg-white/15 hover:border-white/20 transition-all duration-300 rounded-2xl shadow-lg">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-500/10 rounded-full -mr-8 -mt-8"></div>
                    <div class="flex justify-between items-start mb-4"><span class="font-mono text-xs text-white/60">Uptime SLA</span></div>
                    <div><p class="font-mono text-3xl font-extrabold text-white">99.999%</p><p class="font-sans text-xs text-white/40 mt-1">&lt; 5 minutes downtime/year</p></div>
                </div>
                <div class="glass-card relative overflow-hidden p-6 flex flex-col justify-between hover:bg-white/15 hover:border-white/20 transition-all duration-300 rounded-2xl shadow-lg">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-purple-500/10 rounded-full -mr-8 -mt-8"></div>
                    <div class="flex justify-between items-start mb-4"><span class="font-mono text-xs text-white/60">Avg Latency</span></div>
                    <div><p class="font-mono text-3xl font-extrabold text-white">&lt; 12.4ms</p><p class="font-sans text-xs text-white/40 mt-1">Global edge routing median</p></div>
                </div>
                <div class="glass-card relative overflow-hidden p-6 flex flex-col justify-between hover:bg-white/15 hover:border-white/20 transition-all duration-300 rounded-2xl shadow-lg">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-pink-500/10 rounded-full -mr-8 -mt-8"></div>
                    <div class="flex justify-between items-start mb-4"><span class="font-mono text-xs text-white/60">Active GKE Nodes</span></div>
                    <div><p class="font-mono text-3xl font-extrabold text-white">240+</p><p class="font-sans text-xs text-white/40 mt-1">Multi-cloud cluster points</p></div>
                </div>
                <div class="glass-card relative overflow-hidden p-6 flex flex-col justify-between hover:bg-white/15 hover:border-white/20 transition-all duration-300 rounded-2xl shadow-lg">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-teal-500/10 rounded-full -mr-8 -mt-8"></div>
                    <div class="flex justify-between items-start mb-4"><span class="font-mono text-xs text-white/60">Security Rating</span></div>
                    <div><p class="font-mono text-3xl font-extrabold text-white">A+ Tier</p><p class="font-sans text-xs text-white/40 mt-1">SOC-2 Type II standards</p></div>
                </div>
            </div>
        </section>

        {{-- Code editor tabs --}}
        <section class="glass-card rounded-2xl overflow-hidden shadow-xl">
            <div class="bg-white/5 border-b border-white/10 flex items-center px-4 overflow-x-auto scrollbar-none">
                @foreach (['bio' => 'architecture_bio.md', 'stack' => 'technologies_stack.json', 'services' => 'service_blueprints.yaml'] as $tab => $label)
                    <button
                        @click="activeFileTab = '{{ $tab }}'"
                        type="button"
                        :class="activeFileTab === '{{ $tab }}' ? 'border-indigo-400 text-white font-bold bg-white/5' : 'border-transparent text-white/50 hover:text-white'"
                        class="py-3.5 px-5 font-mono text-xs flex items-center gap-2 border-b-2 transition-all cursor-pointer"
                    >
                        <span>{{ $label }}</span>
                    </button>
                @endforeach
            </div>
            <div class="p-6 md:p-8 font-mono text-xs bg-slate-950/25 backdrop-blur-md leading-relaxed min-h-60 select-text">
                <div x-show="activeFileTab === 'bio'" class="space-y-4">
                    <p class="text-white/40"># CORE_SYSTEMS_ARCHITECT_STATEMENT</p>
                    <p class="text-white/80 font-sans text-sm leading-relaxed max-w-3xl">We believe that backend stability is not an afterthought—it is the foundation of product trust. Our team operates at the intersection of robust systems design and clean codebase execution.</p>
                    <p class="text-white/40 mt-4">## PRIMARY_GOALS</p>
                    <ul class="list-disc pl-5 font-sans text-sm text-white/70 space-y-2">
                        <li><strong>Zero Single Point of Failure (SPOF):</strong> Designing load balancers, database clustering, and cross-region fallbacks.</li>
                        <li><strong>Sub-Millisecond Engine Routing:</strong> Performance profiling, aggressive Redis caching, and optimized SQL index matrices.</li>
                        <li><strong>Systemic Visibility:</strong> Standardized log parsing, metrics aggregation dashboards, and automated failover telemetry alerts.</li>
                    </ul>
                </div>
                <div x-show="activeFileTab === 'stack'" x-cloak class="space-y-2">
                    <p class="text-indigo-300">{</p>
                    <div class="pl-6 space-y-1">
                        <p><span class="text-indigo-400">"cloud_infrastructure"</span>: <span class="text-emerald-300">["Amazon Web Services", "Google Cloud", "Cloudflare Edge"]</span>,</p>
                        <p><span class="text-indigo-400">"containerization_orchestration"</span>: <span class="text-emerald-300">["Kubernetes", "Docker Engine", "Helm"]</span>,</p>
                        <p><span class="text-indigo-400">"database_replication"</span>: <span class="text-emerald-300">["PostgreSQL (Active-Replica)", "Redis (Cluster)", "MongoDB"]</span>,</p>
                        <p><span class="text-indigo-400">"runtime_environments"</span>: <span class="text-emerald-300">["TypeScript (Node.js/Bun)", "Go", "Rust (WASM)"]</span>,</p>
                        <p><span class="text-indigo-400">"security_standards"</span>: <span class="text-emerald-300">["TLS 1.3", "RSA-4096 Encrypted Keys", "OIDC Single Sign-On"]</span></p>
                    </div>
                    <p class="text-indigo-300">}</p>
                </div>
                <div x-show="activeFileTab === 'services'" x-cloak class="space-y-1">
                    <p class="text-white/40">---</p>
                    <p><span class="text-indigo-400">blueprints_catalog</span>:</p>
                    <div class="pl-4 space-y-3 mt-2">
                        @foreach ([
                            ['id' => 'audit_and_remediations', 'focus' => 'Deep profile optimization, database query audit, query optimization, security vulnerability scans.'],
                            ['id' => 'cloud_replatforming', 'focus' => 'Kubernetes orchestration setup, infrastructure as code templates (Terraform), secure cloud migrations.'],
                            ['id' => 'performance_contracting', 'focus' => 'Scale architectures to support 50k+ active concurrent connections without lag or load failures.'],
                        ] as $svc)
                            <div>
                                <p><span class="text-purple-300">- service_id</span>: "{{ $svc['id'] }}"</p>
                                <p class="pl-4 text-white/70"><span class="text-white/40">focus</span>: "{{ $svc['focus'] }}"</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- Self test --}}
        <section class="glass-card p-6 rounded-2xl flex flex-col md:flex-row justify-between items-center gap-6 shadow-xl relative overflow-hidden mt-2">
            <div class="absolute top-0 left-0 w-32 h-32 bg-indigo-500/5 rounded-full -ml-12 -mt-12"></div>
            <div class="flex items-start gap-4 relative z-10">
                <div class="p-3 bg-white/5 border border-white/10 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                </div>
                <div class="space-y-1">
                    <h4 class="font-mono text-sm font-bold text-white">SYS_DIAGNOSTICS: LIVE CHECKUP</h4>
                    <p class="font-sans text-xs text-white/50">Verify route responsiveness and security cache status in real-time.</p>
                </div>
            </div>
            <div class="flex items-center gap-4 relative z-10">
                <span x-show="isRunningScript" class="font-mono text-xs text-indigo-400 flex items-center gap-2 animate-pulse">
                    <span class="w-2 h-2 bg-indigo-400 rounded-full animate-ping"></span>
                    <span>TEST_RUNNING...</span>
                </span>
                <span x-show="!isRunningScript && testRunSuccess" x-cloak class="font-mono text-xs text-emerald-400 flex items-center gap-1.5 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1.5 rounded-lg">
                    DIAGNOSTICS_OK (0ms lag)
                </span>
                <span x-show="!isRunningScript && testRunSuccess === null" class="font-mono text-xs text-white/40">Ready to run self test</span>
                <button @click="runSystemSelfTest()" :disabled="isRunningScript" type="button" class="flex items-center gap-2 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 font-mono text-xs px-4 py-2.5 rounded-lg transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-3.5 h-3.5 text-indigo-300" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    <span>RUN_SELF_TEST</span>
                </button>
            </div>
        </section>
    </div>
</x-layouts.app>
