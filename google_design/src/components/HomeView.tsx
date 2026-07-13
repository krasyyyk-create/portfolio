import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { Terminal, Database, Shield, Zap, Cpu, Server, Play, HelpCircle, FileText, Check, AlertCircle } from 'lucide-react';

interface HomeViewProps {
  setActiveScreen: (screen: 'home' | 'projects' | 'services' | 'contact') => void;
}

export default function HomeView({ setActiveScreen }: HomeViewProps) {
  const [activeFileTab, setActiveFileTab] = useState<'bio' | 'stack' | 'services'>('bio');
  const [terminalCommand, setTerminalCommand] = useState('');
  const [terminalHistory, setTerminalHistory] = useState<{ cmd: string; output: string[] }[]>([
    { cmd: 'whoami', output: ['systems_architect.bin', 'Location: Remote/Global', 'Specialization: Multi-region Kubernetes, sub-15ms edge routing, state replication.'] }
  ]);
  const [typedCommand, setTypedCommand] = useState('');
  const [isRunningScript, setIsRunningScript] = useState(false);
  const [testRunSuccess, setTestRunSuccess] = useState<boolean | null>(null);

  // Command logs mapping for the mock terminal
  const commandMap: Record<string, string[]> = {
    'help': [
      'Available commands:',
      '  whoami         - View system architect biography',
      '  neofetch       - Display tech hardware & OS info',
      '  skills         - List architectural proficiencies',
      '  clear          - Clear terminal interface',
      '  terraform plan - View current cloud state simulation'
    ],
    'whoami': [
      'systems_architect.bin',
      'Name: VERTEX',
      'Focus: Robust, high-concurrency cloud environments & edge topologies.',
      'Core Mission: Designing bulletproof backends with millisecond-level precision.'
    ],
    'neofetch': [
      'VERTEX@CoreSystemOS',
      '-------------------------',
      'OS: Kubernetes v1.29 / Alpine Linux',
      'Kernel: Custom-RT-Scheduler-6.1',
      'Uptime: 99.999% continuous',
      'Shell: zsh 5.9',
      'Resolution: Fluid Grid Dynamic',
      'IDE: Vim / VS Code CJS Mode',
      'CPU: Multi-Region Distributed Threads',
      'Memory: Cache Optimized Edge Layers'
    ],
    'skills': [
      'ARCHITECTURAL COMPONENT MATRIX:',
      '  [Cloud Providers]  AWS (EKS, Aurora), GCP (GKE, Cloud SQL), Cloudflare',
      '  [Container/Mesh]  Kubernetes, Docker, Linkerd, Istio, Consul',
      '  [Data Replication] Redis Cluster, Postgres (Drizzle, PGPool-II), Cassandra',
      '  [Performance Engine] Rust, Go, Node.js (Express), Bun, Edge Workers'
    ],
    'terraform plan': [
      'Terraform v1.5.0',
      'Refreshing Terraform state in-memory...',
      'No changes. Infrastructure is perfectly in sync with state config.',
      'Active resources:',
      '  + aws_eks_cluster.core_prod (Healthy, 3 Nodes)',
      '  + google_compute_global_forwarding_rule.cdn_gateway (0.0.0.0/0)',
      '  + postgresql_replication_group.primary_sync (Replica Lag: 2.1ms)'
    ]
  };

  const handleCommandSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const cmdClean = terminalCommand.trim().toLowerCase();
    if (!cmdClean) return;

    if (cmdClean === 'clear') {
      setTerminalHistory([]);
      setTerminalCommand('');
      return;
    }

    const output = commandMap[cmdClean] || [
      `Command not found: "${terminalCommand}".`,
      'Type "help" to list available systems protocols.'
    ];

    setTerminalHistory(prev => [...prev, { cmd: terminalCommand, output }]);
    setTerminalCommand('');
  };

  const runSystemSelfTest = () => {
    if (isRunningScript) return;
    setIsRunningScript(true);
    setTestRunSuccess(null);

    // Simulate logs
    setTimeout(() => {
      setIsRunningScript(false);
      setTestRunSuccess(true);
    }, 2000);
  };

  return (
    <div className="space-y-16">
      {/* Hero Header Component */}
      <section className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <div className="lg:col-span-6 space-y-6">
          <div className="inline-flex items-center gap-2 px-3 py-1 bg-white/10 border border-white/10 rounded-full font-mono text-xs text-white/95 backdrop-blur-md">
            <Zap className="w-3.5 h-3.5 animate-bounce text-indigo-400" />
            <span>LATENCY CRITICAL // SECURE DIRECTORIES</span>
          </div>
          
          <h1 className="font-sans text-4xl md:text-6xl font-bold tracking-tight text-white leading-none">
            We Design <span className="text-indigo-400">Bulletproof</span> Cloud Topology.
          </h1>
          
          <p className="font-sans text-base md:text-lg text-white/70 leading-relaxed max-w-xl">
            A boutique consulting agency specialized in multi-region deployments, Kubernetes clusters, database replication, and millisecond-level code optimization. We build platforms that don't fall down.
          </p>

          <div className="flex flex-wrap gap-4 pt-2">
            <button
              onClick={() => setActiveScreen('contact')}
              className="bg-indigo-500/80 hover:bg-indigo-500 text-white font-sans text-sm px-6 py-3.5 rounded-lg font-semibold active:scale-[0.98] transition-all cursor-pointer flex items-center gap-2 shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/30"
            >
              <span>Initialize Deployment</span>
              <Terminal className="w-4 h-4" />
            </button>
            <button
              onClick={() => setActiveScreen('projects')}
              className="border border-white/10 text-white hover:text-indigo-300 hover:bg-white/10 font-mono text-sm px-6 py-3.5 rounded-lg transition-all cursor-pointer bg-white/5 backdrop-blur-md"
            >
              &gt; View Schematic Logs
            </button>
          </div>
        </div>

        {/* Interactive Mock terminal */}
        <div className="lg:col-span-6">
          <div className="glass-card rounded-2xl overflow-hidden shadow-2xl relative">
            {/* Header / Tabs */}
            <div className="bg-white/5 p-4 flex justify-between items-center border-b border-white/10 select-none">
              <div className="flex gap-1.5">
                <span className="w-3 h-3 rounded-full bg-red-500/40 border border-red-500/20"></span>
                <span className="w-3 h-3 rounded-full bg-yellow-500/40 border border-yellow-500/20"></span>
                <span className="w-3 h-3 rounded-full bg-green-500/40 border border-green-500/20"></span>
              </div>
              <div className="font-mono text-[11px] text-white/50 flex items-center gap-2">
                <Terminal className="w-3 h-3 text-indigo-400 animate-pulse" />
                <span>active_node_terminal.zsh</span>
              </div>
            </div>

            {/* Terminal Body */}
            <div className="p-5 font-mono text-xs space-y-4 h-80 overflow-y-auto bg-slate-950/45 text-white/90">
              <p className="text-white/40 italic">// Welcome to DEV_ARCHITECT cloud portal.</p>
              <p className="text-white/40 italic">// Type "help" to show available commands or explore systems metrics.</p>

              {terminalHistory.map((historyItem, idx) => (
                <div key={idx} className="space-y-1">
                  <p className="text-indigo-400 font-bold">&gt; {historyItem.cmd}</p>
                  {historyItem.output.map((outLine, oIdx) => (
                    <p key={oIdx} className="text-white/70 leading-relaxed whitespace-pre-wrap">{outLine}</p>
                  ))}
                </div>
              ))}

              {/* Input Form */}
              <form onSubmit={handleCommandSubmit} className="flex items-center gap-2 pt-2 border-t border-white/10">
                <span className="text-indigo-400 font-bold select-none">&gt;</span>
                <input
                  type="text"
                  value={terminalCommand}
                  onChange={(e) => setTerminalCommand(e.target.value)}
                  placeholder="Type system commands here..."
                  className="w-full bg-transparent border-none text-indigo-300 focus:outline-none placeholder:text-white/20 font-mono text-xs"
                />
              </form>
            </div>
          </div>
        </div>
      </section>

      {/* Bento Statistics Grid */}
      <section className="space-y-6">
        <div className="space-y-2">
          <h2 className="font-mono text-lg font-bold text-white flex items-center gap-2">
            <Cpu className="w-5 h-5 text-indigo-400" />
            <span>SYSTEMS_EFFICIENCY_METRICS</span>
          </h2>
          <p className="font-sans text-xs text-white/50">Live benchmarks compiled from cloud service providers</p>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {/* Stat 1 */}
          <div className="glass-card relative overflow-hidden p-6 flex flex-col justify-between hover:bg-white/15 hover:border-white/20 transition-all duration-300 rounded-2xl shadow-lg">
            <div className="absolute top-0 right-0 w-24 h-24 bg-indigo-500/10 rounded-full -mr-8 -mt-8"></div>
            <div className="flex justify-between items-start mb-4">
              <span className="font-mono text-xs text-white/60">Uptime SLA</span>
              <Database className="w-4 h-4 text-indigo-400" />
            </div>
            <div>
              <p className="font-mono text-3xl font-extrabold text-white">99.999%</p>
              <p className="font-sans text-xs text-white/40 mt-1">&lt; 5 minutes downtime/year</p>
            </div>
          </div>

          {/* Stat 2 */}
          <div className="glass-card relative overflow-hidden p-6 flex flex-col justify-between hover:bg-white/15 hover:border-white/20 transition-all duration-300 rounded-2xl shadow-lg">
            <div className="absolute top-0 right-0 w-24 h-24 bg-purple-500/10 rounded-full -mr-8 -mt-8"></div>
            <div className="flex justify-between items-start mb-4">
              <span className="font-mono text-xs text-white/60">Avg Latency</span>
              <Zap className="w-4 h-4 text-indigo-400 animate-pulse" />
            </div>
            <div>
              <p className="font-mono text-3xl font-extrabold text-white">&lt; 12.4ms</p>
              <p className="font-sans text-xs text-white/40 mt-1">Global edge routing median</p>
            </div>
          </div>

          {/* Stat 3 */}
          <div className="glass-card relative overflow-hidden p-6 flex flex-col justify-between hover:bg-white/15 hover:border-white/20 transition-all duration-300 rounded-2xl shadow-lg">
            <div className="absolute top-0 right-0 w-24 h-24 bg-pink-500/10 rounded-full -mr-8 -mt-8"></div>
            <div className="flex justify-between items-start mb-4">
              <span className="font-mono text-xs text-white/60">Active GKE Nodes</span>
              <Server className="w-4 h-4 text-indigo-400" />
            </div>
            <div>
              <p className="font-mono text-3xl font-extrabold text-white">240+</p>
              <p className="font-sans text-xs text-white/40 mt-1">Multi-cloud cluster points</p>
            </div>
          </div>

          {/* Stat 4 */}
          <div className="glass-card relative overflow-hidden p-6 flex flex-col justify-between hover:bg-white/15 hover:border-white/20 transition-all duration-300 rounded-2xl shadow-lg">
            <div className="absolute top-0 right-0 w-24 h-24 bg-teal-500/10 rounded-full -mr-8 -mt-8"></div>
            <div className="flex justify-between items-start mb-4">
              <span className="font-mono text-xs text-white/60">Security Rating</span>
              <Shield className="w-4 h-4 text-indigo-400" />
            </div>
            <div>
              <p className="font-mono text-3xl font-extrabold text-white">A+ Tier</p>
              <p className="font-sans text-xs text-white/40 mt-1">SOC-2 Type II standards</p>
            </div>
          </div>
        </div>
      </section>

      {/* Code-Editor Bios Component */}
      <section className="glass-card rounded-2xl overflow-hidden shadow-xl">
        {/* Editor Tabs */}
        <div className="bg-white/5 border-b border-white/10 flex items-center px-4 overflow-x-auto scrollbar-none">
          <button
            onClick={() => setActiveFileTab('bio')}
            className={`py-3.5 px-5 font-mono text-xs flex items-center gap-2 border-b-2 transition-all cursor-pointer ${
              activeFileTab === 'bio'
                ? 'border-indigo-400 text-white font-bold bg-white/5'
                : 'border-transparent text-white/50 hover:text-white'
            }`}
          >
            <FileText className="w-3.5 h-3.5 text-indigo-300" />
            <span>architecture_bio.md</span>
          </button>
          <button
            onClick={() => setActiveFileTab('stack')}
            className={`py-3.5 px-5 font-mono text-xs flex items-center gap-2 border-b-2 transition-all cursor-pointer ${
              activeFileTab === 'stack'
                ? 'border-indigo-400 text-white font-bold bg-white/5'
                : 'border-transparent text-white/50 hover:text-white'
            }`}
          >
            <Database className="w-3.5 h-3.5 text-indigo-300" />
            <span>technologies_stack.json</span>
          </button>
          <button
            onClick={() => setActiveFileTab('services')}
            className={`py-3.5 px-5 font-mono text-xs flex items-center gap-2 border-b-2 transition-all cursor-pointer ${
              activeFileTab === 'services'
                ? 'border-indigo-400 text-white font-bold bg-white/5'
                : 'border-transparent text-white/50 hover:text-white'
            }`}
          >
            <Server className="w-3.5 h-3.5" />
            <span>service_blueprints.yaml</span>
          </button>
        </div>

        {/* Content Viewer */}
        <div className="p-6 md:p-8 font-mono text-xs bg-slate-950/25 backdrop-blur-md leading-relaxed min-h-60 select-text">
          <AnimatePresence mode="wait">
            {activeFileTab === 'bio' && (
              <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                key="bio"
                className="space-y-4"
              >
                <p className="text-white/40"># CORE_SYSTEMS_ARCHITECT_STATEMENT</p>
                <p className="text-white/80 font-sans text-sm leading-relaxed max-w-3xl">
                  We believe that backend stability is not an afterthought—it is the foundation of product trust. Our team operates at the intersection of robust systems design and clean codebase execution. We audit legacy frameworks, build fault-tolerant topologies on AWS and GCP, and craft backend APIs in high-efficiency languages like Node.js, Go, and Rust.
                </p>
                <p className="text-white/40 mt-4">## PRIMARY_GOALS</p>
                <ul className="list-disc pl-5 font-sans text-sm text-white/70 space-y-2">
                  <li><strong>Zero Single Point of Failure (SPOF):</strong> Designing load balancers, database clustering, and cross-region fallbacks.</li>
                  <li><strong>Sub-Millisecond Engine Routing:</strong> Performance profiling, aggressive Redis caching, and optimized SQL index matrices.</li>
                  <li><strong>Systemic Visibility:</strong> Standardized log parsing, metrics aggregation dashboards, and automated failover telemetry alerts.</li>
                </ul>
              </motion.div>
            )}

            {activeFileTab === 'stack' && (
              <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                key="stack"
                className="space-y-2"
              >
                <p className="text-indigo-300">{`{`}</p>
                <div className="pl-6 space-y-1">
                  <p><span className="text-indigo-400">"cloud_infrastructure"</span>: <span className="text-emerald-300">["Amazon Web Services", "Google Cloud", "Cloudflare Edge"]</span>,</p>
                  <p><span className="text-indigo-400">"containerization_orchestration"</span>: <span className="text-emerald-300">["Kubernetes", "Docker Engine", "Helm"]</span>,</p>
                  <p><span className="text-indigo-400">"database_replication"</span>: <span className="text-emerald-300">["PostgreSQL (Active-Replica)", "Redis (Cluster)", "MongoDB"]</span>,</p>
                  <p><span className="text-indigo-400">"runtime_environments"</span>: <span className="text-emerald-300">["TypeScript (Node.js/Bun)", "Go", "Rust (WASM)"]</span>,</p>
                  <p><span className="text-indigo-400">"security_standards"</span>: <span className="text-emerald-300">["TLS 1.3", "RSA-4096 Encrypted Keys", "OIDC Single Sign-On"]</span></p>
                </div>
                <p className="text-indigo-300">{`}`}</p>
              </motion.div>
            )}

            {activeFileTab === 'services' && (
              <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                key="services"
                className="space-y-1"
              >
                <p className="text-white/40">---</p>
                <p><span className="text-indigo-400">blueprints_catalog</span>:</p>
                <div className="pl-4 space-y-3 mt-2">
                  <div>
                    <p><span className="text-purple-300">- service_id</span>: "audit_and_remediations"</p>
                    <p className="pl-4 text-white/70"><span className="text-white/40">focus</span>: "Deep profile optimization, database query audit, query optimization, security vulnerability scans."</p>
                  </div>
                  <div>
                    <p><span className="text-purple-300">- service_id</span>: "cloud_replatforming"</p>
                    <p className="pl-4 text-white/70"><span className="text-white/40">focus</span>: "Kubernetes orchestration setup, infrastructure as code templates (Terraform), secure cloud migrations."</p>
                  </div>
                  <div>
                    <p><span className="text-purple-300">- service_id</span>: "performance_contracting"</p>
                    <p className="pl-4 text-white/70"><span className="text-white/40">focus</span>: "Scale architectures to support 50k+ active concurrent connections without lag or load failures."</p>
                  </div>
                </div>
              </motion.div>
            )}
          </AnimatePresence>
        </div>
      </section>

      {/* Live System Diagnostics Self-Test */}
      <section className="glass-card p-6 rounded-2xl flex flex-col md:flex-row justify-between items-center gap-6 shadow-xl relative overflow-hidden">
        <div className="absolute top-0 left-0 w-32 h-32 bg-indigo-500/5 rounded-full -ml-12 -mt-12"></div>
        <div className="flex items-start gap-4 relative z-10">
          <div className="p-3 bg-white/5 border border-white/10 rounded-lg">
            <Cpu className="w-6 h-6 text-indigo-400 animate-pulse" />
          </div>
          <div className="space-y-1">
            <h4 className="font-mono text-sm font-bold text-white">SYS_DIAGNOSTICS: LIVE CHECKUP</h4>
            <p className="font-sans text-xs text-white/50">Verify route responsiveness and security cache status in real-time.</p>
          </div>
        </div>

        <div className="flex items-center gap-4 relative z-10">
          {isRunningScript ? (
            <span className="font-mono text-xs text-indigo-400 flex items-center gap-2 animate-pulse">
              <span className="w-2 h-2 bg-indigo-400 rounded-full animate-ping"></span>
              <span>TEST_RUNNING...</span>
            </span>
          ) : testRunSuccess === true ? (
            <span className="font-mono text-xs text-emerald-400 flex items-center gap-1.5 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1.5 rounded-lg">
              <Check className="w-3.5 h-3.5" />
              <span>DIAGNOSTICS_OK (0ms lag)</span>
            </span>
          ) : (
            <span className="font-mono text-xs text-white/40">Ready to run self test</span>
          )}

          <button
            onClick={runSystemSelfTest}
            disabled={isRunningScript}
            className="flex items-center gap-2 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 font-mono text-xs px-4 py-2.5 rounded-lg transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <Play className="w-3.5 h-3.5 text-indigo-300" />
            <span>RUN_SELF_TEST</span>
          </button>
        </div>
      </section>
    </div>
  );
}
