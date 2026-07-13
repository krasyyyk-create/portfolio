@php
$projects = [
    [
        'id' => 'cluster-prod',
        'title' => 'High-Availability EKS Cluster',
        'category' => 'infrastructure',
        'description' => 'Designed and implemented a multi-region active-active Kubernetes infrastructure running on AWS, backed by Istio service mesh and fully automated using Terraform IaC.',
        'techStack' => ['AWS EKS', 'Kubernetes', 'Istio Mesh', 'Terraform', 'Linkerd'],
        'metrics' => ['uptime' => '99.999%', 'scale' => '50-120 Auto Nodes', 'throughput' => '150,000 req/sec'],
        'details' => [
            'Eliminated all single points of failure with cross-region route configurations.',
            'Structured automated cluster autoscaling using custom metrics via Prometheus adapters.',
            'Hardened network safety profiles using strict Calico policies and namespace segmentations.',
        ],
        'visualType' => 'kube',
    ],
    [
        'id' => 'db-sync',
        'title' => 'Cross-Cloud Database Replication',
        'category' => 'database',
        'description' => 'Architected a multi-cloud database pipeline bridging GCP Cloud SQL and a secondary AWS RDS Postgres cluster with near-zero replication lag for disaster recovery.',
        'techStack' => ['PostgreSQL', 'GCP Cloud SQL', 'AWS RDS', 'pgPool-II', 'CDC Pipelines'],
        'metrics' => ['latency' => '< 2.4ms lag', 'scale' => '3 Terabytes', 'throughput' => '24,000 read-ops'],
        'details' => [
            'Developed custom pgPool-II middleware to route read-heavy transactions safely.',
            'Established automatic failover routines resolving connectivity drops in less than 4 seconds.',
            'Constructed schema migration automation leveraging robust Drizzle / Prisma sync protocols.',
        ],
        'visualType' => 'db',
    ],
    [
        'id' => 'cdn-routing',
        'title' => 'Global Edge Accelerator',
        'category' => 'api-engine',
        'description' => 'Implemented a globally distributed edge caching proxy utilizing Cloudflare Workers to intercept, authenticate, and sanitize inbound REST/GraphQL requests.',
        'techStack' => ['Cloudflare Workers', 'Rust / WASM', 'Redis Edge', 'TLS 1.3', 'GraphQL'],
        'metrics' => ['latency' => '8.2ms avg', 'throughput' => '1.4M peak-req', 'uptime' => '100% Core edge'],
        'details' => [
            'Wrote custom edge proxy filters compiled in high-performance Rust WASM.',
            'Built geographically routed Redis cache buffers minimizing roundtrip db trips.',
            'Integrated live DDoS mitigation rules blocking active threat payloads in milliseconds.',
        ],
        'visualType' => 'cdn',
    ],
    [
        'id' => 'pipeline-data',
        'title' => 'Real-Time Streaming Bus',
        'category' => 'infrastructure',
        'description' => 'Constructed an event-driven data streaming mesh using Apache Kafka and Apache Flink to process log aggregates and generate real-time metrics telemetry.',
        'techStack' => ['Apache Kafka', 'Apache Flink', 'Go / Golang', 'gRPC', 'Prometheus'],
        'metrics' => ['throughput' => '12 GB/min', 'latency' => '15ms processing', 'scale' => '24 Cluster Partitions'],
        'details' => [
            'Assembled highly optimized Golang partition workers to balance stream processing.',
            'Programmed real-time anomalies logging and telemetry alerts directly to Grafana dashboard.',
            'Reduced partition imbalance by implementing high-trust MurmurHash routing.',
        ],
        'visualType' => 'pipeline',
    ],
];
@endphp

