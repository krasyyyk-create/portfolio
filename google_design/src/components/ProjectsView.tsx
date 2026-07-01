import React, { useState } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { Project } from '../types';
import { Layers, Database, Shield, Zap, Server, ChevronRight, Globe, Github, Info, Cpu } from 'lucide-react';

export default function ProjectsView() {
  const [filter, setFilter] = useState<string>('all');
  const [selectedProject, setSelectedProject] = useState<Project | null>(null);

  const projectsList: Project[] = [
    {
      id: 'cluster-prod',
      title: 'High-Availability EKS Cluster',
      category: 'infrastructure',
      description: 'Designed and implemented a multi-region active-active Kubernetes infrastructure running on AWS, backed by Istio service mesh and fully automated using Terraform IaC.',
      techStack: ['AWS EKS', 'Kubernetes', 'Istio Mesh', 'Terraform', 'Linkerd'],
      metrics: {
        uptime: '99.999%',
        scale: '50-120 Auto Nodes',
        throughput: '150,000 req/sec'
      },
      details: [
        'Eliminated all single points of failure with cross-region route configurations.',
        'Structured automated cluster autoscaling using custom metrics via Prometheus adapters.',
        'Hardened network safety profiles using strict Calico policies and namespace segmentations.'
      ],
      visualType: 'kube'
    },
    {
      id: 'db-sync',
      title: 'Cross-Cloud Database Replication',
      category: 'database',
      description: 'Architected a multi-cloud database pipeline bridging GCP Cloud SQL and a secondary AWS RDS Postgres cluster with near-zero replication lag for disaster recovery.',
      techStack: ['PostgreSQL', 'GCP Cloud SQL', 'AWS RDS', 'pgPool-II', 'CDC Pipelines'],
      metrics: {
        latency: '< 2.4ms lag',
        scale: '3 Terabytes',
        throughput: '24,000 read-ops'
      },
      details: [
        'Developed custom pgPool-II middleware to route read-heavy transactions safely.',
        'Established automatic failover routines resolving connectivity drops in less than 4 seconds.',
        'Constructed schema migration automation leveraging robust Drizzle / Prisma sync protocols.'
      ],
      visualType: 'db'
    },
    {
      id: 'cdn-routing',
      title: 'Global Edge Accelerator',
      category: 'api-engine',
      description: 'Implemented a globally distributed edge caching proxy utilizing Cloudflare Workers to intercept, authenticate, and sanitize inbound REST/GraphQL requests.',
      techStack: ['Cloudflare Workers', 'Rust / WASM', 'Redis Edge', 'TLS 1.3', 'GraphQL'],
      metrics: {
        latency: '8.2ms avg',
        throughput: '1.4M peak-req',
        uptime: '100% Core edge'
      },
      details: [
        'Wrote custom edge proxy filters compiled in high-performance Rust WASM.',
        'Built geographically routed Redis cache buffers minimizing roundtrip db trips.',
        'Integrated live DDoS mitigation rules blocking active threat payloads in milliseconds.'
      ],
      visualType: 'cdn'
    },
    {
      id: 'pipeline-data',
      title: 'Real-Time Streaming Bus',
      category: 'infrastructure',
      description: 'Constructed an event-driven data streaming mesh using Apache Kafka and Apache Flink to process log aggregates and generate real-time metrics telemetry.',
      techStack: ['Apache Kafka', 'Apache Flink', 'Go / Golang', 'gRPC', 'Prometheus'],
      metrics: {
        throughput: '12 GB/min',
        latency: '15ms processing',
        scale: '24 Cluster Partitions'
      },
      details: [
        'Assembled highly optimized Golang partition workers to balance stream processing.',
        'Programmed real-time anomalies logging and telemetry alerts directly to Grafana dashboard.',
        'Reduced partition imbalance by implementing high-trust MurmurHash routing.'
      ],
      visualType: 'pipeline'
    }
  ];

  const filteredProjects = filter === 'all' 
    ? projectsList 
    : projectsList.filter(p => p.category === filter);

  return (
    <motion.div 
      initial={{ opacity: 0, y: 15 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -15 }}
      transition={{ duration: 0.4 }}
      className="space-y-10"
    >
      {/* Page Header */}
      <div className="space-y-3">
        <h1 className="font-sans text-3xl font-bold text-white flex items-center gap-2.5">
          <Layers className="w-6 h-6 text-indigo-400" />
          <span>PORTFOLIO_SCHEMATICS</span>
        </h1>
        <p className="font-sans text-white/70 max-w-xl text-sm leading-relaxed">
          Deep-dives into production architectures we have successfully designed, validated, and currently support across major cloud service providers.
        </p>
      </div>

      {/* Filter Tabs */}
      <div className="flex border-b border-white/10 pb-2 overflow-x-auto scrollbar-none gap-2">
        {(['all', 'infrastructure', 'database', 'api-engine'] as const).map((cat) => (
          <button
            key={cat}
            onClick={() => setFilter(cat)}
            className={`px-4 py-2 font-mono text-xs capitalize transition-all rounded-lg cursor-pointer ${
              filter === cat
                ? 'bg-white/10 border border-white/25 text-white font-bold backdrop-blur-md'
                : 'border border-white/5 bg-white/5 text-white/50 hover:text-white backdrop-blur-sm'
            }`}
          >
            {cat.replace('-', ' ')}
          </button>
        ))}
      </div>

      {/* Projects Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
        {filteredProjects.map((project) => (
          <div 
            key={project.id}
            className="glass-card hover:bg-white/15 hover:border-indigo-400/30 rounded-2xl overflow-hidden flex flex-col justify-between transition-all duration-300 group hover:-translate-y-1 relative shadow-xl"
          >
            {/* Header controls decorative */}
            <div className="bg-white/5 p-3.5 flex justify-between items-center border-b border-white/10 select-none">
              <div className="flex gap-1.5">
                <span className="w-2.5 h-2.5 rounded-full bg-white/20"></span>
                <span className="w-2.5 h-2.5 rounded-full bg-white/20"></span>
                <span className="w-2.5 h-2.5 rounded-full bg-white/20"></span>
              </div>
              <span className="font-mono text-[10px] text-white/40">{project.id}.yaml</span>
            </div>

            {/* Content body */}
            <div className="p-6 space-y-6 flex-grow">
              <div className="space-y-2">
                <div className="flex justify-between items-start gap-4">
                  <h3 className="font-sans text-lg font-bold text-white group-hover:text-indigo-400 transition-colors">
                    {project.title}
                  </h3>
                  <span className="font-mono text-[10px] text-purple-300 bg-purple-500/10 border border-purple-500/20 px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                    {project.category}
                  </span>
                </div>
                <p className="font-sans text-sm text-white/70 leading-relaxed">
                  {project.description}
                </p>
              </div>

              {/* Stats benchmarks */}
              <div className="grid grid-cols-3 gap-2 py-3 border-y border-white/10 text-center bg-slate-950/25 rounded-lg p-2.5">
                {Object.entries(project.metrics).map(([key, val]) => (
                  <div key={key} className="space-y-1 border-r last:border-r-0 border-white/5">
                    <span className="font-mono text-[9px] text-white/40 uppercase block tracking-wider">{key}</span>
                    <span className="font-mono text-xs font-bold text-indigo-400">{val}</span>
                  </div>
                ))}
              </div>

              {/* Technologies chip lists */}
              <div className="flex flex-wrap gap-1.5">
                {project.techStack.map((tech) => (
                  <span 
                    key={tech} 
                    className="font-mono text-[10px] text-white/80 bg-white/5 px-2.5 py-1 rounded-md border border-white/10"
                  >
                    {tech}
                  </span>
                ))}
              </div>
            </div>

            {/* Action panel footer */}
            <div className="p-4 bg-white/5 border-t border-white/10 flex justify-between items-center">
              <button 
                onClick={() => setSelectedProject(project)}
                className="font-mono text-xs text-indigo-300 hover:text-white flex items-center gap-1 cursor-pointer"
              >
                <span>&gt; Inspect Architecture</span>
                <ChevronRight className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
              </button>

              <div className="flex gap-3">
                <a href="#github" className="text-white/40 hover:text-white transition-colors">
                  <Github className="w-4 h-4" />
                </a>
                <a href="#globe" className="text-white/40 hover:text-white transition-colors">
                  <Globe className="w-4 h-4" />
                </a>
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Inspection Modal */}
      <AnimatePresence>
        {selectedProject && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
            {/* Backdrop */}
            <motion.div 
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              onClick={() => setSelectedProject(null)}
              className="absolute inset-0 bg-slate-950/65 backdrop-blur-md"
            />

            {/* Modal Box */}
            <motion.div 
              initial={{ opacity: 0, scale: 0.95, y: 10 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.95, y: 10 }}
              className="relative w-full max-w-2xl glass-card-heavy rounded-2xl overflow-hidden shadow-2xl flex flex-col max-h-[85vh] border border-white/15"
            >
              {/* Window Header */}
              <div className="bg-white/5 p-4 flex justify-between items-center border-b border-white/10 select-none">
                <div className="flex gap-1.5">
                  <button 
                    onClick={() => setSelectedProject(null)} 
                    className="w-3 h-3 rounded-full bg-red-500/60 cursor-pointer hover:bg-red-500 transition-colors"
                    title="Close"
                  ></button>
                  <span className="w-3 h-3 rounded-full bg-yellow-500/30"></span>
                  <span className="w-3 h-3 rounded-full bg-green-500/30"></span>
                </div>
                <div className="font-mono text-xs text-white/50 flex items-center gap-1.5">
                  <Info className="w-3.5 h-3.5 text-indigo-400" />
                  <span>inspect_{selectedProject.id}.sh</span>
                </div>
              </div>

              {/* Modal Body */}
              <div className="p-6 md:p-8 overflow-y-auto space-y-6">
                <div>
                  <span className="font-mono text-[10px] text-indigo-400 uppercase tracking-widest block font-bold mb-1">Architecture Inspect</span>
                  <h2 className="font-sans text-2xl font-bold text-white">{selectedProject.title}</h2>
                  <p className="font-sans text-sm text-white/70 mt-2 leading-relaxed">{selectedProject.description}</p>
                </div>

                {/* Simulated Diagram Block */}
                <div className="p-5 bg-slate-950/30 border border-white/10 rounded-xl font-mono text-xs space-y-4 relative overflow-hidden">
                  <div className="absolute top-2 right-2 flex items-center gap-1 bg-indigo-500/10 px-2 py-0.5 border border-indigo-500/20 rounded-md text-[9px] text-indigo-400">
                    <span className="w-1.5 h-1.5 bg-indigo-400 rounded-full animate-ping"></span>
                    <span>LIVE_FLOW</span>
                  </div>
                  <p className="text-white/40 italic">// Conceptual Node Topology Graph</p>
                  
                  {selectedProject.visualType === 'kube' && (
                    <div className="space-y-2 py-2 text-center text-white/80">
                      <div className="p-2 border border-indigo-400/30 bg-indigo-500/10 rounded-lg inline-block">CDN GATEWAY</div>
                      <div className="text-indigo-400/60">&darr; Routing Protocol</div>
                      <div className="flex justify-center gap-4">
                        <div className="p-2 border border-[#a5b4fc]/30 bg-[#a5b4fc]/5 rounded-lg">Ingress Controller (Istio)</div>
                      </div>
                      <div className="text-indigo-400/60">&darr; Pod Load Balance</div>
                      <div className="flex justify-center gap-2">
                        <div className="p-1.5 border border-emerald-500/30 bg-emerald-500/5 rounded-lg text-[10px]">App Pod A</div>
                        <div className="p-1.5 border border-emerald-500/30 bg-emerald-500/5 rounded-lg text-[10px]">App Pod B</div>
                        <div className="p-1.5 border border-emerald-500/30 bg-emerald-500/5 rounded-lg text-[10px]">App Pod C</div>
                      </div>
                    </div>
                  )}

                  {selectedProject.visualType === 'db' && (
                    <div className="space-y-2 py-2 text-center text-white/80">
                      <div className="p-2 border border-indigo-400/30 bg-indigo-500/10 rounded-lg inline-block">CLIENT APPLICATION</div>
                      <div className="text-indigo-400/60">&darr; Read / Write Router</div>
                      <div className="flex justify-center gap-6">
                        <div className="p-2 border border-emerald-500/30 bg-emerald-500/5 rounded-lg text-[10px]">Primary DB (AWS Postgres)<br/><span className="text-emerald-400 font-bold">Write Node</span></div>
                        <div className="p-2 border border-purple-500/30 bg-purple-500/5 rounded-lg text-[10px]">Replica DB (GCP SQL)<br/><span className="text-purple-300 font-bold">Read / DR Pool</span></div>
                      </div>
                      <div className="text-white/40 italic text-[10px]">// Sync Replication Lag &lt; 2.4ms</div>
                    </div>
                  )}

                  {selectedProject.visualType === 'cdn' && (
                    <div className="space-y-2 py-2 text-center text-white/80">
                      <div className="p-2 border border-[#a5b4fc]/30 bg-[#a5b4fc]/5 rounded-lg inline-block">GLOBAL EDGE CLIENTS</div>
                      <div className="text-indigo-400/60">&darr; Geographic DNS Routing</div>
                      <div className="flex justify-center gap-4">
                        <div className="p-2 border border-indigo-400/30 bg-indigo-500/10 rounded-lg text-[10px]">Edge Worker (US-East)</div>
                        <div className="p-2 border border-indigo-400/30 bg-indigo-500/10 rounded-lg text-[10px]">Edge Worker (EU-West)</div>
                      </div>
                      <div className="text-indigo-400/60">&darr; Fast-Path Redis Cache Lookup</div>
                      <div className="p-1.5 border border-emerald-500/30 bg-emerald-500/5 rounded-lg inline-block text-[10px]">Encrypted Cache Match</div>
                    </div>
                  )}

                  {selectedProject.visualType === 'pipeline' && (
                    <div className="space-y-2 py-2 text-center text-white/80">
                      <div className="p-2 border border-indigo-400/30 bg-indigo-500/10 rounded-lg inline-block">TELEMETRY INGESTION</div>
                      <div className="text-indigo-400/60">&darr; Streaming Bus</div>
                      <div className="p-2 border border-purple-500/30 bg-purple-500/5 rounded-lg inline-block text-[10px]">Apache Kafka Queue Cluster</div>
                      <div className="text-indigo-400/60">&darr; Aggregation Workers</div>
                      <div className="flex justify-center gap-4">
                        <div className="p-1.5 border border-emerald-500/30 bg-emerald-500/5 rounded-lg text-[10px]">Worker Pod 01</div>
                        <div className="p-1.5 border border-emerald-500/30 bg-emerald-500/5 rounded-lg text-[10px]">Worker Pod 02</div>
                      </div>
                    </div>
                  )}
                </div>

                {/* Specs Details */}
                <div className="space-y-3">
                  <h4 className="font-sans text-sm font-bold text-white">SPECIFICATIONS & BENCHMARKS:</h4>
                  <ul className="space-y-2">
                    {selectedProject.details.map((detail, idx) => (
                      <li key={idx} className="font-sans text-sm text-white/70 flex items-start gap-2.5">
                        <span className="font-mono text-indigo-400 text-xs mt-0.5 select-none">&gt;</span>
                        <span>{detail}</span>
                      </li>
                    ))}
                  </ul>
                </div>
              </div>

              {/* Modal Footer */}
              <div className="p-4 bg-white/5 border-t border-white/10 flex justify-between items-center">
                <span className="font-mono text-xs text-white/40">Exit Code: 0 (No Errors)</span>
                <button 
                  onClick={() => setSelectedProject(null)}
                  className="px-5 py-2.5 bg-indigo-500/80 hover:bg-indigo-500 border border-white/10 text-white font-semibold font-sans text-xs rounded-lg active:scale-95 cursor-pointer transition-colors shadow-lg shadow-indigo-500/20"
                >
                  Close Inspection
                </button>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </motion.div>
  );
}
