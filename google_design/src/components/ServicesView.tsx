import React, { useState } from 'react';
import { motion } from 'motion/react';
import { Server, Shield, Zap, Database, Cpu, HelpCircle, Check, ArrowRight, DollarSign } from 'lucide-react';
import { Service } from '../types';

export default function ServicesView() {
  const [selectedServices, setSelectedServices] = useState<string[]>(['arch']);
  const [trafficScale, setTrafficScale] = useState<number>(100000); // req/day
  const [needsMultiRegion, setNeedsMultiRegion] = useState<boolean>(false);
  const [needsHighSecurity, setNeedsHighSecurity] = useState<boolean>(false);

  const servicesList: Service[] = [
    {
      id: 'arch',
      name: 'Cloud Topology & Orchestration',
      icon: 'server',
      tagline: 'Scale-to-Infinity Architecture',
      description: 'Designing Kubernetes, AWS, and GCP infrastructures utilizing Infrastructure as Code (Terraform) with high-availability clustering and robust mesh configurations.',
      basePrice: 2500,
      deliveryTime: '2-3 Weeks',
      features: [
        'Complete Terraform configuration plans',
        'Multi-region load balancing setup',
        'Prometheus & Grafana dashboard telemetry',
        'Fault tolerance audits'
      ]
    },
    {
      id: 'audit',
      name: 'Deep System & Database Audit',
      icon: 'shield',
      tagline: 'Security & Latency Remediations',
      description: 'Profiling existing applications to pinpoint database query deadlocks, API bottle-necks, and potential SOC-2/HIPAA security compliance loopholes.',
      basePrice: 1500,
      deliveryTime: '1-2 Weeks',
      features: [
        'Query optimization profiles',
        'Memory leak & heap dump scans',
        'Automated vulnerability diagnostics',
        'Comprehensive remediation report'
      ]
    },
    {
      id: 'perf',
      name: 'High-Concurrency Development',
      icon: 'zap',
      tagline: 'High-Performance API Engines',
      description: 'Writing custom edge proxies, state replication drivers, and high-speed API routes in optimized TypeScript/Node.js, Go, or compiled Rust WASM.',
      basePrice: 3500,
      deliveryTime: '3-4 Weeks',
      features: [
        'Sub-15ms edge API routes',
        'Redis cluster configuration and caching',
        'gRPC communication buffers',
        'Fully load-tested mock benchmark endpoints'
      ]
    }
  ];

  const handleToggleService = (id: string) => {
    setSelectedServices(prev => 
      prev.includes(id) 
        ? prev.filter(item => item !== id) 
        : [...prev, id]
    );
  };

  // Live budget calculation based on selections
  const computeEstimatedBudget = () => {
    let total = 0;
    
    // Add base prices of selected services
    servicesList.forEach(service => {
      if (selectedServices.includes(service.id)) {
        total += service.basePrice;
      }
    });

    // Add traffic scale modifiers
    if (trafficScale > 500000) {
      total += 1200; // High traffic optimization cost
    } else if (trafficScale > 100000) {
      total += 500;
    }

    // Add custom addons
    if (needsMultiRegion) total += 1500;
    if (needsHighSecurity) total += 1000;

    return total;
  };

  const getIcon = (type: string) => {
    switch (type) {
      case 'server':
        return <Server className="w-5 h-5 text-indigo-400" />;
      case 'shield':
        return <Shield className="w-5 h-5 text-indigo-400" />;
      case 'zap':
        return <Zap className="w-5 h-5 text-indigo-400" />;
      default:
        return <Cpu className="w-5 h-5 text-indigo-400" />;
    }
  };

  return (
    <motion.div 
      initial={{ opacity: 0, y: 15 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -15 }}
      transition={{ duration: 0.4 }}
      className="space-y-12"
    >
      {/* Header section */}
      <div className="space-y-3">
        <h1 className="font-sans text-3xl font-bold text-white flex items-center gap-3">
          <Cpu className="w-6 h-6 text-indigo-400" />
          <span>BLUEPRINT_CAPABILITIES</span>
        </h1>
        <p className="font-sans text-white/70 max-w-xl text-sm leading-relaxed">
          Premium systems engineering and technical advisory contracts designed for enterprise performance standards.
        </p>
      </div>

      {/* Services List cards */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {servicesList.map((service) => {
          const isSelected = selectedServices.includes(service.id);
          return (
            <div 
              key={service.id}
              onClick={() => handleToggleService(service.id)}
              className={`p-6 rounded-2xl border transition-all duration-300 flex flex-col justify-between cursor-pointer group ${
                isSelected 
                  ? 'bg-indigo-500/10 border-indigo-400 shadow-lg shadow-indigo-500/10 backdrop-blur-md' 
                  : 'glass-card border-white/10 hover:border-indigo-400/40 hover:bg-white/10'
              }`}
            >
              <div className="space-y-4">
                <div className="flex justify-between items-start">
                  <div className="p-2.5 bg-white/5 border border-white/10 rounded-xl">
                    {getIcon(service.icon)}
                  </div>
                  <div className={`w-5 h-5 rounded border flex items-center justify-center transition-all ${
                    isSelected ? 'bg-indigo-500 border-indigo-400' : 'border-white/20'
                  }`}>
                    {isSelected && <Check className="w-3.5 h-3.5 text-white font-bold" />}
                  </div>
                </div>

                <div className="space-y-2">
                  <span className="font-mono text-[9px] text-indigo-300 block tracking-widest uppercase">{service.tagline}</span>
                  <h3 className="font-sans text-base font-bold text-white group-hover:text-indigo-300 transition-colors">
                    {service.name}
                  </h3>
                  <p className="font-sans text-xs text-white/60 leading-relaxed">
                    {service.description}
                  </p>
                </div>

                {/* Features checklists */}
                <div className="pt-4 border-t border-white/10 space-y-2">
                  {service.features.map((feat) => (
                    <div key={feat} className="flex items-center gap-2 font-sans text-xs text-white/70">
                      <Check className="w-3.5 h-3.5 text-indigo-400 flex-shrink-0" />
                      <span>{feat}</span>
                    </div>
                  ))}
                </div>
              </div>

              <div className="pt-6 mt-6 border-t border-white/10 flex justify-between items-center font-mono">
                <div>
                  <span className="text-[9px] text-white/40 block uppercase">Est. Delivery</span>
                  <span className="text-xs text-white/80">{service.deliveryTime}</span>
                </div>
                <div className="text-right">
                  <span className="text-[9px] text-white/40 block uppercase">From</span>
                  <span className="text-sm font-bold text-indigo-400">${service.basePrice}</span>
                </div>
              </div>
            </div>
          );
        })}
      </div>

      {/* Interactive Planner Component */}
      <section className="glass-card-heavy border border-white/15 rounded-2xl overflow-hidden p-6 md:p-10 space-y-8 shadow-2xl">
        <div className="space-y-2 border-b border-white/10 pb-4">
          <h2 className="font-sans text-xl font-bold text-white flex items-center gap-2">
            <DollarSign className="w-5 h-5 text-indigo-400 animate-pulse" />
            <span>INTERACTIVE_SYSTEMS_PLANNER</span>
          </h2>
          <p className="font-sans text-xs text-white/50">Customize your resource constraints and compute an estimated budget live.</p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          {/* Options side */}
          <div className="lg:col-span-7 space-y-6">
            {/* Range slider for daily traffic */}
            <div className="space-y-3">
              <div className="flex justify-between font-mono text-xs">
                <span className="text-white/60">EXPECTED_TRAFFIC_VOLUME:</span>
                <span className="text-indigo-300 font-bold">{trafficScale.toLocaleString()} req/day</span>
              </div>
              <input 
                type="range" 
                min={10000} 
                max={1000000} 
                step={10000}
                value={trafficScale}
                onChange={(e) => setTrafficScale(Number(e.target.value))}
                className="w-full accent-indigo-400 bg-slate-950/40 border border-white/10 h-2 rounded-lg cursor-pointer"
              />
              <div className="flex justify-between font-mono text-[9px] text-white/40 select-none">
                <span>10K REQ (LOW)</span>
                <span>500K REQ (MEDIUM)</span>
                <span>1M REQ (HIGH SCALE)</span>
              </div>
            </div>

            {/* Custom checkboxes addons */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              {/* Addon 1 */}
              <div 
                onClick={() => setNeedsMultiRegion(!needsMultiRegion)}
                className={`p-4 border rounded-xl cursor-pointer transition-all flex items-center justify-between ${
                  needsMultiRegion 
                    ? 'bg-indigo-500/10 border-indigo-400' 
                    : 'bg-slate-950/20 border-white/10 hover:border-white/20'
                }`}
              >
                <div className="space-y-1">
                  <span className="font-sans text-xs font-bold text-white block">Multi-Region Router</span>
                  <span className="font-sans text-[11px] text-white/55 block">Cross-region fallback (+$1,500)</span>
                </div>
                <div className={`w-4 h-4 rounded border flex items-center justify-center transition-all ${
                  needsMultiRegion ? 'bg-indigo-500 border-indigo-400' : 'border-white/20'
                }`}>
                  {needsMultiRegion && <Check className="w-3 h-3 text-white font-bold" />}
                </div>
              </div>

              {/* Addon 2 */}
              <div 
                onClick={() => setNeedsHighSecurity(!needsHighSecurity)}
                className={`p-4 border rounded-xl cursor-pointer transition-all flex items-center justify-between ${
                  needsHighSecurity 
                    ? 'bg-indigo-500/10 border-indigo-400' 
                    : 'bg-slate-950/20 border-white/10 hover:border-white/20'
                }`}
              >
                <div className="space-y-1">
                  <span className="font-sans text-xs font-bold text-white block">SOC-2 Security Pack</span>
                  <span className="font-sans text-[11px] text-white/55 block">OIDC Auth & audits (+$1,000)</span>
                </div>
                <div className={`w-4 h-4 rounded border flex items-center justify-center transition-all ${
                  needsHighSecurity ? 'bg-indigo-500 border-indigo-400' : 'border-white/20'
                }`}>
                  {needsHighSecurity && <Check className="w-3 h-3 text-white font-bold" />}
                </div>
              </div>
            </div>
          </div>

          {/* Budget output side */}
          <div className="lg:col-span-5 bg-slate-950/40 border border-white/15 rounded-2xl p-6 space-y-6 relative overflow-hidden backdrop-blur-md shadow-inner">
            {/* Terminal styling decorative details */}
            <div className="absolute top-3 right-3 flex items-center gap-1.5 bg-indigo-500/10 border border-indigo-500/20 px-2 py-0.5 rounded-md text-[9px] text-indigo-400 font-mono">
              <span>SYS_ESTIMATE</span>
            </div>

            <div className="space-y-1">
              <span className="font-mono text-[10px] text-white/40 uppercase block tracking-wider">Estimated Project Budget</span>
              <p className="font-mono text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 to-white flex items-baseline">
                ${computeEstimatedBudget().toLocaleString()}
                <span className="text-xs text-white/50 font-normal tracking-normal font-sans ml-1">USD (Contract Fee)</span>
              </p>
            </div>

            {/* Price breakdown logs */}
            <div className="p-4 bg-slate-950/40 border border-white/10 rounded-xl font-mono text-[11px] text-white/70 space-y-2">
              <p className="text-white/40 italic select-none">// Active Cost Logs:</p>
              {servicesList.map(service => {
                if (selectedServices.includes(service.id)) {
                  return (
                    <p key={service.id} className="flex justify-between text-emerald-400">
                      <span>+ {service.name.substring(0, 18)}...</span>
                      <span>${service.basePrice}</span>
                    </p>
                  );
                }
                return null;
              })}
              {trafficScale > 100000 && (
                <p className="flex justify-between text-indigo-200">
                  <span>+ High-Scale Ingestion Config</span>
                  <span>{trafficScale > 500000 ? '$1,200' : '$500'}</span>
                </p>
              )}
              {needsMultiRegion && (
                <p className="flex justify-between text-indigo-200">
                  <span>+ Multi-Region Active Fallback</span>
                  <span>$1,500</span>
                </p>
              )}
              {needsHighSecurity && (
                <p className="flex justify-between text-indigo-200">
                  <span>+ Security Hardening Suite</span>
                  <span>$1,000</span>
                </p>
              )}
              {selectedServices.length === 0 && (
                <p className="text-red-400 italic">// Choose at least one core service blueprint.</p>
              )}
            </div>

            <div className="space-y-2">
              <p className="font-sans text-[11px] text-white/45 italic">
                * Estimates are calculated on average system size parameters. Standard SLAs, cloud costs, and hosting credits are handled separately.
              </p>
            </div>
          </div>
        </div>
      </section>
    </motion.div>
  );
}