<x-layouts.app title="VERTEX — Projects">
    <div
        x-data="{
            filter: 'all',
            selectedProject: null,
            projects: {{ Js::from($projects) }},
            get filteredProjects() {
                return this.filter === 'all'
                    ? this.projects
                    : this.projects.filter(p => p.category === this.filter);
            }
        }"
        class="space-y-10"
    >
        <div class="space-y-3">
            <h1 class="font-sans text-3xl font-bold text-white flex items-center gap-2.5">
                <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>PORTFOLIO_SCHEMATICS</span>
            </h1>
            <p class="font-sans text-white/70 max-w-xl text-sm leading-relaxed">
                Deep-dives into production architectures we have successfully designed, validated, and currently support across major cloud service providers.
            </p>
        </div>

        <div class="flex border-b border-white/10 pb-2 overflow-x-auto scrollbar-none gap-2">
            @foreach (['all', 'infrastructure', 'database', 'api-engine'] as $cat)
                <button
                    @click="filter = '{{ $cat }}'"
                    type="button"
                    :class="filter === '{{ $cat }}' ? 'bg-white/10 border border-white/25 text-white font-bold backdrop-blur-md' : 'border border-white/5 bg-white/5 text-white/50 hover:text-white backdrop-blur-sm'"
                    class="px-4 py-2 font-mono text-xs capitalize transition-all rounded-lg cursor-pointer"
                >
                    {{ str_replace('-', ' ', $cat) }}
                </button>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <template x-for="project in filteredProjects" :key="project.id">
                <div class="glass-card hover:bg-white/15 hover:border-indigo-400/30 rounded-2xl overflow-hidden flex flex-col justify-between transition-all duration-300 group hover:-translate-y-1 relative shadow-xl">
                    <div class="bg-white/5 p-3.5 flex justify-between items-center border-b border-white/10 select-none">
                        <div class="flex gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-white/20"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-white/20"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-white/20"></span>
                        </div>
                        <span class="font-mono text-[10px] text-white/40" x-text="project.id + '.yaml'"></span>
                    </div>
                    <div class="p-6 space-y-6 flex-grow">
                        <div class="space-y-2">
                            <div class="flex justify-between items-start gap-4">
                                <h3 class="font-sans text-lg font-bold text-white group-hover:text-indigo-400 transition-colors" x-text="project.title"></h3>
                                <span class="font-mono text-[10px] text-purple-300 bg-purple-500/10 border border-purple-500/20 px-2.5 py-0.5 rounded-full uppercase tracking-wider" x-text="project.category"></span>
                            </div>
                            <p class="font-sans text-sm text-white/70 leading-relaxed" x-text="project.description"></p>
                        </div>
                        <div class="grid grid-cols-3 gap-2 py-3 border-y border-white/10 text-center bg-slate-950/25 rounded-lg p-2.5">
                            <template x-for="(val, key) in project.metrics" :key="key">
                                <div class="space-y-1 border-r last:border-r-0 border-white/5">
                                    <span class="font-mono text-[9px] text-white/40 uppercase block tracking-wider" x-text="key"></span>
                                    <span class="font-mono text-xs font-bold text-indigo-400" x-text="val"></span>
                                </div>
                            </template>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="tech in project.techStack" :key="tech">
                                <span class="font-mono text-[10px] text-white/80 bg-white/5 px-2.5 py-1 rounded-md border border-white/10" x-text="tech"></span>
                            </template>
                        </div>
                    </div>
                    <div class="p-4 bg-white/5 border-t border-white/10 flex justify-between items-center">
                        <button @click="selectedProject = project" type="button" class="font-mono text-xs text-indigo-300 hover:text-white flex items-center gap-1 cursor-pointer">
                            <span>&gt; Inspect Architecture</span>
                            <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <div class="flex gap-3 text-white/40 hover:text-white">
                            <a href="#github"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.515.12-3.15 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.635.24 2.85.12 3.15.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg></a>
                            <a href="#globe"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg></a>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Modal --}}
        <div x-show="selectedProject" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @click="selectedProject = null" class="absolute inset-0 bg-slate-950/65 backdrop-blur-md"></div>
            <div class="relative w-full max-w-2xl glass-card-heavy rounded-2xl overflow-hidden shadow-2xl flex flex-col max-h-[85vh] border border-white/15">
                <div class="bg-white/5 p-4 flex justify-between items-center border-b border-white/10 select-none">
                    <div class="flex gap-1.5">
                        <button @click="selectedProject = null" type="button" class="w-3 h-3 rounded-full bg-red-500/60 cursor-pointer hover:bg-red-500 transition-colors"></button>
                        <span class="w-3 h-3 rounded-full bg-yellow-500/30"></span>
                        <span class="w-3 h-3 rounded-full bg-green-500/30"></span>
                    </div>
                    <div class="font-mono text-xs text-white/50 flex items-center gap-1.5">
                        <span x-text="selectedProject ? 'inspect_' + selectedProject.id + '.sh' : ''"></span>
                    </div>
                </div>
                <div class="p-6 md:p-8 overflow-y-auto space-y-6" x-show="selectedProject">
                    <div>
                        <span class="font-mono text-[10px] text-indigo-400 uppercase tracking-widest block font-bold mb-1">Architecture Inspect</span>
                        <h2 class="font-sans text-2xl font-bold text-white" x-text="selectedProject?.title"></h2>
                        <p class="font-sans text-sm text-white/70 mt-2 leading-relaxed" x-text="selectedProject?.description"></p>
                    </div>
                    <div class="p-5 bg-slate-950/30 border border-white/10 rounded-xl font-mono text-xs space-y-4 relative overflow-hidden">
                        <div class="absolute top-2 right-2 flex items-center gap-1 bg-indigo-500/10 px-2 py-0.5 border border-indigo-500/20 rounded-md text-[9px] text-indigo-400">
                            <span class="w-1.5 h-1.5 bg-indigo-400 rounded-full animate-ping"></span>
                            <span>LIVE_FLOW</span>
                        </div>
                        <p class="text-white/40 italic">// Conceptual Node Topology Graph</p>
                        <template x-if="selectedProject?.visualType === 'kube'">
                            <div class="space-y-2 py-2 text-center text-white/80">
                                <div class="p-2 border border-indigo-400/30 bg-indigo-500/10 rounded-lg inline-block">CDN GATEWAY</div>
                                <div class="text-indigo-400/60">&darr; Routing Protocol</div>
                                <div class="p-2 border border-indigo-300/30 bg-indigo-300/5 rounded-lg inline-block">Ingress Controller (Istio)</div>
                                <div class="text-indigo-400/60">&darr; Pod Load Balance</div>
                                <div class="flex justify-center gap-2 flex-wrap">
                                    <div class="p-1.5 border border-emerald-500/30 bg-emerald-500/5 rounded-lg text-[10px]">App Pod A</div>
                                    <div class="p-1.5 border border-emerald-500/30 bg-emerald-500/5 rounded-lg text-[10px]">App Pod B</div>
                                    <div class="p-1.5 border border-emerald-500/30 bg-emerald-500/5 rounded-lg text-[10px]">App Pod C</div>
                                </div>
                            </div>
                        </template>
                        <template x-if="selectedProject?.visualType === 'db'">
                            <div class="space-y-2 py-2 text-center text-white/80">
                                <div class="p-2 border border-indigo-400/30 bg-indigo-500/10 rounded-lg inline-block">CLIENT APPLICATION</div>
                                <div class="text-indigo-400/60">&darr; Read / Write Router</div>
                                <div class="flex justify-center gap-6 flex-wrap">
                                    <div class="p-2 border border-emerald-500/30 bg-emerald-500/5 rounded-lg text-[10px]">Primary DB (AWS Postgres)<br><span class="text-emerald-400 font-bold">Write Node</span></div>
                                    <div class="p-2 border border-purple-500/30 bg-purple-500/5 rounded-lg text-[10px]">Replica DB (GCP SQL)<br><span class="text-purple-300 font-bold">Read / DR Pool</span></div>
                                </div>
                            </div>
                        </template>
                        <template x-if="selectedProject?.visualType === 'cdn'">
                            <div class="space-y-2 py-2 text-center text-white/80">
                                <div class="p-2 border border-indigo-300/30 bg-indigo-300/5 rounded-lg inline-block">GLOBAL EDGE CLIENTS</div>
                                <div class="text-indigo-400/60">&darr; Geographic DNS Routing</div>
                                <div class="flex justify-center gap-4 flex-wrap">
                                    <div class="p-2 border border-indigo-400/30 bg-indigo-500/10 rounded-lg text-[10px]">Edge Worker (US-East)</div>
                                    <div class="p-2 border border-indigo-400/30 bg-indigo-500/10 rounded-lg text-[10px]">Edge Worker (EU-West)</div>
                                </div>
                            </div>
                        </template>
                        <template x-if="selectedProject?.visualType === 'pipeline'">
                            <div class="space-y-2 py-2 text-center text-white/80">
                                <div class="p-2 border border-indigo-400/30 bg-indigo-500/10 rounded-lg inline-block">TELEMETRY INGESTION</div>
                                <div class="text-indigo-400/60">&darr; Streaming Bus</div>
                                <div class="p-2 border border-purple-500/30 bg-purple-500/5 rounded-lg inline-block text-[10px]">Apache Kafka Queue Cluster</div>
                            </div>
                        </template>
                    </div>
                    <div class="space-y-3">
                        <h4 class="font-sans text-sm font-bold text-white">SPECIFICATIONS & BENCHMARKS:</h4>
                        <ul class="space-y-2">
                            <template x-for="(detail, idx) in selectedProject?.details ?? []" :key="idx">
                                <li class="font-sans text-sm text-white/70 flex items-start gap-2.5">
                                    <span class="font-mono text-indigo-400 text-xs mt-0.5 select-none">&gt;</span>
                                    <span x-text="detail"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
                <div class="p-4 bg-white/5 border-t border-white/10 flex justify-between items-center">
                    <span class="font-mono text-xs text-white/40">Exit Code: 0 (No Errors)</span>
                    <button @click="selectedProject = null" type="button" class="px-5 py-2.5 bg-indigo-500/80 hover:bg-indigo-500 border border-white/10 text-white font-semibold font-sans text-xs rounded-lg active:scale-95 cursor-pointer transition-colors shadow-lg shadow-indigo-500/20">
                        Close Inspection
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
